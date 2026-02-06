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
                'description' => 'Endpoints for user registration, login, token management and password recovery.',
                'endpoints' => [
                    [
                        'name' => 'Register',
                        'method' => 'POST',
                        'url' => '/api/v1/auth/register',
                        'description' => 'Initiate user registration. Sends OTP to email for verification.',
                        'auth_required' => false,
                        'request' => [
                            ['name' => 'name', 'type' => 'string', 'required' => true, 'rules' => 'max:255', 'description' => 'Full name of the user.'],
                            ['name' => 'email', 'type' => 'string', 'required' => true, 'rules' => 'email|unique:users', 'description' => 'Valid email address. Must be unique.'],
                            ['name' => 'password', 'type' => 'string', 'required' => true, 'rules' => 'min:8|mixed_case|numbers', 'description' => 'Password with uppercase, lowercase and numbers.'],
                            ['name' => 'phone', 'type' => 'string', 'required' => false, 'rules' => 'max:20', 'description' => 'Phone number (optional).'],
                        ],
                        'headers' => [],
                        'success_response' => [
                            'status' => 200,
                            'body' => [
                                'message' => 'Registration initiated. Please check your email for OTP verification.',
                                'email' => 'user@example.com',
                            ],
                        ],
                        'error_responses' => [
                            [
                                'status' => 422,
                                'description' => 'Validation failed',
                                'body' => [
                                    'message' => 'The email has already been taken.',
                                    'errors' => ['email' => ['The email has already been taken.']],
                                ],
                            ],
                        ],
                    ],
                    [
                        'name' => 'Verify Registration',
                        'method' => 'POST',
                        'url' => '/api/v1/auth/verify-registration',
                        'description' => 'Complete registration by verifying OTP sent to email.',
                        'auth_required' => false,
                        'request' => [
                            ['name' => 'email', 'type' => 'string', 'required' => true, 'rules' => 'email', 'description' => 'Email used during registration.'],
                            ['name' => 'otp', 'type' => 'string', 'required' => true, 'rules' => 'size:6', 'description' => 'Six-digit OTP code received via email.'],
                        ],
                        'headers' => [],
                        'success_response' => [
                            'status' => 201,
                            'body' => [
                                'message' => 'Registration successful.',
                                'user' => ['id' => 1, 'name' => 'John Doe', 'email' => 'user@example.com'],
                                'token' => 'eyJ0eXAiOiJKV1QiLCJhbGc...',
                                'token_type' => 'bearer',
                                'expires_in' => 3600,
                            ],
                        ],
                        'error_responses' => [
                            [
                                'status' => 400,
                                'description' => 'Invalid or expired OTP',
                                'body' => ['message' => 'Invalid or expired OTP.'],
                            ],
                            [
                                'status' => 404,
                                'description' => 'Registration data expired',
                                'body' => ['message' => 'Registration data not found or expired. Please register again.'],
                            ],
                        ],
                    ],
                    [
                        'name' => 'Login',
                        'method' => 'POST',
                        'url' => '/api/v1/auth/login',
                        'description' => 'Authenticate user and receive JWT token.',
                        'auth_required' => false,
                        'request' => [
                            ['name' => 'email', 'type' => 'string', 'required' => true, 'rules' => 'email', 'description' => 'Registered email address.'],
                            ['name' => 'password', 'type' => 'string', 'required' => true, 'rules' => '', 'description' => 'Account password.'],
                        ],
                        'headers' => [],
                        'success_response' => [
                            'status' => 200,
                            'body' => [
                                'message' => 'Login successful.',
                                'user' => ['id' => 1, 'name' => 'John Doe', 'email' => 'user@example.com'],
                                'token' => 'eyJ0eXAiOiJKV1QiLCJhbGc...',
                                'token_type' => 'bearer',
                                'expires_in' => 3600,
                            ],
                        ],
                        'error_responses' => [
                            [
                                'status' => 401,
                                'description' => 'Invalid credentials',
                                'body' => ['message' => 'Invalid email or password.'],
                            ],
                            [
                                'status' => 403,
                                'description' => 'Account blocked',
                                'body' => ['message' => 'Your account has been blocked. Please contact support.'],
                            ],
                        ],
                    ],
                    [
                        'name' => 'Logout',
                        'method' => 'POST',
                        'url' => '/api/v1/auth/logout',
                        'description' => 'Invalidate current JWT token.',
                        'auth_required' => true,
                        'request' => [],
                        'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => [
                            'status' => 200,
                            'body' => ['message' => 'Successfully logged out.'],
                        ],
                        'error_responses' => [
                            [
                                'status' => 401,
                                'description' => 'Unauthenticated',
                                'body' => ['message' => 'Unauthenticated.'],
                            ],
                        ],
                    ],
                    [
                        'name' => 'Refresh Token',
                        'method' => 'POST',
                        'url' => '/api/v1/auth/refresh',
                        'description' => 'Refresh JWT token to extend session.',
                        'auth_required' => true,
                        'request' => [],
                        'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => [
                            'status' => 200,
                            'body' => [
                                'token' => 'eyJ0eXAiOiJKV1QiLCJhbGc...',
                                'token_type' => 'bearer',
                                'expires_in' => 3600,
                            ],
                        ],
                        'error_responses' => [
                            [
                                'status' => 401,
                                'description' => 'Unauthenticated',
                                'body' => ['message' => 'Unauthenticated.'],
                            ],
                        ],
                    ],
                    [
                        'name' => 'Get Current User',
                        'method' => 'GET',
                        'url' => '/api/v1/auth/me',
                        'description' => 'Get authenticated user information with roles.',
                        'auth_required' => true,
                        'request' => [],
                        'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => [
                            'status' => 200,
                            'body' => [
                                'user' => [
                                    'id' => 1,
                                    'name' => 'John Doe',
                                    'email' => 'user@example.com',
                                    'phone' => '+1234567890',
                                    'status' => 'active',
                                    'roles' => [['id' => 1, 'name' => 'customer']],
                                ],
                            ],
                        ],
                        'error_responses' => [
                            [
                                'status' => 401,
                                'description' => 'Unauthenticated',
                                'body' => ['message' => 'Unauthenticated.'],
                            ],
                        ],
                    ],
                    [
                        'name' => 'Forgot Password',
                        'method' => 'POST',
                        'url' => '/api/v1/auth/forgot-password',
                        'description' => 'Request password reset OTP.',
                        'auth_required' => false,
                        'request' => [
                            ['name' => 'email', 'type' => 'string', 'required' => true, 'rules' => 'email|exists:users', 'description' => 'Registered email address.'],
                        ],
                        'headers' => [],
                        'success_response' => [
                            'status' => 200,
                            'body' => ['message' => 'Password reset OTP sent to your email.'],
                        ],
                        'error_responses' => [
                            [
                                'status' => 422,
                                'description' => 'Validation failed',
                                'body' => [
                                    'message' => 'The selected email is invalid.',
                                    'errors' => ['email' => ['The selected email is invalid.']],
                                ],
                            ],
                        ],
                    ],
                    [
                        'name' => 'Reset Password',
                        'method' => 'POST',
                        'url' => '/api/v1/auth/reset-password',
                        'description' => 'Reset password using OTP.',
                        'auth_required' => false,
                        'request' => [
                            ['name' => 'email', 'type' => 'string', 'required' => true, 'rules' => 'email|exists:users', 'description' => 'Registered email address.'],
                            ['name' => 'otp', 'type' => 'string', 'required' => true, 'rules' => 'size:6', 'description' => 'Six-digit OTP code from email.'],
                            ['name' => 'password', 'type' => 'string', 'required' => true, 'rules' => 'min:8|mixed_case|numbers|confirmed', 'description' => 'New password.'],
                            ['name' => 'password_confirmation', 'type' => 'string', 'required' => true, 'rules' => '', 'description' => 'Must match password field.'],
                        ],
                        'headers' => [],
                        'success_response' => [
                            'status' => 200,
                            'body' => ['message' => 'Password reset successful. You can now login with your new password.'],
                        ],
                        'error_responses' => [
                            [
                                'status' => 400,
                                'description' => 'Invalid or expired OTP',
                                'body' => ['message' => 'Invalid or expired OTP.'],
                            ],
                        ],
                    ],
                    [
                        'name' => 'Resend OTP',
                        'method' => 'POST',
                        'url' => '/api/v1/auth/resend-otp',
                        'description' => 'Resend OTP for verification.',
                        'auth_required' => false,
                        'request' => [
                            ['name' => 'email', 'type' => 'string', 'required' => true, 'rules' => 'email', 'description' => 'Email to resend OTP to.'],
                            ['name' => 'type', 'type' => 'string', 'required' => true, 'rules' => 'in:registration,password_reset,email_verification', 'description' => 'OTP purpose type.'],
                        ],
                        'headers' => [],
                        'success_response' => [
                            'status' => 200,
                            'body' => ['message' => 'OTP resent successfully.'],
                        ],
                        'error_responses' => [
                            [
                                'status' => 429,
                                'description' => 'Too many requests',
                                'body' => ['message' => 'Please wait before requesting a new OTP.'],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        return view('documentation', compact('endpoints'));
    }
}
