<?php

namespace App\Http\Controllers\Api\Social;

use App\Http\Controllers\Controller;
use App\Http\Resources\VendorResource;
use App\Models\Vendor;
use App\Models\VendorFollow;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VendorFollowController extends Controller
{
    /**
     * Follow a vendor.
     */
    public function store(Vendor $vendor, Request $request): JsonResponse
    {
        $user = $request->user('api');

        VendorFollow::firstOrCreate([
            'user_id' => $user->id,
            'vendor_id' => $vendor->id,
        ]);

        return response()->json([
            'message' => 'Now following '.$vendor->shop_name.'.',
            'followers_count' => $vendor->followers()->count(),
        ]);
    }

    /**
     * Unfollow a vendor.
     */
    public function destroy(Vendor $vendor, Request $request): JsonResponse
    {
        $user = $request->user('api');

        VendorFollow::where('user_id', $user->id)
            ->where('vendor_id', $vendor->id)
            ->delete();

        return response()->json([
            'message' => 'Unfollowed '.$vendor->shop_name.'.',
            'followers_count' => $vendor->followers()->count(),
        ]);
    }

    /**
     * List vendors the authenticated user follows.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user('api');

        $vendors = Vendor::query()
            ->whereHas('followers', fn ($q) => $q->where('user_id', $user->id))
            ->withCount('products')
            ->paginate($request->input('per_page', 15));

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
}
