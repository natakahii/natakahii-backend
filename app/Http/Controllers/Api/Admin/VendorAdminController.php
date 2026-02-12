<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\VendorResource;
use App\Models\Vendor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VendorAdminController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Vendor::with('user')->withCount('products');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $vendors = $query->latest()->paginate($request->input('per_page', 15));

        return response()->json([
            'vendors' => VendorResource::collection($vendors),
            'meta' => [
                'current_page' => $vendors->currentPage(),
                'last_page' => $vendors->lastPage(),
                'per_page' => $vendors->perPage(),
                'total' => $vendors->total(),
            ],
        ]);
    }

    public function reviewVerification(Vendor $vendor, Request $request): JsonResponse
    {
        $request->validate(['status' => ['required', 'in:approved,suspended']]);
        $vendor->update(['status' => $request->status]);

        return response()->json([
            'message' => "Vendor {$request->status}.",
            'vendor' => new VendorResource($vendor->fresh('user')),
        ]);
    }
}
