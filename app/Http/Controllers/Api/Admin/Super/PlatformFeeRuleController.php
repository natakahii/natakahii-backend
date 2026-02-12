<?php

namespace App\Http\Controllers\Api\Admin\Super;

use App\Http\Controllers\Controller;
use App\Models\PlatformFeeRule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlatformFeeRuleController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:percentage,flat'],
            'value' => ['required', 'numeric', 'min:0'],
            'applies_to' => ['nullable', 'string'],
        ]);

        $rule = PlatformFeeRule::create($request->only(['name', 'type', 'value', 'applies_to']));

        return response()->json([
            'message' => 'Platform fee rule created.',
            'rule' => $rule,
        ], 201);
    }
}
