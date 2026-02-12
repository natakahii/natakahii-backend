<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductAdminController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Product::with('vendor', 'category')->withCount('reviews');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        $products = $query->latest()->paginate($request->input('per_page', 15));

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

    public function moderate(Product $product, Request $request): JsonResponse
    {
        $request->validate(['status' => ['required', 'in:active,draft,out_of_stock']]);
        $product->update(['status' => $request->status]);

        return response()->json([
            'message' => "Product status changed to {$request->status}.",
            'product' => new ProductResource($product->fresh('vendor', 'category')),
        ]);
    }
}
