<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dispute;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupportAdminController extends Controller
{
    public function tickets(Request $request): JsonResponse
    {
        $tickets = Dispute::with('user', 'order')
            ->whereIn('status', ['open', 'under_review'])
            ->latest()
            ->paginate($request->input('per_page', 15));

        return response()->json(['tickets' => $tickets]);
    }
}
