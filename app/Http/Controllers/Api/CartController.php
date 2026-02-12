<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Show the authenticated user's cart.
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user('api');
        $cart = Cart::getOrCreateForUser($user->id);
        $cart->load('items.product.images');

        return response()->json([
            'cart' => new CartResource($cart),
        ]);
    }

    /**
     * Add an item to the cart (or update quantity if already present).
     */
    public function addItem(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $user = $request->user('api');
        $product = Product::findOrFail($request->product_id);
        $cart = Cart::getOrCreateForUser($user->id);

        $item = $cart->items()->where('product_id', $product->id)->first();

        if ($item) {
            $item->update([
                'quantity' => $item->quantity + $request->quantity,
                'price' => $product->discount_price ?? $product->price,
            ]);
        } else {
            $item = CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'price' => $product->discount_price ?? $product->price,
            ]);
        }

        $cart->load('items.product.images');

        return response()->json([
            'message' => 'Item added to cart.',
            'cart' => new CartResource($cart),
        ]);
    }
}
