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
            [
                'group' => 'Admin',
                'description' => 'Administrative endpoints for platform management. All routes require authentication and the admin role.',
                'endpoints' => [
                    [
                        'name' => 'Dashboard Statistics',
                        'method' => 'GET',
                        'url' => '/api/v1/admin/dashboard',
                        'description' => 'Retrieve high-level platform statistics including user, vendor, product, and order counts.',
                        'auth_required' => true,
                        'request' => [],
                        'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => [
                            'status' => 200,
                            'body' => [
                                'message' => 'Dashboard statistics retrieved.',
                                'data' => [
                                    'total_users' => 150,
                                    'active_users' => 142,
                                    'blocked_users' => 8,
                                    'total_vendors' => 25,
                                    'active_vendors' => 20,
                                    'total_products' => 340,
                                    'total_orders' => 1200,
                                    'total_revenue' => 58750.00,
                                    'pending_orders' => 15,
                                ],
                            ],
                        ],
                        'error_responses' => [
                            [
                                'status' => 401,
                                'description' => 'Unauthenticated',
                                'body' => ['message' => 'Unauthenticated.'],
                            ],
                            [
                                'status' => 403,
                                'description' => 'Forbidden — user does not have admin role',
                                'body' => ['message' => 'Forbidden. You do not have the required role.'],
                            ],
                        ],
                    ],
                    [
                        'name' => 'List Users',
                        'method' => 'GET',
                        'url' => '/api/v1/admin/users',
                        'description' => 'Retrieve a paginated list of all users. Supports filtering by status, role, and search keyword.',
                        'auth_required' => true,
                        'request' => [
                            ['name' => 'status', 'type' => 'string', 'required' => false, 'rules' => 'in:active,blocked', 'description' => 'Filter by user status.'],
                            ['name' => 'role', 'type' => 'string', 'required' => false, 'rules' => 'string', 'description' => 'Filter by role name (e.g. admin, vendor, customer).'],
                            ['name' => 'search', 'type' => 'string', 'required' => false, 'rules' => 'string', 'description' => 'Search by name or email.'],
                            ['name' => 'per_page', 'type' => 'integer', 'required' => false, 'rules' => 'integer|min:1|max:100', 'description' => 'Items per page. Default: 15.'],
                            ['name' => 'page', 'type' => 'integer', 'required' => false, 'rules' => 'integer|min:1', 'description' => 'Page number.'],
                        ],
                        'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => [
                            'status' => 200,
                            'body' => [
                                'message' => 'Users retrieved.',
                                'data' => [
                                    'current_page' => 1,
                                    'data' => [
                                        ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com', 'status' => 'active', 'roles' => [['id' => 3, 'name' => 'customer']]],
                                        ['id' => 2, 'name' => 'Jane Smith', 'email' => 'jane@example.com', 'status' => 'active', 'roles' => [['id' => 2, 'name' => 'vendor']]],
                                    ],
                                    'last_page' => 10,
                                    'per_page' => 15,
                                    'total' => 150,
                                ],
                            ],
                        ],
                        'error_responses' => [
                            [
                                'status' => 401,
                                'description' => 'Unauthenticated',
                                'body' => ['message' => 'Unauthenticated.'],
                            ],
                            [
                                'status' => 403,
                                'description' => 'Forbidden',
                                'body' => ['message' => 'Forbidden. You do not have the required role.'],
                            ],
                        ],
                    ],
                    [
                        'name' => 'Get User',
                        'method' => 'GET',
                        'url' => '/api/v1/admin/users/{user}',
                        'description' => 'Retrieve a single user by ID with their assigned roles.',
                        'auth_required' => true,
                        'request' => [],
                        'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => [
                            'status' => 200,
                            'body' => [
                                'message' => 'User retrieved.',
                                'data' => [
                                    'id' => 1,
                                    'name' => 'John Doe',
                                    'email' => 'john@example.com',
                                    'phone' => '+1234567890',
                                    'status' => 'active',
                                    'created_at' => '2026-01-15T10:30:00.000000Z',
                                    'roles' => [['id' => 3, 'name' => 'customer', 'description' => 'Default role. Browses and purchases products.']],
                                ],
                            ],
                        ],
                        'error_responses' => [
                            [
                                'status' => 401,
                                'description' => 'Unauthenticated',
                                'body' => ['message' => 'Unauthenticated.'],
                            ],
                            [
                                'status' => 403,
                                'description' => 'Forbidden',
                                'body' => ['message' => 'Forbidden. You do not have the required role.'],
                            ],
                            [
                                'status' => 404,
                                'description' => 'User not found',
                                'body' => ['message' => 'No query results for model [App\\Models\\User] 999.'],
                            ],
                        ],
                    ],
                    [
                        'name' => 'Update User Status',
                        'method' => 'PATCH',
                        'url' => '/api/v1/admin/users/{user}/status',
                        'description' => 'Block or unblock a user. Admin cannot change their own status.',
                        'auth_required' => true,
                        'request' => [
                            ['name' => 'status', 'type' => 'string', 'required' => true, 'rules' => 'in:active,blocked', 'description' => 'New status for the user.'],
                        ],
                        'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => [
                            'status' => 200,
                            'body' => [
                                'message' => 'User status updated to blocked.',
                                'data' => [
                                    'id' => 5,
                                    'name' => 'Bad Actor',
                                    'email' => 'bad@example.com',
                                    'status' => 'blocked',
                                    'roles' => [['id' => 3, 'name' => 'customer']],
                                ],
                            ],
                        ],
                        'error_responses' => [
                            [
                                'status' => 403,
                                'description' => 'Cannot change own status',
                                'body' => ['message' => 'You cannot change your own status.'],
                            ],
                            [
                                'status' => 422,
                                'description' => 'Validation failed',
                                'body' => [
                                    'message' => 'The given data was invalid.',
                                    'errors' => ['status' => ['Status must be either "active" or "blocked".']],
                                ],
                            ],
                        ],
                    ],
                    [
                        'name' => 'Assign Role',
                        'method' => 'POST',
                        'url' => '/api/v1/admin/users/{user}/assign-role',
                        'description' => 'Assign a role to a user. Uses syncWithoutDetaching so duplicate assignments are safe.',
                        'auth_required' => true,
                        'request' => [
                            ['name' => 'role', 'type' => 'string', 'required' => true, 'rules' => 'exists:roles,name', 'description' => 'Role name to assign (admin, vendor, customer).'],
                        ],
                        'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => [
                            'status' => 200,
                            'body' => [
                                'message' => "Role 'vendor' assigned to John Doe.",
                                'data' => [
                                    'id' => 1,
                                    'name' => 'John Doe',
                                    'email' => 'john@example.com',
                                    'status' => 'active',
                                    'roles' => [
                                        ['id' => 2, 'name' => 'vendor'],
                                        ['id' => 3, 'name' => 'customer'],
                                    ],
                                ],
                            ],
                        ],
                        'error_responses' => [
                            [
                                'status' => 422,
                                'description' => 'Invalid role',
                                'body' => [
                                    'message' => 'The given data was invalid.',
                                    'errors' => ['role' => ['The specified role does not exist.']],
                                ],
                            ],
                        ],
                    ],
                    [
                        'name' => 'Revoke Role',
                        'method' => 'DELETE',
                        'url' => '/api/v1/admin/users/{user}/revoke-role',
                        'description' => 'Remove a role from a user. Admin cannot revoke their own admin role.',
                        'auth_required' => true,
                        'request' => [
                            ['name' => 'role', 'type' => 'string', 'required' => true, 'rules' => 'exists:roles,name', 'description' => 'Role name to revoke.'],
                        ],
                        'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => [
                            'status' => 200,
                            'body' => [
                                'message' => "Role 'vendor' revoked from John Doe.",
                                'data' => [
                                    'id' => 1,
                                    'name' => 'John Doe',
                                    'email' => 'john@example.com',
                                    'status' => 'active',
                                    'roles' => [['id' => 3, 'name' => 'customer']],
                                ],
                            ],
                        ],
                        'error_responses' => [
                            [
                                'status' => 403,
                                'description' => 'Cannot revoke own admin role',
                                'body' => ['message' => 'You cannot revoke your own admin role.'],
                            ],
                            [
                                'status' => 422,
                                'description' => 'Invalid role',
                                'body' => [
                                    'message' => 'The given data was invalid.',
                                    'errors' => ['role' => ['The selected role is invalid.']],
                                ],
                            ],
                        ],
                    ],
                    [
                        'name' => 'List Roles',
                        'method' => 'GET',
                        'url' => '/api/v1/admin/roles',
                        'description' => 'Retrieve all available roles in the system.',
                        'auth_required' => true,
                        'request' => [],
                        'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => [
                            'status' => 200,
                            'body' => [
                                'message' => 'Roles retrieved.',
                                'data' => [
                                    ['id' => 1, 'name' => 'admin', 'description' => 'Full system access. Manages users, vendors, and platform settings.'],
                                    ['id' => 2, 'name' => 'vendor', 'description' => 'Sells products on the platform. Manages own inventory and orders.'],
                                    ['id' => 3, 'name' => 'customer', 'description' => 'Default role. Browses and purchases products.'],
                                ],
                            ],
                        ],
                        'error_responses' => [
                            [
                                'status' => 401,
                                'description' => 'Unauthenticated',
                                'body' => ['message' => 'Unauthenticated.'],
                            ],
                            [
                                'status' => 403,
                                'description' => 'Forbidden',
                                'body' => ['message' => 'Forbidden. You do not have the required role.'],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        return view('documentation', compact('endpoints'));
    }
}
