<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\JsonResponse;

class EscrowAdminController extends Controller
{
    public function showOrder(Order $order): JsonResponse
    {
        $order->load('items.vendor', 'payments', 'user');

        return response()->json([
            'order' => new OrderResource($order),
            'escrow_status' => $order->payment_status,
        ]);
    }
}
