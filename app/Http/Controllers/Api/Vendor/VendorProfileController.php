<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\StoreVendorProfileRequest;
use App\Http\Resources\VendorResource;
use App\Models\Vendor;
use App\Support\CdnHelper;
use Illuminate\Http\JsonResponse;

class VendorProfileController extends Controller
{
    /**
     * Create or update the authenticated user's vendor profile.
     */
    public function store(StoreVendorProfileRequest $request): JsonResponse
    {
        $user = $request->user('api');
        $vendor = $user->vendor;

        $data = [
            'shop_name' => $request->shop_name,
            'shop_slug' => $request->shop_slug,
            'description' => $request->description,
        ];

        if ($request->hasFile('logo')) {
            if ($vendor?->logo) {
                CdnHelper::delete($vendor->logo);
            }

            $data['logo'] = CdnHelper::upload($request->file('logo'), 'vendors/logos');
        }

        if ($vendor) {
            $vendor->update($data);
        } else {
            $data['user_id'] = $user->id;
            $data['status'] = 'pending';
            $vendor = Vendor::create($data);

            $user->assignRole('vendor');
        }

        return response()->json([
            'message' => $vendor->wasRecentlyCreated ? 'Vendor profile created. Awaiting approval.' : 'Vendor profile updated.',
            'vendor' => new VendorResource($vendor->load('user')),
        ], $vendor->wasRecentlyCreated ? 201 : 200);
    }
}
