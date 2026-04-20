<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController
{
    /**
     * Display a listing of categories
     */
    public function index(Request $request)
    {
        $query = Category::with('products');

        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $categories = $query->paginate($request->per_page ?? 20);

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    /**
     * Store a newly created category
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string',
        ]);

        $category = Category::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully',
            'data' => $category,
        ], 201);
    }

    /**
     * Display the specified category
     */
    public function show(Category $category)
    {
        return response()->json([
            'success' => true,
            'data' => $category->load('products'),
        ]);
    }

    /**
     * Update the specified category
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $category->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully',
            'data' => $category,
        ]);
    }

    /**
     * Delete the specified category
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully',
        ]);
    }
}
