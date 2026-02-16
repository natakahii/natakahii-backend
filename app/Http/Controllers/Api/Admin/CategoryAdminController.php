<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class CategoryAdminController extends Controller
{
    /**
     * List all categories (including inactive ones).
     */
    public function index(): JsonResponse
    {
        $categories = Category::query()
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->with(['children' => function ($query) {
                $query->orderBy('sort_order');
            }])
            ->withCount('products')
            ->get();

        return response()->json([
            'categories' => CategoryResource::collection($categories),
        ]);
    }

    /**
     * Store a new category.
     */
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $validated = $request->validated();

        // Generate slug from name if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Ensure slug is unique
        $originalSlug = $validated['slug'];
        $counter = 1;
        while (Category::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        // Set default sort_order if not provided
        if (!isset($validated['sort_order'])) {
            $maxSortOrder = Category::where('parent_id', $validated['parent_id'] ?? null)
                ->max('sort_order');
            $validated['sort_order'] = ($maxSortOrder ?? 0) + 1;
        }

        // Set default is_active if not provided
        if (!isset($validated['is_active'])) {
            $validated['is_active'] = true;
        }

        $category = Category::create($validated);

        return response()->json([
            'message' => 'Category created successfully',
            'category' => new CategoryResource($category->load('children')),
        ], 201);
    }

    /**
     * Update an existing category.
     */
    public function update(StoreCategoryRequest $request, Category $category): JsonResponse
    {
        $validated = $request->validated();

        // Generate slug from name if not provided
        if (empty($validated['slug']) && isset($validated['name'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Ensure slug is unique (excluding current category)
        if (isset($validated['slug'])) {
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (Category::where('slug', $validated['slug'])
                ->where('id', '!=', $category->id)
                ->exists()) {
                $validated['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        $category->update($validated);

        return response()->json([
            'message' => 'Category updated successfully',
            'category' => new CategoryResource($category->load('children')),
        ]);
    }

    /**
     * Delete a category.
     */
    public function destroy(Category $category): JsonResponse
    {
        // Check if category has products
        if ($category->products()->exists()) {
            return response()->json([
                'message' => 'Cannot delete category with existing products',
            ], 422);
        }

        // Check if category has children
        if ($category->children()->exists()) {
            return response()->json([
                'message' => 'Cannot delete category with subcategories',
            ], 422);
        }

        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully',
        ]);
    }

    /**
     * Toggle category active status.
     */
    public function toggleStatus(Category $category): JsonResponse
    {
        $category->update([
            'is_active' => !$category->is_active,
        ]);

        return response()->json([
            'message' => 'Category status updated successfully',
            'category' => new CategoryResource($category),
        ]);
    }
}
