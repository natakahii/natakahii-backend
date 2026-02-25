<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AttributeResource;
use App\Http\Resources\CategoryResource;
use App\Models\Attribute;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    /**
     * List all active categories with their children.
     */
    public function index(): JsonResponse
    {
        $categories = Category::query()
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->with(['children' => function ($query) {
                $query->where('is_active', true)->orderBy('sort_order');
            }])
            ->withCount('products')
            ->get();

        return response()->json([
            'data' => CategoryResource::collection($categories),
        ]);
    }

    /**
     * Get filterable attributes for a category.
     */
    public function filters(Category $category): JsonResponse
    {
        $attributes = Attribute::query()
            ->where('is_filterable', true)
            ->whereHas('productAttributeValues', function ($query) use ($category) {
                $query->whereHas('product', function ($q) use ($category) {
                    $q->where('category_id', $category->id);
                });
            })
            ->with('values')
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'category' => new CategoryResource($category),
            'filters' => AttributeResource::collection($attributes),
        ]);
    }
}
