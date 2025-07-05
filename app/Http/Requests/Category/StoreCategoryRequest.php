<?php
// File: app/Http/Requests/Category/StoreCategoryRequest.php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check() && auth()->user()->can('create', \App\Models\Category::class);
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:100',
            'slug' => 'nullable|string|max:120|unique:categories,slug',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'icon' => 'nullable|string|max:100',
            'parent_id' => 'nullable|exists:categories,id',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'meta_title' => 'nullable|string|max:160',
            'meta_description' => 'nullable|string|max:320'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Category name is required',
            'name.max' => 'Category name cannot exceed 100 characters',
            'slug.unique' => 'This slug is already taken',
            'image.image' => 'File must be an image',
            'image.max' => 'Image size cannot exceed 2MB',
            'parent_id.exists' => 'Selected parent category does not exist'
        ];
    }
}
