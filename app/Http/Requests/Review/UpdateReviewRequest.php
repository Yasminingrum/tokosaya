<?php

namespace App\Http\Requests\Review;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $review = $this->route('review');

        // User must be authenticated and own the review
        return Auth::check() && $review && $review->user_id === Auth::id();
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
            'images' => [
                'nullable',
                'array',
                'max:5'
            ],
            'images.*' => [
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048' // 2MB max per image
            ],
            'delete_images' => [
                'nullable',
                'array'
            ],
            'delete_images.*' => [
                'string',
                'url'
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

            'images.max' => 'You can upload maximum 5 images.',
            'images.*.image' => 'All uploaded files must be images.',
            'images.*.mimes' => 'Images must be in JPG, JPEG, PNG, or WebP format.',
            'images.*.max' => 'Each image must not exceed 2MB.',

            'delete_images.*.url' => 'Invalid image URL for deletion.'
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
            'images' => 'review images',
            'images.*' => 'review image',
            'delete_images' => 'images to delete',
            'delete_images.*' => 'image to delete'
        ];
    }

    /**
     * Handle a failed authorization attempt.
     */
    protected function failedAuthorization()
    {
        if ($this->expectsJson()) {
            throw new \Illuminate\Http\Exceptions\HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to update this review.'
                ], 403)
            );
        }

        throw new \Illuminate\Auth\Access\AuthorizationException(
            'You are not authorized to update this review.'
        );
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

            // Custom validation: Check if review can still be edited (within 30 days)
            $review = $this->route('review');
            if ($review && $review->created_at->diffInDays() > 30) {
                $validator->errors()->add(
                    'edit_period',
                    'Reviews can only be edited within 30 days of creation.'
                );
            }

            // Custom validation: Validate delete_images are actually from this review
            if ($this->delete_images && $review) {
                $reviewImages = $review->images ? json_decode($review->images, true) : [];
                foreach ($this->delete_images as $imageUrl) {
                    if (!in_array($imageUrl, $reviewImages)) {
                        $validator->errors()->add(
                            'delete_images',
                            'One or more images to delete do not belong to this review.'
                        );
                        break;
                    }
                }
            }

            // Custom validation: Check total image count after additions and deletions
            if ($review) {
                $currentImages = $review->images ? json_decode($review->images, true) : [];
                $newImages = $this->images ? count($this->images) : 0;
                $deletingImages = $this->delete_images ? count($this->delete_images) : 0;

                $finalImageCount = count($currentImages) + $newImages - $deletingImages;

                if ($finalImageCount > 5) {
                    $validator->errors()->add(
                        'images',
                        'Total number of images cannot exceed 5. Please delete some existing images first.'
                    );
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
