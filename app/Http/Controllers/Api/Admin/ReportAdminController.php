<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportAdminController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'reports' => [
                'total_users' => User::count(),
                'total_vendors' => Vendor::count(),
                'total_products' => Product::count(),
                'total_orders' => Order::count(),
                'total_revenue' => Payment::where('status', 'success')->sum('amount'),
            ],
        ]);
    }

    public function action(int $report, Request $request): JsonResponse
    {
        return response()->json([
            'message' => 'Report action processed.',
        ]);
    }
}
