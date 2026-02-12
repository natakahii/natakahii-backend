<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentResource;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Initiate a payment for an order.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'order_id' => ['required', 'exists:orders,id'],
            'payment_method' => ['required', 'string', 'in:mpesa,card,bank_transfer'],
        ]);

        $user = $request->user('api');
        $order = Order::where('id', $request->order_id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if ($order->payment_status === 'paid') {
            return response()->json([
                'message' => 'This order has already been paid.',
            ], 422);
        }

        $payment = Payment::create([
            'order_id' => $order->id,
            'payment_method' => $request->payment_method,
            'amount' => $order->total_amount,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Payment initiated. Complete payment using the provided details.',
            'payment' => new PaymentResource($payment),
        ], 201);
    }

    /**
     * Handle payment provider webhook callbacks.
     */
    public function webhook(string $provider, Request $request): JsonResponse
    {
        $transactionId = $request->input('transaction_id');
        $status = $request->input('status');

        if (! $transactionId || ! $status) {
            return response()->json(['message' => 'Invalid webhook payload.'], 400);
        }

        $payment = Payment::where('transaction_id', $transactionId)->first();

        if (! $payment) {
            return response()->json(['message' => 'Payment not found.'], 404);
        }

        $newStatus = match ($status) {
            'success', 'completed' => 'success',
            'failed', 'cancelled' => 'failed',
            default => 'pending',
        };

        $payment->update(['status' => $newStatus]);

        if ($newStatus === 'success') {
            $payment->order->update([
                'payment_status' => 'paid',
                'status' => 'paid',
            ]);
        }

        return response()->json(['message' => 'Webhook processed.']);
    }
}
