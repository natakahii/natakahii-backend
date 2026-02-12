<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RefundAdminController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'order_id' => ['required', 'exists:orders,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'reason' => ['required', 'string'],
        ]);

        $order = Order::findOrFail($request->order_id);

        $payment = Payment::create([
            'order_id' => $order->id,
            'payment_method' => 'refund',
            'amount' => $request->amount,
            'status' => 'success',
            'transaction_id' => 'REF-'.strtoupper(uniqid()),
        ]);

        return response()->json([
            'message' => 'Refund processed.',
            'payment' => $payment,
        ], 201);
    }
}
