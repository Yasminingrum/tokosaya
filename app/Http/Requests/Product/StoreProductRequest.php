<?php
// File: app/Http/Requests/Product/StoreProductRequest.php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check() && auth()->user()->can('create', \App\Models\Product::class);
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:200',
            'slug' => 'nullable|string|max:220|unique:products,slug',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'sku' => 'required|string|max:50|unique:products,sku',
            'barcode' => 'nullable|string|max:50|unique:products,barcode',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',

            // Price in rupiah (will be converted to cents)
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0|gt:price',
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
            'meta_description' => 'nullable|string|max:320',

            // Images
            'images' => 'nullable|array',
            'images.*' => 'image|max:2048',

            // Variants
            'variants' => 'nullable|array',
            'variants.*.name' => 'required|string|max:60',
            'variants.*.value' => 'required|string|max:60',
            'variants.*.price_adjustment' => 'nullable|numeric',
            'variants.*.stock_quantity' => 'nullable|integer|min:0',
            'variants.*.sku' => 'nullable|string|max:50',

            // Attributes
            'attributes' => 'nullable|array',
            'attributes.*' => 'exists:product_attributes,id'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Product name is required',
            'description.required' => 'Product description is required',
            'sku.required' => 'SKU is required',
            'sku.unique' => 'SKU must be unique',
            'category_id.required' => 'Please select a category',
            'price.required' => 'Price is required',
            'price.min' => 'Price must be greater than 0',
            'stock_quantity.required' => 'Stock quantity is required'
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
