<?php

namespace App\Http\Controllers\Api\Cargo;

use App\Http\Controllers\Controller;
use App\Models\VendorDropoff;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VendorDropoffController extends Controller
{
    /**
     * List dropoffs (for cargo agents/admin).
     */
    public function index(Request $request): JsonResponse
    {
        $query = VendorDropoff::with('vendor', 'order', 'fulfillmentCenter');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $dropoffs = $query->latest()->paginate($request->input('per_page', 15));

        return response()->json([
            'dropoffs' => $dropoffs,
        ]);
    }

    /**
     * Receive a dropoff at the fulfillment center.
     */
    public function receive(VendorDropoff $dropoff, Request $request): JsonResponse
    {
        $request->validate([
            'fulfillment_center_id' => ['required', 'exists:fulfillment_centers,id'],
        ]);

        $dropoff->update([
            'status' => 'received',
            'fulfillment_center_id' => $request->fulfillment_center_id,
        ]);

        return response()->json([
            'message' => 'Dropoff received.',
            'dropoff' => $dropoff->fresh('vendor', 'fulfillmentCenter'),
        ]);
    }

    /**
     * Update dropoff item notes.
     */
    public function updateItems(VendorDropoff $dropoff, Request $request): JsonResponse
    {
        $request->validate([
            'notes' => ['nullable', 'string'],
        ]);

        $dropoff->update($request->only('notes'));

        return response()->json([
            'message' => 'Dropoff updated.',
            'dropoff' => $dropoff->fresh(),
        ]);
    }

    /**
     * Start QC inspection.
     */
    public function qcStart(VendorDropoff $dropoff): JsonResponse
    {
        $dropoff->update(['status' => 'qc_in_progress']);

        return response()->json([
            'message' => 'QC started.',
            'dropoff' => $dropoff->fresh(),
        ]);
    }

    /**
     * Complete QC inspection.
     */
    public function qcComplete(VendorDropoff $dropoff, Request $request): JsonResponse
    {
        $request->validate([
            'passed' => ['required', 'boolean'],
            'notes' => ['nullable', 'string'],
        ]);

        $dropoff->update([
            'status' => $request->passed ? 'qc_passed' : 'qc_failed',
            'notes' => $request->notes ?? $dropoff->notes,
        ]);

        return response()->json([
            'message' => 'QC completed.',
            'dropoff' => $dropoff->fresh(),
        ]);
    }
}
