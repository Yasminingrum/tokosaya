<?php
// File: app/Http/Requests/Category/UpdateCategoryRequest.php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check() && auth()->user()->can('update', $this->route('category'));
    }

    public function rules()
    {
        $category = $this->route('category');

        return [
            'name' => 'required|string|max:100',
            'slug' => [
                'nullable',
                'string',
                'max:120',
                Rule::unique('categories', 'slug')->ignore($category->id)
            ],
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'icon' => 'nullable|string|max:100',
            'parent_id' => [
                'nullable',
                'exists:categories,id',
                Rule::notIn([$category->id]) // Cannot be parent of itself
            ],
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'meta_title' => 'nullable|string|max:160',
            'meta_description' => 'nullable|string|max:320'
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $category = $this->route('category');
            $parentId = $this->parent_id;

            if ($parentId) {
                // Check if trying to set a descendant as parent (would create circular reference)
                $descendants = $this->getDescendantIds($category->id);

                if (in_array($parentId, $descendants)) {
                    $validator->errors()->add('parent_id', 'Cannot set a descendant category as parent');
                }
            }
        });
    }

    private function getDescendantIds($categoryId)
    {
        $descendants = [];
        $children = \App\Models\Category::where('parent_id', $categoryId)->pluck('id')->toArray();

        foreach ($children as $childId) {
            $descendants[] = $childId;
            $descendants = array_merge($descendants, $this->getDescendantIds($childId));
        }

        return $descendants;
    }
}
