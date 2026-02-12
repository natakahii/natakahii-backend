<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dispute;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DisputeAdminController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Dispute::with('user', 'order');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $disputes = $query->latest()->paginate($request->input('per_page', 15));

        return response()->json(['disputes' => $disputes]);
    }

    public function resolve(Dispute $dispute, Request $request): JsonResponse
    {
        $request->validate([
            'status' => ['required', 'in:resolved,rejected'],
            'resolution' => ['required', 'string'],
        ]);

        $dispute->update([
            'status' => $request->status,
            'resolution' => $request->resolution,
        ]);

        return response()->json([
            'message' => "Dispute {$request->status}.",
            'dispute' => $dispute->fresh('user', 'order'),
        ]);
    }
}
