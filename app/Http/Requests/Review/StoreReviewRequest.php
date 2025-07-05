<?php

namespace App\Http\Requests\Review;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'rating' => [
                'required',
                'integer',
                'min:1',
                'max:5'
            ],
            'title' => [
                'nullable',
                'string',
                'max:150',
                'min:3'
            ],
            'review' => [
                'nullable',
                'string',
                'max:2000',
                'min:10'
            ],
            'order_item_id' => [
                'nullable',
                'integer',
                'exists:order_items,id'
            ],
            'images' => [
                'nullable',
                'array',
                'max:5'
            ],
            'images.*' => [
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048' // 2MB max per image
            ]
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'rating.required' => 'Please provide a rating for this product.',
            'rating.integer' => 'Rating must be a valid number.',
            'rating.min' => 'Rating must be at least 1 star.',
            'rating.max' => 'Rating cannot exceed 5 stars.',

            'title.min' => 'Review title must be at least 3 characters.',
            'title.max' => 'Review title cannot exceed 150 characters.',

            'review.min' => 'Review content must be at least 10 characters.',
            'review.max' => 'Review content cannot exceed 2000 characters.',

            'order_item_id.exists' => 'Invalid order item specified.',

            'images.max' => 'You can upload maximum 5 images.',
            'images.*.image' => 'All uploaded files must be images.',
            'images.*.mimes' => 'Images must be in JPG, JPEG, PNG, or WebP format.',
            'images.*.max' => 'Each image must not exceed 2MB.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'rating' => 'rating',
            'title' => 'review title',
            'review' => 'review content',
            'order_item_id' => 'order item',
            'images' => 'review images',
            'images.*' => 'review image'
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        if ($this->expectsJson()) {
            throw new \Illuminate\Http\Exceptions\HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422)
            );
        }

        parent::failedValidation($validator);
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(\Illuminate\Contracts\Validation\Validator $validator): void
    {
        $validator->after(function ($validator) {
            // Custom validation: Ensure either title or review content is provided
            if (empty($this->title) && empty($this->review)) {
                $validator->errors()->add(
                    'content',
                    'Please provide either a review title or review content.'
                );
            }

            // Custom validation: Check if user has already reviewed this product
            if ($this->route('product')) {
                $product = $this->route('product');
                $existingReview = \App\Models\ProductReview::where('user_id', Auth::id())
                                                         ->where('product_id', $product->id)
                                                         ->when($this->order_item_id, function($query) {
                                                             $query->where('order_item_id', $this->order_item_id);
                                                         })
                                                         ->first();

                if ($existingReview) {
                    $validator->errors()->add(
                        'duplicate',
                        'You have already reviewed this product.'
                    );
                }
            }

            // Custom validation: Verify order item ownership if provided
            if ($this->order_item_id) {
                $orderItem = \App\Models\OrderItem::find($this->order_item_id);
                if ($orderItem) {
                    $order = $orderItem->order;
                    if (!$order || $order->user_id !== Auth::id()) {
                        $validator->errors()->add(
                            'order_item_id',
                            'Invalid order item - you can only review products you have purchased.'
                        );
                    } elseif ($order->payment_status !== 'paid') {
                        $validator->errors()->add(
                            'order_item_id',
                            'You can only review products from paid orders.'
                        );
                    } elseif ($order->status !== 'delivered') {
                        $validator->errors()->add(
                            'order_item_id',
                            'You can only review products from delivered orders.'
                        );
                    }
                }
            }
        });
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Trim whitespace from text fields
        if ($this->has('title')) {
            $this->merge([
                'title' => trim($this->title)
            ]);
        }

        if ($this->has('review')) {
            $this->merge([
                'review' => trim($this->review)
            ]);
        }

        // Convert empty strings to null
        if ($this->title === '') {
            $this->merge(['title' => null]);
        }

        if ($this->review === '') {
            $this->merge(['review' => null]);
        }
    }
}
