<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AlertResource;
use App\Models\Alert;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    /**
     * Create a price-drop or back-in-stock alert.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'type' => ['required', 'string', 'in:price_drop,back_in_stock'],
            'target_price' => ['nullable', 'numeric', 'min:0', 'required_if:type,price_drop'],
        ]);

        $user = $request->user('api');

        $alert = Alert::updateOrCreate(
            [
                'user_id' => $user->id,
                'product_id' => $request->product_id,
                'type' => $request->type,
            ],
            [
                'target_price' => $request->target_price,
                'is_triggered' => false,
            ]
        );

        return response()->json([
            'message' => 'Alert created.',
            'alert' => new AlertResource($alert->load('product')),
        ], 201);
    }
}
