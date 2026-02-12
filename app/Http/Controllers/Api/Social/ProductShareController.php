<?php

namespace App\Http\Controllers\Api\Social;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductShare;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductShareController extends Controller
{
    /**
     * Record a product share event.
     */
    public function store(Product $product, Request $request): JsonResponse
    {
        $request->validate([
            'platform' => ['nullable', 'string', 'max:50'],
        ]);

        ProductShare::create([
            'product_id' => $product->id,
            'user_id' => $request->user('api')?->id,
            'platform' => $request->platform,
        ]);

        return response()->json([
            'message' => 'Share recorded.',
            'shares_count' => $product->shares()->count(),
        ]);
    }
}
