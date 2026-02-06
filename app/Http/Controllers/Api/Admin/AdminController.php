<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Return high-level platform statistics.
     */
    public function dashboard(): JsonResponse
    {
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('status', 'active')->count(),
            'blocked_users' => User::where('status', 'blocked')->count(),
            'total_vendors' => DB::table('vendors')->count(),
            'active_vendors' => DB::table('vendors')->where('status', 'active')->count(),
            'total_products' => DB::table('products')->whereNull('deleted_at')->count(),
            'total_orders' => DB::table('orders')->count(),
            'total_revenue' => (float) DB::table('orders')
                ->where('payment_status', 'paid')
                ->sum('total_amount'),
            'pending_orders' => DB::table('orders')->where('status', 'pending')->count(),
        ];

        return response()->json([
            'message' => 'Dashboard statistics retrieved.',
            'data' => $stats,
        ], 200);
    }
}
