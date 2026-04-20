<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Services\ProductService;

class ProductController
{
    public function __construct(private readonly ProductService $productService)
    {
    }

    /**
     * Display a listing of products
     */
    public function index(Request $request)
    {
        $request->validate([
            'barcode' => 'nullable|string|max:100',
            'item_code' => 'nullable|string|max:100',
            'name' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'is_active' => 'nullable|boolean',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $products = $this->productService->listProducts(
            $request->only(['barcode', 'item_code', 'name', 'category_id', 'is_active']),
            (int) ($request->input('per_page', 15))
        );

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    /**
     * Search products for POS with minimal payload.
     */
    public function search(Request $request)
    {
        $request->validate([
            'barcode' => 'nullable|string|max:100',
            'item_code' => 'nullable|string|max:100',
            'name' => 'nullable|string|max:255',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        if (!$request->filled('barcode') && !$request->filled('item_code') && !$request->filled('name')) {
            return response()->json([
                'success' => false,
                'message' => 'Provide at least one search parameter: barcode, item_code, or name.',
            ], 422);
        }

        $data = $this->productService->searchForPos(
            $request->only(['barcode', 'item_code', 'name']),
            (int) $request->input('limit', 20)
        );

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Store a newly created product
     */
    public function store(StoreProductRequest $request)
    {
        $product = $this->productService->createProduct($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'data' => $product->load('category'),
        ], 201);
    }

    /**
     * Display the specified product
     */
    public function show(Product $product)
    {
        return response()->json([
            'success' => true,
            'data' => $product->load('category'),
        ]);
    }

    /**
     * Update the specified product
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $product = $this->productService->updateProduct($product, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => $product->load('category'),
        ]);
    }

    /**
     * Delete the specified product
     */
    public function destroy(Product $product)
    {
        $this->productService->deleteProduct($product);

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully',
        ]);
    }

    /**
     * Get low stock products
     */
    public function lowStock(Request $request)
    {
        $request->validate([
            'threshold' => 'nullable|integer|min:0|max:1000000',
        ]);

        $threshold = (int) ($request->input('threshold', 10));
        $products = $this->productService->lowStock($threshold);

        return response()->json([
            'success' => true,
            'threshold' => $threshold,
            'count' => $products->count(),
            'data' => $products,
        ]);
    }
}
