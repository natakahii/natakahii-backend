<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\CargoShipment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShipmentAdminController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = CargoShipment::with('order.user', 'fulfillmentCenter');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $shipments = $query->latest()->paginate($request->input('per_page', 15));

        return response()->json([
            'shipments' => $shipments,
        ]);
    }

    public function inspections(CargoShipment $shipment): JsonResponse
    {
        $shipment->load('trackingEvents');

        return response()->json([
            'shipment' => $shipment,
            'tracking_events' => $shipment->trackingEvents,
        ]);
    }
}
