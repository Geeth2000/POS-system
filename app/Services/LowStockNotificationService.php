<?php

namespace App\Services;

use App\Models\Product;
use App\Models\User;
use App\Notifications\LowStockNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class LowStockNotificationService
{
    /**
     * Check stock for the given products and generate/resolve notifications.
     */
    public function checkProducts(array $productIds): void
    {
        if (empty($productIds)) {
            return;
        }

        $products = Product::whereIn('id', $productIds)->get();

        // Get admins and managers who should receive the notifications
        $notifiableUsers = User::whereIn('role', ['admin', 'manager'])
            ->where('is_active', true)
            ->get();

        if ($notifiableUsers->isEmpty()) {
            return;
        }

        foreach ($products as $product) {
            $threshold = $product->low_stock_threshold ?? 5;
            $isLowStock = $product->stock_qty <= $threshold;

            // Find existing unread low stock notifications for this product
            $existingNotificationQuery = DB::table('notifications')
                ->where('type', LowStockNotification::class)
                ->where('read_at', null)
                ->whereJsonContains('data->product_id', $product->id);

            if ($isLowStock) {
                // Generate notification if one doesn't already exist
                if (!$existingNotificationQuery->exists()) {
                    Notification::send($notifiableUsers, new LowStockNotification($product));
                }
            } else {
                // If stock is back above threshold, resolve any active notifications
                $existingNotificationQuery->update(['read_at' => now()]);
            }
        }
    }

    /**
     * Convenience method for a single product.
     */
    public function checkProduct(Product $product): void
    {
        $this->checkProducts([$product->id]);
    }
}
