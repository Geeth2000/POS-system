<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Services\ProductService;

class ProductController extends Controller
{
    public function __construct(private readonly ProductService $productService)
    {
        $this->middleware(['auth', 'web.role:admin,manager']);
    }

    /**
     * Display a listing of the products.
     */
    public function index(Request $request)
    {
        $products = $this->productService->listProducts(
            $request->only(['name', 'category_id', 'is_active']),
            20
        );

        $categories = Category::orderBy('name')->get();

        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'item_code'   => 'required|string|max:100|unique:products,sku',
            'category_id' => 'required|exists:categories,id',
            'price'       => 'required|numeric|min:0',
            'stock_qty'   => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);

        $this->productService->createProduct($validated);

        return redirect()->route('products.index')
            ->with('success', 'Product added successfully.');
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'item_code'   => 'required|string|max:100|unique:products,sku,' . $product->id,
            'category_id' => 'required|exists:categories,id',
            'price'       => 'required|numeric|min:0',
            'stock_qty'   => 'required|integer|min:0',
            'description' => 'nullable|string',
            'is_active'   => 'sometimes|boolean',
        ]);

        $this->productService->updateProduct($product, $validated);

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product)
    {
        $this->productService->deleteProduct($product);

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }
}
