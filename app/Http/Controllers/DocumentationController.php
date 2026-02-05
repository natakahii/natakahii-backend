<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class DocumentationController extends Controller
{
    public function index(): View
    {
        $endpoints = [
            [
                'group' => 'Authentication',
                'endpoints' => [
                    [
                        'name' => 'Register',
                        'method' => 'POST',
                        'url' => '/api/v1/auth/register',
                        'description' => 'Initiate user registration. Sends OTP to email for verification.',
                        'auth_required' => false,
                        'request' => [
                            'name' => 'string|required|max:255',
                            'email' => 'string|required|email|unique',
                            'password' => 'string|required|min:8|mixed_case|numbers',
                            'phone' => 'string|nullable|max:20',
                        ],
                        'response' => [
                            'message' => 'Registration initiated. Please check your email for OTP verification.',
                            'email' => 'user@example.com',
                        ],
                    ],
                    [
                        'name' => 'Verify Registration',
                        'method' => 'POST',
                        'url' => '/api/v1/auth/verify-registration',
                        'description' => 'Complete registration by verifying OTP sent to email.',
                        'auth_required' => false,
                        'request' => [
                            'email' => 'string|required|email',
                            'otp' => 'string|required|size:6',
                            'type' => 'string|required|in:registration',
                        ],
                        'response' => [
                            'message' => 'Registration successful.',
                            'user' => ['id' => 1, 'name' => 'John Doe', 'email' => 'user@example.com'],
                            'token' => 'eyJ0eXAiOiJKV1QiLCJhbGc...',
                            'token_type' => 'bearer',
                            'expires_in' => 3600,
                        ],
                    ],
                    [
                        'name' => 'Login',
                        'method' => 'POST',
                        'url' => '/api/v1/auth/login',
                        'description' => 'Authenticate user and receive JWT token.',
                        'auth_required' => false,
                        'request' => [
                            'email' => 'string|required|email',
                            'password' => 'string|required',
                        ],
                        'response' => [
                            'message' => 'Login successful.',
                            'user' => ['id' => 1, 'name' => 'John Doe', 'email' => 'user@example.com'],
                            'token' => 'eyJ0eXAiOiJKV1QiLCJhbGc...',
                            'token_type' => 'bearer',
                            'expires_in' => 3600,
                        ],
                    ],
                    [
                        'name' => 'Logout',
                        'method' => 'POST',
                        'url' => '/api/v1/auth/logout',
                        'description' => 'Invalidate current JWT token.',
                        'auth_required' => true,
                        'request' => [],
                        'response' => [
                            'message' => 'Successfully logged out.',
                        ],
                    ],
                    [
                        'name' => 'Refresh Token',
                        'method' => 'POST',
                        'url' => '/api/v1/auth/refresh',
                        'description' => 'Refresh JWT token to extend session.',
                        'auth_required' => true,
                        'request' => [],
                        'response' => [
                            'token' => 'eyJ0eXAiOiJKV1QiLCJhbGc...',
                            'token_type' => 'bearer',
                            'expires_in' => 3600,
                        ],
                    ],
                    [
                        'name' => 'Get Current User',
                        'method' => 'GET',
                        'url' => '/api/v1/auth/me',
                        'description' => 'Get authenticated user information.',
                        'auth_required' => true,
                        'request' => [],
                        'response' => [
                            'user' => ['id' => 1, 'name' => 'John Doe', 'email' => 'user@example.com', 'roles' => []],
                        ],
                    ],
                    [
                        'name' => 'Forgot Password',
                        'method' => 'POST',
                        'url' => '/api/v1/auth/forgot-password',
                        'description' => 'Request password reset OTP.',
                        'auth_required' => false,
                        'request' => [
                            'email' => 'string|required|email|exists:users',
                        ],
                        'response' => [
                            'message' => 'Password reset OTP sent to your email.',
                        ],
                    ],
                    [
                        'name' => 'Reset Password',
                        'method' => 'POST',
                        'url' => '/api/v1/auth/reset-password',
                        'description' => 'Reset password using OTP.',
                        'auth_required' => false,
                        'request' => [
                            'email' => 'string|required|email|exists:users',
                            'otp' => 'string|required|size:6',
                            'password' => 'string|required|min:8|mixed_case|numbers|confirmed',
                        ],
                        'response' => [
                            'message' => 'Password reset successful. You can now login with your new password.',
                        ],
                    ],
                    [
                        'name' => 'Resend OTP',
                        'method' => 'POST',
                        'url' => '/api/v1/auth/resend-otp',
                        'description' => 'Resend OTP for verification.',
                        'auth_required' => false,
                        'request' => [
                            'email' => 'string|required|email',
                            'type' => 'string|required|in:registration,password_reset,email_verification',
                        ],
                        'response' => [
                            'message' => 'OTP resent successfully.',
                        ],
                    ],
                ],
            ],
        ];

        return view('documentation', compact('endpoints'));
    }
}
