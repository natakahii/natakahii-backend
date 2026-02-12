<?php

namespace App\Http\Controllers\Api\Shipping;

use App\Http\Controllers\Controller;
use App\Models\ShippingQuote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShippingQuoteController extends Controller
{
    /**
     * Request shipping quotes for an order or cart.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'destination_address' => ['required', 'string'],
            'weight_kg' => ['required', 'numeric', 'min:0.1'],
        ]);

        $quotes = [
            [
                'provider' => 'NatakaHii Cargo',
                'service_level' => 'standard',
                'price' => round($request->weight_kg * 2500, 2),
                'currency' => 'TZS',
                'estimated_days' => 5,
            ],
            [
                'provider' => 'NatakaHii Cargo',
                'service_level' => 'express',
                'price' => round($request->weight_kg * 5000, 2),
                'currency' => 'TZS',
                'estimated_days' => 2,
            ],
        ];

        $userId = $request->user('api')?->id;
        $saved = [];

        foreach ($quotes as $quote) {
            $saved[] = ShippingQuote::create(array_merge($quote, [
                'user_id' => $userId,
                'expires_at' => now()->addHours(24),
            ]));
        }

        return response()->json([
            'message' => 'Shipping quotes generated.',
            'quotes' => $saved,
        ]);
    }

    /**
     * Select a specific shipping quote.
     */
    public function select(ShippingQuote $quote): JsonResponse
    {
        if ($quote->expires_at && $quote->expires_at->isPast()) {
            return response()->json([
                'message' => 'This quote has expired. Please request new quotes.',
            ], 422);
        }

        ShippingQuote::query()
            ->where('user_id', $quote->user_id)
            ->where('id', '!=', $quote->id)
            ->update(['is_selected' => false]);

        $quote->update(['is_selected' => true]);

        return response()->json([
            'message' => 'Shipping quote selected.',
            'quote' => $quote->fresh(),
        ]);
    }
}
