<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Support\CdnHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserProfileController extends Controller
{
    /**
     * Update the authenticated user's profile information.
     */
    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:20'],
        ]);

        $user = $request->user('api');
        $user->update($request->only(['name', 'phone']));

        return response()->json([
            'message' => 'Profile updated.',
            'user' => new UserResource($user->load('roles')),
        ]);
    }

    /**
     * Upload or replace the authenticated user's profile photo.
     */
    public function updatePhoto(Request $request): JsonResponse
    {
        $request->validate([
            'photo' => ['required', 'image', 'max:5120'],
        ]);

        $user = $request->user('api');

        if ($user->profile_photo) {
            CdnHelper::delete($user->profile_photo);
        }

        $cdnUrl = CdnHelper::upload($request->file('photo'), 'users/avatars');

        $user->update(['profile_photo' => $cdnUrl]);

        return response()->json([
            'message' => 'Profile photo updated.',
            'user' => new UserResource($user->load('roles')),
        ]);
    }

    /**
     * Remove the authenticated user's profile photo.
     */
    public function destroyPhoto(Request $request): JsonResponse
    {
        $user = $request->user('api');

        if ($user->profile_photo) {
            CdnHelper::delete($user->profile_photo);
            $user->update(['profile_photo' => null]);
        }

        return response()->json([
            'message' => 'Profile photo removed.',
            'user' => new UserResource($user->load('roles')),
        ]);
    }
}
