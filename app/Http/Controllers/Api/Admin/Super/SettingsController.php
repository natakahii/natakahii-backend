<?php

namespace App\Http\Controllers\Api\Admin\Super;

use App\Http\Controllers\Controller;
use App\Models\PlatformSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function show(): JsonResponse
    {
        $settings = PlatformSetting::all()->groupBy('group');

        return response()->json(['settings' => $settings]);
    }

    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'settings' => ['required', 'array'],
            'settings.*.key' => ['required', 'string'],
            'settings.*.value' => ['nullable', 'string'],
            'settings.*.group' => ['nullable', 'string'],
        ]);

        foreach ($request->settings as $setting) {
            PlatformSetting::setValue(
                $setting['key'],
                $setting['value'] ?? null,
                $setting['group'] ?? 'general'
            );
        }

        return response()->json(['message' => 'Settings updated.']);
    }
}
