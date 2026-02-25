<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $categoryId = $this->route('category')?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('categories', 'slug')->ignore($categoryId),
            ],
            'icon' => ['nullable', 'string', 'max:100'],
            'parent_id' => [
                'nullable',
                'integer',
                'exists:categories,id',
                function ($attribute, $value, $fail) use ($categoryId) {
                    // Prevent setting parent to self
                    if ($categoryId && $value == $categoryId) {
                        $fail('A category cannot be its own parent.');
                    }
                    
                    // Prevent circular references (parent cannot be a child of this category)
                    if ($categoryId && $value) {
                        $parent = \App\Models\Category::find($value);
                        if ($parent && $parent->parent_id == $categoryId) {
                            $fail('Cannot create circular category relationship.');
                        }
                    }
                },
            ],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Category name is required',
            'name.max' => 'Category name cannot exceed 255 characters',
            'slug.regex' => 'Slug must contain only lowercase letters, numbers, and hyphens',
            'slug.unique' => 'This slug is already in use',
            'icon.max' => 'Icon name cannot exceed 100 characters',
            'parent_id.exists' => 'The selected parent category does not exist',
            'is_active.boolean' => 'Active status must be true or false',
            'sort_order.integer' => 'Sort order must be a number',
            'sort_order.min' => 'Sort order must be at least 0',
        ];
    }
}
