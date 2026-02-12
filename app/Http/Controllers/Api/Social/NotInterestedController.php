<?php

namespace App\Http\Controllers\Api\Social;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\NotInterested;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotInterestedController extends Controller
{
    /**
     * Mark a product as not interested.
     */
    public function store(Product $product, Request $request): JsonResponse
    {
        $user = $request->user('api');

        NotInterested::firstOrCreate([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        return response()->json([
            'message' => 'Product marked as not interested.',
        ]);
    }

    /**
     * List products the user marked as not interested.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user('api');

        $products = Product::query()
            ->whereHas('notInterested', fn ($q) => $q->where('user_id', $user->id))
            ->with('images', 'vendor')
            ->paginate($request->input('per_page', 15));

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
}
