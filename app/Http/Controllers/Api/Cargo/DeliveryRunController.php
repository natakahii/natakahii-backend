<?php

namespace App\Http\Controllers\Api\Cargo;

use App\Http\Controllers\Controller;
use App\Models\DeliveryRun;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeliveryRunController extends Controller
{
    /**
     * List delivery runs assigned to the current delivery agent.
     */
    public function myRuns(Request $request): JsonResponse
    {
        $user = $request->user('api');

        $runs = DeliveryRun::where('assigned_to', $user->id)
            ->with('shipments', 'fulfillmentCenter')
            ->latest()
            ->paginate($request->input('per_page', 15));

        return response()->json([
            'delivery_runs' => $runs,
        ]);
    }

    /**
     * Create a delivery run (admin).
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'assigned_to' => ['nullable', 'exists:users,id'],
            'fulfillment_center_id' => ['nullable', 'exists:fulfillment_centers,id'],
            'scheduled_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $run = DeliveryRun::create($request->only([
            'assigned_to', 'fulfillment_center_id', 'scheduled_date', 'notes',
        ]));

        return response()->json([
            'message' => 'Delivery run created.',
            'delivery_run' => $run,
        ], 201);
    }

    /**
     * Assign shipments to a delivery run.
     */
    public function assignShipments(DeliveryRun $run, Request $request): JsonResponse
    {
        $request->validate([
            'shipment_ids' => ['required', 'array'],
            'shipment_ids.*' => ['exists:cargo_shipments,id'],
        ]);

        $run->shipments()->syncWithoutDetaching($request->shipment_ids);

        return response()->json([
            'message' => 'Shipments assigned.',
            'delivery_run' => $run->load('shipments'),
        ]);
    }

    /**
     * Dispatch a delivery run.
     */
    public function dispatch(DeliveryRun $run): JsonResponse
    {
        $run->update(['status' => 'dispatched']);

        return response()->json([
            'message' => 'Delivery run dispatched.',
            'delivery_run' => $run->fresh(),
        ]);
    }
}
