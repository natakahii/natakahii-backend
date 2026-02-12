<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\WishlistResource;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    /**
     * List the authenticated user's wishlist.
     */
    public function index(Request $request): JsonResponse
    {
        $wishlists = Wishlist::query()
            ->where('user_id', $request->user('api')->id)
            ->with('product.images', 'product.vendor')
            ->latest()
            ->paginate($request->input('per_page', 15));

        return response()->json([
            'wishlists' => WishlistResource::collection($wishlists),
            'meta' => [
                'current_page' => $wishlists->currentPage(),
                'last_page' => $wishlists->lastPage(),
                'per_page' => $wishlists->perPage(),
                'total' => $wishlists->total(),
            ],
        ]);
    }

    /**
     * Toggle a product in the wishlist.
     */
    public function toggle(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
        ]);

        $user = $request->user('api');
        $existing = Wishlist::where('user_id', $user->id)
            ->where('product_id', $request->product_id)
            ->first();

        if ($existing) {
            $existing->delete();

            return response()->json([
                'message' => 'Product removed from wishlist.',
                'wishlisted' => false,
            ]);
        }

        Wishlist::create([
            'user_id' => $user->id,
            'product_id' => $request->product_id,
        ]);

        return response()->json([
            'message' => 'Product added to wishlist.',
            'wishlisted' => true,
        ], 201);
    }
}
