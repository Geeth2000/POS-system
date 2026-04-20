<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product')?->id ?? $this->route('product');

        return [
            'barcode' => 'sometimes|string|max:100|unique:products,barcode,' . $productId,
            'item_code' => 'sometimes|string|max:100|unique:products,item_code,' . $productId,
            'name' => 'sometimes|string|max:255',
            'category_id' => 'sometimes|exists:categories,id',
            'price' => 'sometimes|numeric|min:0',
            'stock_qty' => 'sometimes|integer|min:0',
            'description' => 'nullable|string',
            'cost' => 'nullable|numeric|min:0',
            'is_active' => 'sometimes|boolean',
        ];
    }
}
