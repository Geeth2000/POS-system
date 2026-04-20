<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use App\Repositories\ProductRepository;
use App\Repositories\TransactionRepository;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SalesService
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly TransactionRepository $transactionRepository,
    ) {
    }

    public function createSale(User $cashier, array $payload): Transaction
    {
        return DB::transaction(function () use ($cashier, $payload) {
            $productIds = collect($payload['items'])
                ->pluck('product_id')
                ->unique()
                ->values()
                ->all();

            $products = $this->productRepository
                ->findForUpdateByIds($productIds)
                ->keyBy('id');

            if ($products->count() !== count($productIds)) {
                throw ValidationException::withMessages([
                    'items' => ['One or more selected products were not found.'],
                ]);
            }

            $subtotal = 0;

            foreach ($payload['items'] as $item) {
                $product = $products->get($item['product_id']);
                $availableQty = $product->stock_qty ?? $product->quantity;

                if (!$product->is_active) {
                    throw ValidationException::withMessages([
                        'items' => ["Product {$product->name} is inactive."],
                    ]);
                }

                if ($availableQty < $item['quantity']) {
                    throw ValidationException::withMessages([
                        'items' => ["Insufficient stock for {$product->name}."],
                    ]);
                }

                $subtotal += ((float) $product->price) * $item['quantity'];
            }

            $taxAmount = (float) ($payload['tax_amount'] ?? 0);
            $discountAmount = (float) ($payload['discount_amount'] ?? 0);
            $totalAmount = $subtotal + $taxAmount - $discountAmount;

            if ($totalAmount < 0) {
                throw ValidationException::withMessages([
                    'discount_amount' => ['Discount cannot make the total amount negative.'],
                ]);
            }

            $transaction = $this->transactionRepository->createTransaction([
                'transaction_number' => 'TMP-' . Str::uuid(),
                'customer_id' => $payload['customer_id'] ?? null,
                'cashier_id' => $cashier->id,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'payment_method' => $payload['payment_method'],
                'notes' => $payload['notes'] ?? null,
            ]);

            $transaction->transaction_number = sprintf(
                'TRX-%s-%06d',
                now()->format('Ymd'),
                $transaction->id
            );
            $transaction->save();

            foreach ($payload['items'] as $item) {
                $product = $products->get($item['product_id']);
                $lineTotal = ((float) $product->price) * $item['quantity'];

                $this->transactionRepository->createTransactionItem([
                    'transaction_id' => $transaction->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                    'line_total' => $lineTotal,
                ]);

                $newStock = ($product->stock_qty ?? $product->quantity) - $item['quantity'];
                $product->stock_qty = $newStock;
                $product->quantity = $newStock;
                $product->save();
            }

            return $transaction->load(['customer', 'cashier', 'items.product']);
        });
    }
}
