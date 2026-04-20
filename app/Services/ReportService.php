<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Support\Carbon;

class ReportService
{
    public function dailySalesTotal(string $date): array
    {
        $day = Carbon::parse($date);
        $start = $day->copy()->startOfDay();
        $end = $day->copy()->endOfDay();

        $summary = Transaction::query()
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('COUNT(*) as total_transactions')
            ->selectRaw('COALESCE(SUM(total_amount), 0) as total_sales')
            ->selectRaw('COALESCE(SUM(tax_amount), 0) as total_tax')
            ->selectRaw('COALESCE(SUM(discount_amount), 0) as total_discount')
            ->first();

        $byPaymentMethod = Transaction::query()
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('payment_method, COALESCE(SUM(total_amount), 0) as total_sales')
            ->groupBy('payment_method')
            ->pluck('total_sales', 'payment_method');

        return [
            'date' => $day->toDateString(),
            'total_transactions' => (int) ($summary->total_transactions ?? 0),
            'total_sales' => (float) ($summary->total_sales ?? 0),
            'total_tax' => (float) ($summary->total_tax ?? 0),
            'total_discount' => (float) ($summary->total_discount ?? 0),
            'by_payment_method' => $byPaymentMethod,
        ];
    }

    public function topSellingProducts(string $fromDate, string $toDate, int $limit = 10): array
    {
        $start = Carbon::parse($fromDate)->startOfDay();
        $end = Carbon::parse($toDate)->endOfDay();

        return TransactionItem::query()
            ->join('transactions', 'transactions.id', '=', 'transaction_items.transaction_id')
            ->join('products', 'products.id', '=', 'transaction_items.product_id')
            ->whereBetween('transactions.created_at', [$start, $end])
            ->where('products.is_active', true)
            ->selectRaw('products.id as product_id, products.name, products.price')
            ->selectRaw('SUM(transaction_items.quantity) as total_quantity')
            ->selectRaw('SUM(transaction_items.line_total) as total_sales')
            ->groupBy('products.id', 'products.name', 'products.price')
            ->orderByDesc('total_quantity')
            ->orderByDesc('total_sales')
            ->limit($limit)
            ->get()
            ->map(fn ($product) => [
                'product_id' => (int) $product->product_id,
                'name' => $product->name,
                'total_quantity' => (int) $product->total_quantity,
                'total_sales' => (float) $product->total_sales,
                'price' => (float) $product->price,
            ])
            ->all();
    }

    public function lowStockItems(int $threshold = 10): array
    {
        return Product::query()
            ->select(['id', 'name', 'barcode', 'item_code', 'stock_qty', 'price', 'category_id'])
            ->where('is_active', true)
            ->where('stock_qty', '<=', $threshold)
            ->orderBy('stock_qty')
            ->orderBy('name')
            ->get()
            ->map(fn (Product $product) => [
                'id' => $product->id,
                'name' => $product->name,
                'barcode' => $product->barcode,
                'item_code' => $product->item_code,
                'stock_qty' => $product->stock_qty,
                'price' => (float) $product->price,
                'category_id' => $product->category_id,
            ])
            ->all();
    }
}
