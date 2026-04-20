<?php

namespace App\Repositories;

use App\Models\Cart;
use App\Models\CartItem;

class CartRepository
{
    public function getOrCreateActiveCart(int $userId): Cart
    {
        $cart = Cart::query()
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->latest('id')
            ->first();

        if ($cart) {
            return $cart;
        }

        return Cart::create([
            'user_id' => $userId,
            'status' => 'active',
        ]);
    }

    public function getActiveCartForUser(int $userId): ?Cart
    {
        return Cart::query()
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->latest('id')
            ->first();
    }

    public function getActiveCartForUserWithItems(int $userId): ?Cart
    {
        return Cart::query()
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->latest('id')
            ->with(['items.product:id,name'])
            ->first();
    }

    public function getActiveCartForUserWithLock(int $userId): ?Cart
    {
        return Cart::query()
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->latest('id')
            ->lockForUpdate()
            ->first();
    }

    public function upsertCartItem(Cart $cart, int $productId, int $quantity, string $price): CartItem
    {
        $item = CartItem::query()->firstOrNew([
            'cart_id' => $cart->id,
            'product_id' => $productId,
        ]);

        $item->quantity = $item->exists ? $item->quantity + $quantity : $quantity;
        $item->price = $price;
        $item->save();

        return $item;
    }

    public function getItemsByCartId(int $cartId)
    {
        return CartItem::query()
            ->where('cart_id', $cartId)
            ->with(['product:id,name,stock_qty,is_active'])
            ->get();
    }

    public function clearCart(int $cartId): void
    {
        CartItem::query()->where('cart_id', $cartId)->delete();
    }

    public function removeCartItem(int $cartId, int $productId): void
    {
        CartItem::query()
            ->where('cart_id', $cartId)
            ->where('product_id', $productId)
            ->delete();
    }
}
