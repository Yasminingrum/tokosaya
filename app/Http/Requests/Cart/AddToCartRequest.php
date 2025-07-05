<?php
// File: app/Http/Requests/Cart/AddToCartRequest.php

namespace App\Http\Requests\Cart;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Product;
use App\Models\ProductVariant;

class AddToCartRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'quantity' => 'required|integer|min:1|max:10'
        ];
    }

    public function messages()
    {
        return [
            'product_id.required' => 'Product selection is required',
            'product_id.exists' => 'Selected product is not available',
            'variant_id.exists' => 'Selected variant is not available',
            'quantity.required' => 'Quantity is required',
            'quantity.min' => 'Minimum quantity is 1',
            'quantity.max' => 'Maximum quantity is 10 per order'
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $product = Product::find($this->product_id);

            if (!$product) {
                return;
            }

            // Check if product is active
            if ($product->status !== 'active') {
                $validator->errors()->add('product_id', 'This product is not available for purchase');
                return;
            }

            // Check stock availability
            $availableStock = $product->stock_quantity;

            if ($this->variant_id) {
                $variant = ProductVariant::find($this->variant_id);
                if ($variant && $variant->product_id === $product->id) {
                    $availableStock = $variant->stock_quantity;
                } else {
                    $validator->errors()->add('variant_id', 'Selected variant does not belong to this product');
                    return;
                }
            }

            if ($product->track_stock && $availableStock < $this->quantity) {
                if ($availableStock === 0) {
                    $validator->errors()->add('quantity', 'This product is out of stock');
                } else {
                    $validator->errors()->add('quantity', "Only {$availableStock} items available in stock");
                }
            }
        });
    }
}
