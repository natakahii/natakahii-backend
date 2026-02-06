<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AssignRoleRequest;
use App\Http\Requests\Admin\UpdateUserStatusRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    /**
     * List all users with optional filters and pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::query()->with('roles');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('role')) {
            $query->whereHas('roles', function ($q) use ($request): void {
                $q->where('name', $request->input('role'));
            });
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate($request->input('per_page', 15));

        return response()->json([
            'message' => 'Users retrieved.',
            'data' => $users,
        ], 200);
    }

    /**
     * Show a single user with their roles.
     */
    public function show(User $user): JsonResponse
    {
        return response()->json([
            'message' => 'User retrieved.',
            'data' => $user->load('roles'),
        ], 200);
    }

    /**
     * Update a user's status (active / blocked).
     */
    public function updateStatus(UpdateUserStatusRequest $request, User $user): JsonResponse
    {
        if ($user->id === auth('api')->id()) {
            return response()->json([
                'message' => 'You cannot change your own status.',
            ], 403);
        }

        $user->update(['status' => $request->validated('status')]);

        return response()->json([
            'message' => "User status updated to {$user->status}.",
            'data' => $user->load('roles'),
        ], 200);
    }

    /**
     * Assign a role to a user.
     */
    public function assignRole(AssignRoleRequest $request, User $user): JsonResponse
    {
        $roleName = $request->validated('role');
        $role = Role::where('name', $roleName)->firstOrFail();

        $user->roles()->syncWithoutDetaching([$role->id]);

        return response()->json([
            'message' => "Role '{$roleName}' assigned to {$user->name}.",
            'data' => $user->load('roles'),
        ], 200);
    }

    /**
     * Revoke a role from a user.
     */
    public function revokeRole(Request $request, User $user): JsonResponse
    {
        $request->validate([
            'role' => ['required', 'string', 'exists:roles,name'],
        ]);

        if ($user->id === auth('api')->id() && $request->input('role') === 'admin') {
            return response()->json([
                'message' => 'You cannot revoke your own admin role.',
            ], 403);
        }

        $role = Role::where('name', $request->input('role'))->firstOrFail();

        $user->roles()->detach($role->id);

        return response()->json([
            'message' => "Role '{$request->input('role')}' revoked from {$user->name}.",
            'data' => $user->load('roles'),
        ], 200);
    }

    /**
     * List all available roles.
     */
    public function roles(): JsonResponse
    {
        return response()->json([
            'message' => 'Roles retrieved.',
            'data' => Role::all(['id', 'name', 'description']),
        ], 200);
    }
}
