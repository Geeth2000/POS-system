<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\CartRepository;
use App\Repositories\ProductRepository;
use App\Repositories\SaleRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BillingService
{
    public function __construct(
        private readonly CartRepository $cartRepository,
        private readonly ProductRepository $productRepository,
        private readonly SaleRepository $saleRepository,
    ) {
    }

    public function addItemToCart(User $user, array $payload): array
    {
        $product = $this->productRepository->baseQuery()
            ->whereKey($payload['product_id'])
            ->where('is_active', true)
            ->first();

        if (!$product) {
            throw ValidationException::withMessages([
                'product_id' => ['The selected product is not active or does not exist.'],
            ]);
        }

        $cart = $this->cartRepository->getOrCreateActiveCart($user->id);
        $this->cartRepository->upsertCartItem(
            $cart,
            (int) $payload['product_id'],
            (int) $payload['quantity'],
            (string) $product->price
        );

        return $this->getCartSummary($user);
    }

    public function getCartSummary(User $user): array
    {
        $cart = $this->cartRepository->getActiveCartForUserWithItems($user->id);

        if (!$cart) {
            return [
                'cart_id' => null,
                'items' => [],
                'total_amount' => 0,
            ];
        }

        $items = $cart->items->map(function ($item) {
            $lineTotal = ((float) $item->price) * $item->quantity;

            return [
                'product_id' => $item->product_id,
                'name' => $item->product?->name,
                'quantity' => $item->quantity,
                'price' => $item->price,
                'line_total' => round($lineTotal, 2),
            ];
        })->values();

        return [
            'cart_id' => $cart->id,
            'items' => $items,
            'total_amount' => round($items->sum('line_total'), 2),
        ];
    }

    public function checkout(User $user, array $payload): array
    {
        return DB::transaction(function () use ($user, $payload) {
            $cart = $this->cartRepository->getActiveCartForUserWithLock($user->id);

            if (!$cart) {
                throw ValidationException::withMessages([
                    'cart' => ['No active cart found.'],
                ]);
            }

            $items = $this->cartRepository->getItemsByCartId($cart->id);

            if ($items->isEmpty()) {
                throw ValidationException::withMessages([
                    'cart' => ['Cannot checkout an empty cart.'],
                ]);
            }

            $itemsByProduct = $items
                ->groupBy('product_id')
                ->map(fn ($group) => [
                    'product_id' => (int) $group->first()->product_id,
                    'quantity' => (int) $group->sum('quantity'),
                    'price' => (string) $group->first()->price,
                ])
                ->values();

            $productIds = $itemsByProduct->pluck('product_id')->unique()->values()->all();
            $products = $this->productRepository->findForUpdateByIds($productIds)->keyBy('id');

            $totalAmount = 0;
            $billItems = [];

            foreach ($itemsByProduct as $item) {
                $product = $products->get($item['product_id']);

                if (!$product || !$product->is_active) {
                    throw ValidationException::withMessages([
                        'items' => ["Product for item {$item['product_id']} is not available."],
                    ]);
                }

                if ($product->stock_qty < $item['quantity']) {
                    throw ValidationException::withMessages([
                        'items' => ["Insufficient stock for {$product->name}."],
                    ]);
                }

                $lineTotal = ((float) $item['price']) * $item['quantity'];
                $totalAmount += $lineTotal;

                $billItems[] = [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'line_total' => round($lineTotal, 2),
                ];
            }

            $sale = $this->saleRepository->createSale([
                'user_id' => $user->id,
                'total_amount' => round($totalAmount, 2),
                'payment_method' => $payload['payment_method'],
            ]);

            foreach ($itemsByProduct as $item) {
                $product = $products->get($item['product_id']);

                $this->saleRepository->createSaleItem([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);

                if (!$this->productRepository->decrementStockAtomically($product, $item['quantity'])) {
                    throw ValidationException::withMessages([
                        'items' => ["Unable to update stock for {$product->name}."],
                    ]);
                }
            }

            $cart->status = 'checked_out';
            $cart->checked_out_at = now();
            $cart->save();

            $this->cartRepository->clearCart($cart->id);

            return [
                'sale_id' => $sale->id,
                'user_id' => $sale->user_id,
                'payment_method' => $sale->payment_method,
                'items' => $billItems,
                'total_amount' => round($totalAmount, 2),
                'checked_out_at' => $cart->checked_out_at?->toISOString(),
            ];
        });
    }

    public function removeItemFromCart(User $user, int $productId): array
    {
        $cart = $this->cartRepository->getActiveCartForUser($user->id);

        if ($cart) {
            $this->cartRepository->removeCartItem($cart->id, $productId);
        }

        return $this->getCartSummary($user);
    }
}
