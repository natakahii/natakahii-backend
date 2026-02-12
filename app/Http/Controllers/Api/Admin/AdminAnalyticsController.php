<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\JsonResponse;

class AdminAnalyticsController extends Controller
{
    public function overview(): JsonResponse
    {
        return response()->json([
            'analytics' => [
                'users' => User::count(),
                'vendors' => Vendor::where('status', 'approved')->count(),
                'products' => Product::where('status', 'active')->count(),
                'orders_today' => Order::whereDate('created_at', today())->count(),
                'orders_total' => Order::count(),
                'revenue_total' => Payment::where('status', 'success')->sum('amount'),
            ],
        ]);
    }
}
