<?php

namespace App\Http\Controllers\Api\Cargo;

use App\Http\Controllers\Controller;
use App\Models\CargoShipment;
use App\Models\TrackingEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShipmentTrackingController extends Controller
{
    /**
     * Show tracking info for a shipment.
     */
    public function show(CargoShipment $shipment): JsonResponse
    {
        $shipment->load('trackingEvents', 'order');

        return response()->json([
            'shipment' => $shipment,
            'tracking_events' => $shipment->trackingEvents->sortByDesc('occurred_at')->values(),
        ]);
    }

    /**
     * Add a tracking event to a shipment.
     */
    public function store(CargoShipment $shipment, Request $request): JsonResponse
    {
        $request->validate([
            'event' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
        ]);

        $event = TrackingEvent::create([
            'cargo_shipment_id' => $shipment->id,
            'event' => $request->event,
            'description' => $request->description,
            'location' => $request->location,
            'occurred_at' => now(),
        ]);

        return response()->json([
            'message' => 'Tracking event added.',
            'event' => $event,
        ], 201);
    }

    /**
     * Mark a shipment as delivered.
     */
    public function markDelivered(CargoShipment $shipment): JsonResponse
    {
        $shipment->update(['status' => 'delivered']);

        TrackingEvent::create([
            'cargo_shipment_id' => $shipment->id,
            'event' => 'delivered',
            'description' => 'Package has been delivered.',
            'occurred_at' => now(),
        ]);

        $shipment->order->update(['status' => 'delivered']);

        return response()->json([
            'message' => 'Shipment marked as delivered.',
            'shipment' => $shipment->fresh(),
        ]);
    }
}
