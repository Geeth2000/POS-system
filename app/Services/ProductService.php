<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\ProductRepository;

class ProductService
{
    public function __construct(private readonly ProductRepository $productRepository)
    {
    }

    public function listProducts(array $filters, int $perPage = 15)
    {
        return $this->productRepository
            ->queryForSearch($filters)
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function createProduct(array $data): Product
    {
        $data = $this->normalizeProductData($data);

        return $this->productRepository->create($data);
    }

    public function updateProduct(Product $product, array $data): Product
    {
        $data = $this->normalizeProductData($data, false);

        return $this->productRepository->update($product, $data);
    }

    public function deleteProduct(Product $product): void
    {
        $this->productRepository->delete($product);
    }

    public function lowStock(int $threshold)
    {
        return $this->productRepository->lowStock($threshold);
    }

    public function searchForPos(array $filters, int $limit = 20)
    {
        return $this->productRepository->searchForPos($filters, $limit);
    }

    private function normalizeProductData(array $data, bool $forCreate = true): array
    {
        if ($forCreate && !array_key_exists('is_active', $data)) {
            $data['is_active'] = true;
        }

        if ($forCreate && !array_key_exists('cost', $data)) {
            // Legacy schema has a non-null cost column; keep API payload minimal.
            $data['cost'] = 0;
        }

        if (array_key_exists('item_code', $data)) {
            // Keep legacy sku in sync with new item_code.
            $data['sku'] = $data['item_code'];
        }

        if (array_key_exists('stock_qty', $data)) {
            // Keep legacy quantity in sync with new stock_qty.
            $data['quantity'] = $data['stock_qty'];
        }

        return $data;
    }
}
