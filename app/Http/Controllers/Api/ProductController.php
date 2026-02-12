<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ReviewResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * List active products with filtering, search, and pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Product::query()
            ->where('status', 'active')
            ->with(['vendor', 'category', 'images'])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        $sortField = $request->input('sort_by', 'created_at');
        $sortDirection = $request->input('sort_dir', 'desc');
        $allowedSorts = ['created_at', 'price', 'name'];

        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDirection === 'asc' ? 'asc' : 'desc');
        }

        $products = $query->paginate($request->input('per_page', 15));

        return response()->json([
            'products' => ProductResource::collection($products),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ]);
    }

    /**
     * Show a single product with full details.
     */
    public function show(Product $product): JsonResponse
    {
        $product->load([
            'vendor',
            'category',
            'images',
            'variants.attributeValues.attribute',
            'variants.attributeValues.attributeValue',
            'attributeValues.attribute',
            'attributeValues.attributeValue',
        ])
            ->loadCount('reviews')
            ->loadAvg('reviews', 'rating');

        $reviews = $product->reviews()
            ->with('user')
            ->latest()
            ->limit(5)
            ->get();

        return response()->json([
            'product' => new ProductResource($product),
            'recent_reviews' => ReviewResource::collection($reviews),
        ]);
    }
}
