<?php

namespace App\Http\Controllers\Api\Admin\Super;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminAccountController extends Controller
{
    public function index(): JsonResponse
    {
        $admins = User::whereHas('roles', fn ($q) => $q->where('name', 'admin'))
            ->with('roles')
            ->get();

        return response()->json([
            'admins' => UserResource::collection($admins),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'status' => 'active',
        ]);

        $user->assignRole('admin');

        return response()->json([
            'message' => 'Admin account created.',
            'admin' => new UserResource($user->load('roles')),
        ], 201);
    }

    public function update(User $user, Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'unique:users,email,'.$user->id],
        ]);

        $user->update($request->only(['name', 'email']));

        return response()->json([
            'message' => 'Admin updated.',
            'admin' => new UserResource($user->fresh('roles')),
        ]);
    }

    public function destroy(User $user, Request $request): JsonResponse
    {
        if ($user->id === $request->user('api')->id) {
            return response()->json(['message' => 'Cannot delete your own account.'], 403);
        }

        $user->roles()->detach();
        $user->delete();

        return response()->json(['message' => 'Admin account deleted.']);
    }
}
