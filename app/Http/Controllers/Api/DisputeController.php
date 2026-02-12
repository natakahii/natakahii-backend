<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dispute;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DisputeController extends Controller
{
    /**
     * File a dispute for an order.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'order_id' => ['required', 'exists:orders,id'],
            'reason' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
        ]);

        $user = $request->user('api');

        $existing = Dispute::where('user_id', $user->id)
            ->where('order_id', $request->order_id)
            ->whereIn('status', ['open', 'under_review'])
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'An active dispute already exists for this order.',
            ], 422);
        }

        $dispute = Dispute::create([
            'user_id' => $user->id,
            'order_id' => $request->order_id,
            'reason' => $request->reason,
            'description' => $request->description,
            'status' => 'open',
        ]);

        return response()->json([
            'message' => 'Dispute filed.',
            'dispute' => $dispute->load('order'),
        ], 201);
    }
}
