<?php

use App\Http\Controllers\Api\Admin\AdminController;
use App\Http\Controllers\Api\Admin\UserManagementController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // ──────────────────────────────────────
    // Public authentication
    // ──────────────────────────────────────
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/verify-registration', [AuthController::class, 'verifyRegistration']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);
        Route::post('/resend-otp', [AuthController::class, 'resendOtp']);

        Route::middleware('auth:api')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::post('/refresh', [AuthController::class, 'refresh']);
            Route::get('/me', [AuthController::class, 'me']);
        });
    });

    // ──────────────────────────────────────
    // Admin endpoints (requires auth + admin role)
    // ──────────────────────────────────────
    Route::prefix('admin')
        ->middleware(['auth:api', 'role:admin'])
        ->group(function () {
            Route::get('/dashboard', [AdminController::class, 'dashboard']);

            // User management
            Route::get('/users', [UserManagementController::class, 'index']);
            Route::get('/users/{user}', [UserManagementController::class, 'show']);
            Route::patch('/users/{user}/status', [UserManagementController::class, 'updateStatus']);
            Route::post('/users/{user}/assign-role', [UserManagementController::class, 'assignRole']);
            Route::delete('/users/{user}/revoke-role', [UserManagementController::class, 'revokeRole']);

            // Roles
            Route::get('/roles', [UserManagementController::class, 'roles']);
        });
});
