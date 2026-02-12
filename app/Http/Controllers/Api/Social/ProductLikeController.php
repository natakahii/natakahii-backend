<?php

namespace App\Http\Controllers\Api\Social;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductLike;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductLikeController extends Controller
{
    /**
     * Like a product.
     */
    public function store(Product $product, Request $request): JsonResponse
    {
        $user = $request->user('api');

        ProductLike::firstOrCreate([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        return response()->json([
            'message' => 'Product liked.',
            'likes_count' => $product->likes()->count(),
        ]);
    }

    /**
     * Unlike a product.
     */
    public function destroy(Product $product, Request $request): JsonResponse
    {
        $user = $request->user('api');

        ProductLike::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->delete();

        return response()->json([
            'message' => 'Product unliked.',
            'likes_count' => $product->likes()->count(),
        ]);
    }
}
