<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\VendorDropoff;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VendorDropoffController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $vendor = $request->user('api')->vendor;

        if (! $vendor) {
            return response()->json(['message' => 'No vendor profile found.'], 404);
        }

        $dropoffs = VendorDropoff::where('vendor_id', $vendor->id)
            ->with('order', 'fulfillmentCenter')
            ->latest()
            ->paginate($request->input('per_page', 15));

        return response()->json(['dropoffs' => $dropoffs]);
    }

    public function store(Request $request): JsonResponse
    {
        $vendor = $request->user('api')->vendor;

        if (! $vendor) {
            return response()->json(['message' => 'No vendor profile found.'], 404);
        }

        $request->validate([
            'order_id' => ['required', 'exists:orders,id'],
            'fulfillment_center_id' => ['required', 'exists:fulfillment_centers,id'],
            'notes' => ['nullable', 'string'],
        ]);

        $dropoff = VendorDropoff::create([
            'vendor_id' => $vendor->id,
            'order_id' => $request->order_id,
            'fulfillment_center_id' => $request->fulfillment_center_id,
            'status' => 'pending',
            'notes' => $request->notes,
        ]);

        return response()->json([
            'message' => 'Dropoff registered.',
            'dropoff' => $dropoff->load('fulfillmentCenter'),
        ], 201);
    }
}
