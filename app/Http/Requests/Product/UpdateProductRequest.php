<?php
// File: app/Http/Requests/Product/UpdateProductRequest.php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check() && auth()->user()->can('update', $this->route('product'));
    }

    public function rules()
    {
        $product = $this->route('product');

        return [
            'name' => 'required|string|max:200',
            'slug' => [
                'nullable',
                'string',
                'max:220',
                Rule::unique('products', 'slug')->ignore($product->id)
            ],
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'sku' => [
                'required',
                'string',
                'max:50',
                Rule::unique('products', 'sku')->ignore($product->id)
            ],
            'barcode' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('products', 'barcode')->ignore($product->id)
            ],
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',

            // Price in rupiah
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',

            // Stock
            'stock_quantity' => 'required|integer|min:0',
            'min_stock_level' => 'nullable|integer|min:0',
            'max_stock_level' => 'nullable|integer|min:1',
            'track_stock' => 'boolean',
            'allow_backorder' => 'boolean',

            // Physical attributes
            'weight_grams' => 'nullable|integer|min:0',
            'length_mm' => 'nullable|integer|min:0',
            'width_mm' => 'nullable|integer|min:0',
            'height_mm' => 'nullable|integer|min:0',

            // Status
            'status' => 'required|in:draft,active,inactive,discontinued',
            'featured' => 'boolean',
            'digital' => 'boolean',

            // SEO
            'meta_title' => 'nullable|string|max:160',
            'meta_description' => 'nullable|string|max:320'
        ];
    }

    protected function prepareForValidation()
    {
        // Convert price from rupiah to cents
        if ($this->has('price')) {
            $this->merge([
                'price_cents' => (int) round($this->price * 100)
            ]);
        }

        if ($this->has('compare_price')) {
            $this->merge([
                'compare_price_cents' => (int) round($this->compare_price * 100)
            ]);
        }

        if ($this->has('cost_price')) {
            $this->merge([
                'cost_price_cents' => (int) round($this->cost_price * 100)
            ]);
        }
    }
}
