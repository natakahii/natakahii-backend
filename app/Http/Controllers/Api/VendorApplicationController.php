<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVendorApplicationRequest;
use App\Models\VendorApplication;
use App\Notifications\VendorApplicationApproved;
use App\Notifications\VendorApplicationRejected;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VendorApplicationController extends Controller
{
   
    public function status(): JsonResponse
    {
        $user = auth('api')->user();
        $application = $user->vendorApplication();

        return response()->json([
            'has_application' => $application->exists(),
            'application' => $application->first(),
        ]);
    }

    /**
     * Submit a vendor application (user).
     */
    public function store(StoreVendorApplicationRequest $request): JsonResponse
    {
        $user = auth('api')->user();

        // Check if user already has an active application
        $existingApplication = VendorApplication::where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if ($existingApplication) {
            return response()->json([
                'message' => 'You already have a pending vendor application.',
                'application' => $existingApplication,
            ], 422);
        }

        // Check if user is already a vendor
        if ($user->vendor()->exists()) {
            return response()->json([
                'message' => 'You are already a vendor.',
            ], 422);
        }

        $application = VendorApplication::create([
            'user_id' => $user->id,
            ...$request->validated(),
        ]);

        return response()->json([
            'message' => 'Vendor application submitted successfully.',
            'application' => $application,
        ], 201);
    }

    /**
     * Get all vendor applications (admin).
     */
    public function index(Request $request): JsonResponse
    {
        $query = VendorApplication::with('user');

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Search by business name or user email
        if ($request->search) {
            $search = "%{$request->search}%";
            $query->where(function ($q) use ($search) {
                $q->where('business_name', 'like', $search)
                    ->orWhere('business_email', 'like', $search)
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('email', 'like', $search);
                    });
            });
        }

        // Sort
        $sortBy = $request->sort_by ?? 'created_at';
        $sortOrder = $request->sort_order ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        // Paginate
        $applications = $query->paginate($request->per_page ?? 15);

        return response()->json([
            'data' => $applications->items(),
            'pagination' => [
                'total' => $applications->total(),
                'per_page' => $applications->perPage(),
                'current_page' => $applications->currentPage(),
                'last_page' => $applications->lastPage(),
            ],
        ]);
    }

    /**
     * Get vendor application details (admin).
     */
    public function show(VendorApplication $application): JsonResponse
    {
        return response()->json([
            'application' => $application->load('user'),
        ]);
    }

    /**
     * Update vendor application status (admin).
     */
    public function updateStatus(Request $request, VendorApplication $application): JsonResponse
    {
        $request->validate([
            'status' => ['required', 'in:approved,rejected'],
            'rejection_reason' => ['nullable', 'string', 'max:500'],
        ]);

        $application->update([
            'status' => $request->status,
            'rejection_reason' => $request->rejection_reason ?? null,
        ]);

        $user = $application->user;

        // If approved, create a vendor account and send approval email
        if ($request->status === 'approved') {
            $vendor = $user->vendor()->firstOrCreate(
                ['user_id' => $user->id],
                [
                    'shop_name' => $application->business_name,
                    'shop_slug' => \Illuminate\Support\Str::slug($application->business_name . '-' . $user->id),
                    'description' => $application->description,
                    'status' => 'approved',
                ]
            );

            $user->assignRole('vendor');

            // Send approval email asynchronously
            try {
                $user->notify(new VendorApplicationApproved($application));
            } catch (\Exception $e) {
                \Log::error('Failed to send vendor approval email', [
                    'user_id' => $user->id,
                    'application_id' => $application->id,
                    'error' => $e->getMessage(),
                ]);
            }
        } else if ($request->status === 'rejected') {
            // Send rejection email with reason asynchronously
            try {
                $user->notify(new VendorApplicationRejected($application));
            } catch (\Exception $e) {
                \Log::error('Failed to send vendor rejection email', [
                    'user_id' => $user->id,
                    'application_id' => $application->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return response()->json([
            'message' => 'Application status updated.',
            'application' => $application,
        ]);
    }
}
