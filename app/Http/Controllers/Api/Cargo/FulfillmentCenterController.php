<?php

namespace App\Http\Controllers\Api\Cargo;

use App\Http\Controllers\Controller;
use App\Models\FulfillmentCenter;
use Illuminate\Http\JsonResponse;

class FulfillmentCenterController extends Controller
{
    /**
     * List active fulfillment centers.
     */
    public function index(): JsonResponse
    {
        $centers = FulfillmentCenter::where('is_active', true)
            ->orderBy('name')
            ->get();

        return response()->json([
            'fulfillment_centers' => $centers,
        ]);
    }
}
