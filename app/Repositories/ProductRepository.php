<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class ProductRepository
{
    public function baseQuery(): Builder
    {
        return Product::query()->with('category');
    }

    public function queryForSearch(array $filters): Builder
    {
        $query = $this->baseQuery();

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (array_key_exists('is_active', $filters) && $filters['is_active'] !== null) {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        if (!empty($filters['barcode'])) {
            $query->where('barcode', $filters['barcode']);
        } elseif (!empty($filters['item_code'])) {
            $query->where('item_code', $filters['item_code']);
        } elseif (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        return $query;
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(Product $product, array $data): Product
    {
        $product->update($data);

        return $product->refresh();
    }

    public function delete(Product $product): void
    {
        $product->delete();
    }

    public function lowStock(int $threshold): Collection
    {
        return Product::query()
            ->where('stock_qty', '<=', $threshold)
            ->where('is_active', true)
            ->with('category')
            ->get();
    }

    public function findForUpdateByIds(array $productIds): Collection
    {
        return Product::query()
            ->whereIn('id', $productIds)
            ->lockForUpdate()
            ->get();
    }

    public function decrementStockAtomically(Product $product, int $quantity): bool
    {
        $affected = Product::query()
            ->whereKey($product->id)
            ->where('stock_qty', '>=', $quantity)
            ->update([
                'stock_qty' => DB::raw('stock_qty - ' . (int) $quantity),
                'quantity' => DB::raw('quantity - ' . (int) $quantity),
            ]);

        return $affected === 1;
    }

    public function searchForPos(array $filters, int $limit = 20): Collection
    {
        $query = Product::query()
            ->select(['id', 'name', 'price', 'stock_qty'])
            ->where('is_active', true)
            ->limit($limit);

        if (!empty($filters['query'])) {
            $term = $filters['query'];
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', '%' . $term . '%')
                  ->orWhere('sku', 'like', '%' . $term . '%')
                  ->orWhere('barcode', 'like', '%' . $term . '%')
                  ->orWhere('item_code', 'like', '%' . $term . '%');
            });
        } elseif (!empty($filters['barcode'])) {
            $query->where('barcode', $filters['barcode']);
        } elseif (!empty($filters['item_code'])) {
            $query->where('item_code', $filters['item_code']);
        } elseif (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        return $query
            ->orderBy('name')
            ->get()
            ->map(fn (Product $product) => [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'stock' => $product->stock_qty,
            ]);
    }
}
