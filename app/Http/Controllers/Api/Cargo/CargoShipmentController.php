<?php

namespace App\Http\Controllers\Api\Cargo;

use App\Http\Controllers\Controller;
use App\Models\CargoShipment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CargoShipmentController extends Controller
{
    /**
     * Create a new cargo shipment (admin only).
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'order_id' => ['required', 'exists:orders,id'],
            'fulfillment_center_id' => ['nullable', 'exists:fulfillment_centers,id'],
            'destination_address' => ['required', 'string'],
            'recipient_name' => ['required', 'string', 'max:255'],
            'recipient_phone' => ['nullable', 'string', 'max:20'],
        ]);

        $shipment = CargoShipment::create([
            'order_id' => $request->order_id,
            'fulfillment_center_id' => $request->fulfillment_center_id,
            'tracking_number' => CargoShipment::generateTrackingNumber(),
            'status' => 'pending',
            'destination_address' => $request->destination_address,
            'recipient_name' => $request->recipient_name,
            'recipient_phone' => $request->recipient_phone,
        ]);

        return response()->json([
            'message' => 'Shipment created.',
            'shipment' => $shipment,
        ], 201);
    }
}
