<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function vendorOverview(Request $request): JsonResponse
    {
        $vendor = $request->user('api')->vendor;

        if (! $vendor) {
            return response()->json(['message' => 'No vendor profile found.'], 404);
        }

        $totalProducts = Product::where('vendor_id', $vendor->id)->count();
        $activeProducts = Product::where('vendor_id', $vendor->id)->where('status', 'active')->count();
        $totalOrders = OrderItem::where('vendor_id', $vendor->id)->distinct('order_id')->count('order_id');
        $totalRevenue = OrderItem::where('vendor_id', $vendor->id)
            ->selectRaw('SUM(price * quantity) as total')
            ->value('total') ?? 0;

        return response()->json([
            'analytics' => [
                'total_products' => $totalProducts,
                'active_products' => $activeProducts,
                'total_orders' => $totalOrders,
                'total_revenue' => round($totalRevenue, 2),
            ],
        ]);
    }
}
