<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'barcode' => 'required|string|max:100|unique:products,barcode',
            'item_code' => 'required|string|max:100|unique:products,item_code',
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'stock_qty' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'cost' => 'nullable|numeric|min:0',
            'is_active' => 'sometimes|boolean',
        ];
    }
}
