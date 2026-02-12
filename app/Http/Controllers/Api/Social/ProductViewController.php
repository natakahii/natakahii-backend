<?php

namespace App\Http\Controllers\Api\Social;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductView;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductViewController extends Controller
{
    /**
     * Record a product view event.
     */
    public function store(Product $product, Request $request): JsonResponse
    {
        ProductView::create([
            'product_id' => $product->id,
            'user_id' => $request->user('api')?->id,
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'message' => 'View recorded.',
        ]);
    }
}
