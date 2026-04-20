<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\User;
use App\Repositories\ProductRepository;
use App\Repositories\SaleRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleService
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly SaleRepository $saleRepository,
    ) {
    }

    public function createSale(User $user, array $payload): Sale
    {
        return DB::transaction(function () use ($user, $payload) {
            $itemsByProduct = collect($payload['items'])
                ->groupBy('product_id')
                ->map(fn ($items) => [
                    'product_id' => (int) $items->first()['product_id'],
                    'quantity' => (int) $items->sum('quantity'),
                ])
                ->values();

            $productIds = $itemsByProduct
                ->pluck('product_id')
                ->unique()
                ->values()
                ->all();

            $products = $this->productRepository
                ->findForUpdateByIds($productIds)
                ->keyBy('id');

            if ($products->count() !== count($productIds)) {
                throw ValidationException::withMessages([
                    'items' => ['One or more products were not found.'],
                ]);
            }

            $totalAmount = 0;

            foreach ($itemsByProduct as $item) {
                $product = $products->get($item['product_id']);
                $availableStock = $product->stock_qty;

                if (!$product->is_active) {
                    throw ValidationException::withMessages([
                        'items' => ["Product {$product->name} is inactive."],
                    ]);
                }

                if ($availableStock < $item['quantity']) {
                    throw ValidationException::withMessages([
                        'items' => ["Insufficient stock for {$product->name}."],
                    ]);
                }

                $totalAmount += ((float) $product->price) * $item['quantity'];
            }

            $sale = $this->saleRepository->createSale([
                'user_id' => $user->id,
                'total_amount' => $totalAmount,
                'payment_method' => $payload['payment_method'],
            ]);

            foreach ($itemsByProduct as $item) {
                $product = $products->get($item['product_id']);

                $this->saleRepository->createSaleItem([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                ]);

                if (!$this->productRepository->decrementStockAtomically($product, $item['quantity'])) {
                    throw ValidationException::withMessages([
                        'items' => ["Unable to update stock for {$product->name}."],
                    ]);
                }
            }

            return $sale->load(['user', 'items.product']);
        });
    }
}
