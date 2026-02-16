<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class DocumentationController extends Controller
{
    public function index(): View
    {
        $endpoints = [
            ...$this->authEndpoints(),
            ...$this->userProfileEndpoints(),
            ...$this->catalogEndpoints(),
            ...$this->vendorEndpoints(),
            ...$this->cartWishlistAlertEndpoints(),
            ...$this->checkoutPaymentEndpoints(),
            ...$this->socialCommerceEndpoints(),
            ...$this->mediaVideoEndpoints(),
            ...$this->aiAssistantEndpoints(),
            ...$this->cargoShippingEndpoints(),
            ...$this->messagingDisputeEndpoints(),
            ...$this->analyticsNotificationEndpoints(),
            ...$this->adminEndpoints(),
            ...$this->superAdminEndpoints(),
        ];

        return view('documentation', compact('endpoints'));
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function authEndpoints(): array
    {
        return [
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
                            'body' => ['message' => 'Registration initiated. Please check your email for OTP verification.', 'email' => 'user@example.com'],
                        ],
                        'error_responses' => [
                            ['status' => 422, 'description' => 'Validation failed', 'body' => ['message' => 'The email has already been taken.', 'errors' => ['email' => ['The email has already been taken.']]]],
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
                            'body' => ['message' => 'Registration successful.', 'user' => ['id' => 1, 'name' => 'John Doe', 'email' => 'user@example.com'], 'token' => 'eyJ0eXAiOiJKV1QiLCJhbGc...', 'token_type' => 'bearer', 'expires_in' => 3600],
                        ],
                        'error_responses' => [
                            ['status' => 400, 'description' => 'Invalid or expired OTP', 'body' => ['message' => 'Invalid or expired OTP.']],
                            ['status' => 404, 'description' => 'Registration data expired', 'body' => ['message' => 'Registration data not found or expired. Please register again.']],
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
                            'body' => ['message' => 'Login successful.', 'user' => ['id' => 1, 'name' => 'John Doe', 'email' => 'user@example.com'], 'token' => 'eyJ0eXAiOiJKV1QiLCJhbGc...', 'token_type' => 'bearer', 'expires_in' => 3600],
                        ],
                        'error_responses' => [
                            ['status' => 401, 'description' => 'Invalid credentials', 'body' => ['message' => 'Invalid email or password.']],
                            ['status' => 403, 'description' => 'Account blocked', 'body' => ['message' => 'Your account has been blocked. Please contact support.']],
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
                        'success_response' => ['status' => 200, 'body' => ['message' => 'Successfully logged out.']],
                        'error_responses' => [['status' => 401, 'description' => 'Unauthenticated', 'body' => ['message' => 'Unauthenticated.']]],
                    ],
                    [
                        'name' => 'Refresh Token',
                        'method' => 'POST',
                        'url' => '/api/v1/auth/refresh',
                        'description' => 'Refresh JWT token to extend session.',
                        'auth_required' => true,
                        'request' => [],
                        'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 200, 'body' => ['token' => 'eyJ0eXAiOiJKV1QiLCJhbGc...', 'token_type' => 'bearer', 'expires_in' => 3600]],
                        'error_responses' => [['status' => 401, 'description' => 'Unauthenticated', 'body' => ['message' => 'Unauthenticated.']]],
                    ],
                    [
                        'name' => 'Get Current User',
                        'method' => 'GET',
                        'url' => '/api/v1/auth/me',
                        'description' => 'Get authenticated user information with roles.',
                        'auth_required' => true,
                        'request' => [],
                        'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 200, 'body' => ['user' => ['id' => 1, 'name' => 'John Doe', 'email' => 'user@example.com', 'phone' => '+1234567890', 'profile_photo' => 'https://cdn.natakahii.com/users/avatars/abc123.jpg', 'status' => 'active', 'roles' => [['id' => 1, 'name' => 'customer']]]]],
                        'error_responses' => [['status' => 401, 'description' => 'Unauthenticated', 'body' => ['message' => 'Unauthenticated.']]],
                    ],
                    [
                        'name' => 'Forgot Password',
                        'method' => 'POST',
                        'url' => '/api/v1/auth/forgot-password',
                        'description' => 'Request password reset OTP.',
                        'auth_required' => false,
                        'request' => [['name' => 'email', 'type' => 'string', 'required' => true, 'rules' => 'email|exists:users', 'description' => 'Registered email address.']],
                        'headers' => [],
                        'success_response' => ['status' => 200, 'body' => ['message' => 'Password reset OTP sent to your email.']],
                        'error_responses' => [['status' => 422, 'description' => 'Validation failed', 'body' => ['message' => 'The selected email is invalid.', 'errors' => ['email' => ['The selected email is invalid.']]]]],
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
                        'success_response' => ['status' => 200, 'body' => ['message' => 'Password reset successful. You can now login with your new password.']],
                        'error_responses' => [['status' => 400, 'description' => 'Invalid or expired OTP', 'body' => ['message' => 'Invalid or expired OTP.']]],
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
                        'success_response' => ['status' => 200, 'body' => ['message' => 'OTP resent successfully.']],
                        'error_responses' => [['status' => 429, 'description' => 'Too many requests', 'body' => ['message' => 'Please wait before requesting a new OTP.']]],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function userProfileEndpoints(): array
    {
        return [
            [
                'group' => 'User Profile',
                'description' => 'Manage the authenticated user\'s profile and profile photo. Photos are stored in Backblaze B2 and served via CDN.',
                'endpoints' => [
                    [
                        'name' => 'Update Profile', 'method' => 'PATCH', 'url' => '/api/v1/profile',
                        'description' => 'Update the authenticated user\'s name or phone number.',
                        'auth_required' => true,
                        'request' => [
                            ['name' => 'name', 'type' => 'string', 'required' => false, 'rules' => 'max:255', 'description' => 'Updated display name.'],
                            ['name' => 'phone', 'type' => 'string', 'required' => false, 'rules' => 'max:20', 'description' => 'Updated phone number.'],
                        ],
                        'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 200, 'body' => ['message' => 'Profile updated.', 'user' => ['id' => 1, 'name' => 'John Doe', 'email' => 'user@example.com', 'phone' => '+255700000000', 'profile_photo' => 'https://cdn.natakahii.com/users/avatars/abc123.jpg', 'status' => 'active', 'roles' => ['customer']]]],
                        'error_responses' => [['status' => 401, 'description' => 'Unauthenticated', 'body' => ['message' => 'Unauthenticated.']]],
                    ],
                    [
                        'name' => 'Upload Profile Photo', 'method' => 'POST', 'url' => '/api/v1/profile/photo',
                        'description' => 'Upload or replace the user\'s profile photo. Stored in Backblaze B2 and returned as a CDN URL.',
                        'auth_required' => true,
                        'request' => [
                            ['name' => 'photo', 'type' => 'file', 'required' => true, 'rules' => 'image|max:5120', 'description' => 'Profile photo image (max 5MB). Accepts JPEG, PNG, GIF, WebP.'],
                        ],
                        'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 200, 'body' => ['message' => 'Profile photo updated.', 'user' => ['id' => 1, 'name' => 'John Doe', 'profile_photo' => 'https://cdn.natakahii.com/users/avatars/abc123.jpg']]],
                        'error_responses' => [['status' => 422, 'description' => 'Validation failed', 'body' => ['message' => 'The photo field must be an image.', 'errors' => ['photo' => ['The photo field must be an image.']]]]],
                    ],
                    [
                        'name' => 'Remove Profile Photo', 'method' => 'DELETE', 'url' => '/api/v1/profile/photo',
                        'description' => 'Delete the user\'s profile photo from storage and set to null.',
                        'auth_required' => true,
                        'request' => [],
                        'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 200, 'body' => ['message' => 'Profile photo removed.', 'user' => ['id' => 1, 'name' => 'John Doe', 'profile_photo' => null]]],
                        'error_responses' => [['status' => 401, 'description' => 'Unauthenticated', 'body' => ['message' => 'Unauthenticated.']]],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function catalogEndpoints(): array
    {
        return [
            [
                'group' => 'Catalog',
                'description' => 'Public product catalog browsing. No authentication required.',
                'endpoints' => [
                    [
                        'name' => 'List Categories', 'method' => 'GET', 'url' => '/api/v1/categories',
                        'description' => 'List all active root categories with children and product counts.',
                        'auth_required' => false, 'request' => [], 'headers' => [],
                        'success_response' => ['status' => 200, 'body' => ['categories' => [['id' => 1, 'name' => 'Electronics', 'slug' => 'electronics', 'children' => [['id' => 2, 'name' => 'Phones']], 'products_count' => 45]]]],
                        'error_responses' => [],
                    ],
                    [
                        'name' => 'Category Filters', 'method' => 'GET', 'url' => '/api/v1/categories/{category}/filters',
                        'description' => 'Get filterable attributes for a specific category.',
                        'auth_required' => false, 'request' => [], 'headers' => [],
                        'success_response' => ['status' => 200, 'body' => ['category' => ['id' => 1, 'name' => 'Electronics'], 'filters' => [['id' => 1, 'name' => 'Color', 'values' => [['id' => 1, 'value' => 'Red']]]]]],
                        'error_responses' => [['status' => 404, 'description' => 'Category not found', 'body' => ['message' => 'Not found.']]],
                    ],
                    [
                        'name' => 'List Products', 'method' => 'GET', 'url' => '/api/v1/products',
                        'description' => 'List active products with filtering, search, and pagination.',
                        'auth_required' => false,
                        'request' => [
                            ['name' => 'category_id', 'type' => 'integer', 'required' => false, 'rules' => '', 'description' => 'Filter by category.'],
                            ['name' => 'vendor_id', 'type' => 'integer', 'required' => false, 'rules' => '', 'description' => 'Filter by vendor.'],
                            ['name' => 'search', 'type' => 'string', 'required' => false, 'rules' => '', 'description' => 'Search by name or description.'],
                            ['name' => 'min_price', 'type' => 'number', 'required' => false, 'rules' => '', 'description' => 'Minimum price filter.'],
                            ['name' => 'max_price', 'type' => 'number', 'required' => false, 'rules' => '', 'description' => 'Maximum price filter.'],
                            ['name' => 'sort_by', 'type' => 'string', 'required' => false, 'rules' => 'in:created_at,price,name', 'description' => 'Sort field. Default: created_at.'],
                            ['name' => 'sort_dir', 'type' => 'string', 'required' => false, 'rules' => 'in:asc,desc', 'description' => 'Sort direction. Default: desc.'],
                            ['name' => 'per_page', 'type' => 'integer', 'required' => false, 'rules' => '', 'description' => 'Items per page. Default: 15.'],
                        ],
                        'headers' => [],
                        'success_response' => ['status' => 200, 'body' => ['products' => [['id' => 1, 'name' => 'Wireless Earbuds', 'price' => '29999.00', 'image' => 'https://cdn.natakahii.com/products/1/earbuds.jpg', 'vendor' => ['shop_name' => 'TechShop', 'logo' => 'https://cdn.natakahii.com/vendors/logos/techshop.png']]], 'meta' => ['current_page' => 1, 'last_page' => 5, 'per_page' => 15, 'total' => 68]]],
                        'error_responses' => [],
                    ],
                    [
                        'name' => 'Show Product', 'method' => 'GET', 'url' => '/api/v1/products/{product}',
                        'description' => 'Get full product details including variants, attributes, and recent reviews.',
                        'auth_required' => false, 'request' => [], 'headers' => [],
                        'success_response' => ['status' => 200, 'body' => ['product' => ['id' => 1, 'name' => 'Wireless Earbuds', 'price' => '29999.00', 'images' => ['https://cdn.natakahii.com/products/1/earbuds.jpg'], 'variants' => [], 'reviews_count' => 12, 'reviews_avg_rating' => '4.50'], 'recent_reviews' => []]],
                        'error_responses' => [['status' => 404, 'description' => 'Product not found', 'body' => ['message' => 'Not found.']]],
                    ],
                    [
                        'name' => 'Resolve Variant', 'method' => 'GET', 'url' => '/api/v1/products/{product}/variants/resolve?attribute_value_ids=1,4,7',
                        'description' => 'Resolve a product variant from selected attribute value IDs.',
                        'auth_required' => false,
                        'request' => [['name' => 'attribute_value_ids', 'type' => 'string', 'required' => true, 'rules' => '', 'description' => 'Comma-separated attribute value IDs.']],
                        'headers' => [],
                        'success_response' => ['status' => 200, 'body' => ['variant' => ['id' => 1, 'sku' => 'WE-BLK-M', 'price' => '29999.00', 'stock' => 50]]],
                        'error_responses' => [['status' => 404, 'description' => 'No matching variant', 'body' => ['message' => 'No matching variant found for the selected attributes.']]],
                    ],
                    [
                        'name' => 'List Vendors', 'method' => 'GET', 'url' => '/api/v1/vendors',
                        'description' => 'List approved vendors with optional search.',
                        'auth_required' => false,
                        'request' => [
                            ['name' => 'search', 'type' => 'string', 'required' => false, 'rules' => '', 'description' => 'Search by shop name or description.'],
                            ['name' => 'per_page', 'type' => 'integer', 'required' => false, 'rules' => '', 'description' => 'Items per page. Default: 15.'],
                        ],
                        'headers' => [],
                        'success_response' => ['status' => 200, 'body' => ['vendors' => [['id' => 1, 'shop_name' => 'TechShop', 'logo' => 'https://cdn.natakahii.com/vendors/logos/techshop.png', 'products_count' => 45]], 'meta' => ['current_page' => 1, 'last_page' => 2, 'per_page' => 15, 'total' => 20]]],
                        'error_responses' => [],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function vendorEndpoints(): array
    {
        return [
            [
                'group' => 'Vendor',
                'description' => 'Vendor store management. Requires authentication and vendor role.',
                'endpoints' => [
                    [
                        'name' => 'Create/Update Vendor Profile', 'method' => 'POST', 'url' => '/api/v1/vendor/profile',
                        'description' => 'Create or update vendor store profile. First-time creates new vendor, subsequent calls update.',
                        'auth_required' => true,
                        'request' => [
                            ['name' => 'shop_name', 'type' => 'string', 'required' => true, 'rules' => 'max:255', 'description' => 'Store display name.'],
                            ['name' => 'shop_slug', 'type' => 'string', 'required' => true, 'rules' => 'alpha_dash|unique:vendors', 'description' => 'URL-friendly store slug.'],
                            ['name' => 'description', 'type' => 'string', 'required' => false, 'rules' => '', 'description' => 'Store description.'],
                            ['name' => 'logo', 'type' => 'file', 'required' => false, 'rules' => 'image|max:2048', 'description' => 'Store logo image.'],
                        ],
                        'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 201, 'body' => ['message' => 'Vendor profile created. Awaiting approval.', 'vendor' => ['id' => 1, 'shop_name' => 'My Store', 'logo' => 'https://cdn.natakahii.com/vendors/logos/my-store.png', 'status' => 'pending']]],
                        'error_responses' => [['status' => 422, 'description' => 'Validation failed', 'body' => ['message' => 'The shop slug has already been taken.']]],
                    ],
                    [
                        'name' => 'Create Product', 'method' => 'POST', 'url' => '/api/v1/vendor/products',
                        'description' => 'Create a new product with optional variants and images. Vendor must be approved.',
                        'auth_required' => true,
                        'request' => [
                            ['name' => 'category_id', 'type' => 'integer', 'required' => true, 'rules' => 'exists:categories,id', 'description' => 'Product category.'],
                            ['name' => 'name', 'type' => 'string', 'required' => true, 'rules' => 'max:255', 'description' => 'Product name.'],
                            ['name' => 'description', 'type' => 'string', 'required' => false, 'rules' => '', 'description' => 'Product description.'],
                            ['name' => 'price', 'type' => 'number', 'required' => true, 'rules' => 'numeric|min:0', 'description' => 'Base price.'],
                            ['name' => 'discount_price', 'type' => 'number', 'required' => false, 'rules' => 'numeric|min:0', 'description' => 'Discounted price.'],
                            ['name' => 'stock', 'type' => 'integer', 'required' => true, 'rules' => 'integer|min:0', 'description' => 'Available stock.'],
                            ['name' => 'images[]', 'type' => 'file', 'required' => false, 'rules' => 'image|max:2048', 'description' => 'Product images (multiple).'],
                            ['name' => 'variants', 'type' => 'array', 'required' => false, 'rules' => '', 'description' => 'Array of variant objects with sku, price, stock, attributes.'],
                        ],
                        'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 201, 'body' => ['message' => 'Product created successfully.', 'product' => ['id' => 1, 'name' => 'Wireless Earbuds', 'slug' => 'wireless-earbuds-abc123', 'images' => ['https://cdn.natakahii.com/products/1/earbuds-1.jpg', 'https://cdn.natakahii.com/products/1/earbuds-2.jpg']]]],
                        'error_responses' => [['status' => 403, 'description' => 'Vendor not approved', 'body' => ['message' => 'Your vendor account must be approved before listing products.']]],
                    ],
                    [
                        'name' => 'List Vendor Dropoffs', 'method' => 'GET', 'url' => '/api/v1/vendor/dropoffs',
                        'description' => 'List dropoffs registered by the authenticated vendor.',
                        'auth_required' => true, 'request' => [], 'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 200, 'body' => ['dropoffs' => [['id' => 1, 'status' => 'pending', 'order_id' => 10]]]],
                        'error_responses' => [['status' => 404, 'description' => 'No vendor profile', 'body' => ['message' => 'No vendor profile found.']]],
                    ],
                    [
                        'name' => 'Register Dropoff', 'method' => 'POST', 'url' => '/api/v1/vendor/dropoffs',
                        'description' => 'Register a product dropoff at a fulfillment center.',
                        'auth_required' => true,
                        'request' => [
                            ['name' => 'order_id', 'type' => 'integer', 'required' => true, 'rules' => 'exists:orders,id', 'description' => 'Order to drop off.'],
                            ['name' => 'fulfillment_center_id', 'type' => 'integer', 'required' => true, 'rules' => 'exists:fulfillment_centers,id', 'description' => 'Target fulfillment center.'],
                            ['name' => 'notes', 'type' => 'string', 'required' => false, 'rules' => '', 'description' => 'Additional notes.'],
                        ],
                        'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 201, 'body' => ['message' => 'Dropoff registered.', 'dropoff' => ['id' => 1, 'status' => 'pending']]],
                        'error_responses' => [['status' => 404, 'description' => 'No vendor profile', 'body' => ['message' => 'No vendor profile found.']]],
                    ],
                    [
                        'name' => 'Upload Product Media', 'method' => 'POST', 'url' => '/api/v1/vendor/products/{product}/media',
                        'description' => 'Upload an image or video for a vendor product.',
                        'auth_required' => true,
                        'request' => [
                            ['name' => 'file', 'type' => 'file', 'required' => true, 'rules' => 'max:51200', 'description' => 'Image or video file (max 50MB).'],
                            ['name' => 'type', 'type' => 'string', 'required' => true, 'rules' => 'in:image,video', 'description' => 'Media type.'],
                            ['name' => 'title', 'type' => 'string', 'required' => false, 'rules' => 'max:255', 'description' => 'Media title.'],
                        ],
                        'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 201, 'body' => ['message' => 'Media uploaded.', 'media' => ['id' => 1, 'type' => 'image', 'file_path' => 'https://cdn.natakahii.com/media/1/photo.jpg']]],
                        'error_responses' => [['status' => 403, 'description' => 'Not product owner', 'body' => ['message' => 'You do not own this product.']]],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function cartWishlistAlertEndpoints(): array
    {
        return [
            [
                'group' => 'Cart, Wishlist & Alerts',
                'description' => 'Shopping cart management, wishlists and price/stock alerts. Requires authentication.',
                'endpoints' => [
                    [
                        'name' => 'Get Cart', 'method' => 'GET', 'url' => '/api/v1/cart',
                        'description' => 'Retrieve the authenticated user\'s cart with items.',
                        'auth_required' => true, 'request' => [], 'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 200, 'body' => ['cart' => ['id' => 1, 'items' => [['id' => 1, 'product_id' => 5, 'quantity' => 2, 'price' => '29999.00']]]]],
                        'error_responses' => [['status' => 401, 'description' => 'Unauthenticated', 'body' => ['message' => 'Unauthenticated.']]],
                    ],
                    [
                        'name' => 'Add Cart Item', 'method' => 'POST', 'url' => '/api/v1/cart/items',
                        'description' => 'Add a product to the cart or increase quantity if already present.',
                        'auth_required' => true,
                        'request' => [
                            ['name' => 'product_id', 'type' => 'integer', 'required' => true, 'rules' => 'exists:products,id', 'description' => 'Product to add.'],
                            ['name' => 'quantity', 'type' => 'integer', 'required' => true, 'rules' => 'min:1', 'description' => 'Quantity to add.'],
                        ],
                        'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 200, 'body' => ['message' => 'Item added to cart.', 'cart' => ['id' => 1, 'items' => []]]],
                        'error_responses' => [['status' => 422, 'description' => 'Validation failed', 'body' => ['message' => 'The selected product id is invalid.']]],
                    ],
                    [
                        'name' => 'List Wishlist', 'method' => 'GET', 'url' => '/api/v1/wishlists',
                        'description' => 'Get paginated wishlist for the authenticated customer.',
                        'auth_required' => true, 'request' => [['name' => 'per_page', 'type' => 'integer', 'required' => false, 'rules' => '', 'description' => 'Items per page.']], 'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 200, 'body' => ['wishlists' => [['id' => 1, 'product_id' => 5, 'product' => ['name' => 'Earbuds', 'image' => 'https://cdn.natakahii.com/products/5/earbuds.jpg']]], 'meta' => ['total' => 3]]],
                        'error_responses' => [],
                    ],
                    [
                        'name' => 'Toggle Wishlist', 'method' => 'POST', 'url' => '/api/v1/wishlists/toggle',
                        'description' => 'Add or remove a product from the wishlist (toggle).',
                        'auth_required' => true,
                        'request' => [['name' => 'product_id', 'type' => 'integer', 'required' => true, 'rules' => 'exists:products,id', 'description' => 'Product to toggle.']],
                        'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 201, 'body' => ['message' => 'Product added to wishlist.', 'wishlisted' => true]],
                        'error_responses' => [],
                    ],
                    [
                        'name' => 'Create Alert', 'method' => 'POST', 'url' => '/api/v1/alerts',
                        'description' => 'Create a price-drop or back-in-stock alert for a product.',
                        'auth_required' => true,
                        'request' => [
                            ['name' => 'product_id', 'type' => 'integer', 'required' => true, 'rules' => 'exists:products,id', 'description' => 'Product to watch.'],
                            ['name' => 'type', 'type' => 'string', 'required' => true, 'rules' => 'in:price_drop,back_in_stock', 'description' => 'Alert type.'],
                            ['name' => 'target_price', 'type' => 'number', 'required' => false, 'rules' => 'required_if:type,price_drop', 'description' => 'Target price for price_drop alerts.'],
                        ],
                        'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 201, 'body' => ['message' => 'Alert created.', 'alert' => ['id' => 1, 'type' => 'price_drop', 'target_price' => '20000.00']]],
                        'error_responses' => [],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function checkoutPaymentEndpoints(): array
    {
        return [
            [
                'group' => 'Checkout & Payments',
                'description' => 'Convert carts to orders, process payments, and handle shipping quotes.',
                'endpoints' => [
                    [
                        'name' => 'Checkout', 'method' => 'POST', 'url' => '/api/v1/checkout',
                        'description' => 'Convert the authenticated user\'s cart into an order. Cart items are cleared after checkout.',
                        'auth_required' => true, 'request' => [], 'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 201, 'body' => ['message' => 'Order placed successfully.', 'order' => ['id' => 1, 'order_number' => 'NTK-20260208-ABC123', 'total_amount' => '59998.00', 'status' => 'pending']]],
                        'error_responses' => [['status' => 422, 'description' => 'Empty cart', 'body' => ['message' => 'Your cart is empty.']]],
                    ],
                    [
                        'name' => 'Initiate Payment', 'method' => 'POST', 'url' => '/api/v1/payments',
                        'description' => 'Initiate payment for an order.',
                        'auth_required' => true,
                        'request' => [
                            ['name' => 'order_id', 'type' => 'integer', 'required' => true, 'rules' => 'exists:orders,id', 'description' => 'Order to pay for.'],
                            ['name' => 'payment_method', 'type' => 'string', 'required' => true, 'rules' => 'in:mpesa,card,bank_transfer', 'description' => 'Payment method.'],
                        ],
                        'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 201, 'body' => ['message' => 'Payment initiated.', 'payment' => ['id' => 1, 'amount' => '59998.00', 'status' => 'pending']]],
                        'error_responses' => [['status' => 422, 'description' => 'Already paid', 'body' => ['message' => 'This order has already been paid.']]],
                    ],
                    [
                        'name' => 'Payment Webhook', 'method' => 'POST', 'url' => '/api/v1/payments/webhook/{provider}',
                        'description' => 'Webhook callback for payment providers. No auth required.',
                        'auth_required' => false,
                        'request' => [
                            ['name' => 'transaction_id', 'type' => 'string', 'required' => true, 'rules' => '', 'description' => 'Provider transaction ID.'],
                            ['name' => 'status', 'type' => 'string', 'required' => true, 'rules' => '', 'description' => 'Payment status from provider.'],
                        ],
                        'headers' => [],
                        'success_response' => ['status' => 200, 'body' => ['message' => 'Webhook processed.']],
                        'error_responses' => [['status' => 400, 'description' => 'Invalid payload', 'body' => ['message' => 'Invalid webhook payload.']]],
                    ],
                    [
                        'name' => 'Request Shipping Quotes', 'method' => 'POST', 'url' => '/api/v1/shipping/quotes',
                        'description' => 'Get shipping quotes based on destination and weight.',
                        'auth_required' => false,
                        'request' => [
                            ['name' => 'destination_address', 'type' => 'string', 'required' => true, 'rules' => '', 'description' => 'Delivery destination address.'],
                            ['name' => 'weight_kg', 'type' => 'number', 'required' => true, 'rules' => 'min:0.1', 'description' => 'Total weight in kilograms.'],
                        ],
                        'headers' => [],
                        'success_response' => ['status' => 200, 'body' => ['message' => 'Shipping quotes generated.', 'quotes' => [['provider' => 'NatakaHii Cargo', 'service_level' => 'standard', 'price' => '5000.00', 'estimated_days' => 5]]]],
                        'error_responses' => [],
                    ],
                    [
                        'name' => 'Select Shipping Quote', 'method' => 'POST', 'url' => '/api/v1/shipping/quotes/{quote}/select',
                        'description' => 'Select a shipping quote for an order.',
                        'auth_required' => false, 'request' => [], 'headers' => [],
                        'success_response' => ['status' => 200, 'body' => ['message' => 'Shipping quote selected.']],
                        'error_responses' => [['status' => 422, 'description' => 'Quote expired', 'body' => ['message' => 'This quote has expired. Please request new quotes.']]],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function socialCommerceEndpoints(): array
    {
        return [
            [
                'group' => 'Social Commerce',
                'description' => 'Social engagement features: likes, shares, follows, views, and not-interested markers.',
                'endpoints' => [
                    [
                        'name' => 'Record Product View', 'method' => 'POST', 'url' => '/api/v1/products/{product}/view',
                        'description' => 'Record a product view event. Public, no auth required.',
                        'auth_required' => false, 'request' => [], 'headers' => [],
                        'success_response' => ['status' => 200, 'body' => ['message' => 'View recorded.']],
                        'error_responses' => [],
                    ],
                    [
                        'name' => 'Share Product', 'method' => 'POST', 'url' => '/api/v1/products/{product}/shares',
                        'description' => 'Record a product share event. Public.',
                        'auth_required' => false,
                        'request' => [['name' => 'platform', 'type' => 'string', 'required' => false, 'rules' => 'max:50', 'description' => 'Share platform (e.g. whatsapp, facebook).']],
                        'headers' => [],
                        'success_response' => ['status' => 200, 'body' => ['message' => 'Share recorded.', 'shares_count' => 15]],
                        'error_responses' => [],
                    ],
                    [
                        'name' => 'Like Product', 'method' => 'POST', 'url' => '/api/v1/products/{product}/likes',
                        'description' => 'Like a product. Requires customer role.',
                        'auth_required' => true, 'request' => [], 'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 200, 'body' => ['message' => 'Product liked.', 'likes_count' => 42]],
                        'error_responses' => [],
                    ],
                    [
                        'name' => 'Unlike Product', 'method' => 'DELETE', 'url' => '/api/v1/products/{product}/likes',
                        'description' => 'Remove a like from a product.',
                        'auth_required' => true, 'request' => [], 'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 200, 'body' => ['message' => 'Product unliked.', 'likes_count' => 41]],
                        'error_responses' => [],
                    ],
                    [
                        'name' => 'Follow Vendor', 'method' => 'POST', 'url' => '/api/v1/vendors/{vendor}/follow',
                        'description' => 'Follow a vendor to receive updates.',
                        'auth_required' => true, 'request' => [], 'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 200, 'body' => ['message' => 'Now following TechShop.', 'followers_count' => 150]],
                        'error_responses' => [],
                    ],
                    [
                        'name' => 'Unfollow Vendor', 'method' => 'DELETE', 'url' => '/api/v1/vendors/{vendor}/follow',
                        'description' => 'Unfollow a vendor.',
                        'auth_required' => true, 'request' => [], 'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 200, 'body' => ['message' => 'Unfollowed TechShop.', 'followers_count' => 149]],
                        'error_responses' => [],
                    ],
                    [
                        'name' => 'My Followed Vendors', 'method' => 'GET', 'url' => '/api/v1/me/following/vendors',
                        'description' => 'List vendors the authenticated user follows.',
                        'auth_required' => true, 'request' => [], 'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 200, 'body' => ['vendors' => [['id' => 1, 'shop_name' => 'TechShop']], 'meta' => ['total' => 5]]],
                        'error_responses' => [],
                    ],
                    [
                        'name' => 'Mark Not Interested', 'method' => 'POST', 'url' => '/api/v1/products/{product}/not-interested',
                        'description' => 'Mark a product as not interested to hide it from recommendations.',
                        'auth_required' => true, 'request' => [], 'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 200, 'body' => ['message' => 'Product marked as not interested.']],
                        'error_responses' => [],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function mediaVideoEndpoints(): array
    {
        return [
            [
                'group' => 'Media & Video',
                'description' => 'Product media management and video commerce feed.',
                'endpoints' => [
                    [
                        'name' => 'List Product Media', 'method' => 'GET', 'url' => '/api/v1/products/{product}/media',
                        'description' => 'List all media (images and videos) for a product. Public.',
                        'auth_required' => false, 'request' => [], 'headers' => [],
                        'success_response' => ['status' => 200, 'body' => ['media' => [['id' => 1, 'type' => 'image', 'file_path' => 'https://cdn.natakahii.com/media/1/photo.jpg', 'mime_type' => 'image/jpeg']]]],
                        'error_responses' => [],
                    ],
                    [
                        'name' => 'Video Feed', 'method' => 'GET', 'url' => '/api/v1/videos/feed',
                        'description' => 'Paginated video feed for video commerce. Public.',
                        'auth_required' => false, 'request' => [['name' => 'per_page', 'type' => 'integer', 'required' => false, 'rules' => '', 'description' => 'Items per page.']], 'headers' => [],
                        'success_response' => ['status' => 200, 'body' => ['videos' => [['id' => 1, 'type' => 'video', 'file_path' => 'https://cdn.natakahii.com/media/1/demo.mp4', 'mime_type' => 'video/mp4', 'product' => ['name' => 'Earbuds']]], 'meta' => ['total' => 25]]],
                        'error_responses' => [],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function aiAssistantEndpoints(): array
    {
        return [
            [
                'group' => 'AI Shopping Assistant',
                'description' => 'AI-powered shopping assistant for product recommendations and conversations.',
                'endpoints' => [
                    [
                        'name' => 'Quick Ask', 'method' => 'POST', 'url' => '/api/v1/ai/ask',
                        'description' => 'Ask the AI assistant a quick question. Public, no auth required.',
                        'auth_required' => false,
                        'request' => [['name' => 'message', 'type' => 'string', 'required' => true, 'rules' => 'max:2000', 'description' => 'Your question or message.']],
                        'headers' => [],
                        'success_response' => ['status' => 200, 'body' => ['reply' => 'Thank you for your question! Try browsing our product catalog.']],
                        'error_responses' => [],
                    ],
                    [
                        'name' => 'List AI Conversations', 'method' => 'GET', 'url' => '/api/v1/ai/conversations',
                        'description' => 'List the authenticated user\'s AI chat conversations.',
                        'auth_required' => true, 'request' => [], 'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 200, 'body' => ['conversations' => [['id' => 1, 'title' => 'Shopping help', 'messages_count' => 5]]]],
                        'error_responses' => [],
                    ],
                    [
                        'name' => 'Create AI Conversation', 'method' => 'POST', 'url' => '/api/v1/ai/conversations',
                        'description' => 'Start a new AI conversation.',
                        'auth_required' => true,
                        'request' => [['name' => 'title', 'type' => 'string', 'required' => false, 'rules' => 'max:255', 'description' => 'Conversation title.']],
                        'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 201, 'body' => ['message' => 'Conversation created.', 'conversation' => ['id' => 1, 'title' => 'Shopping help']]],
                        'error_responses' => [],
                    ],
                    [
                        'name' => 'Send AI Message', 'method' => 'POST', 'url' => '/api/v1/ai/conversations/{conversation}/messages',
                        'description' => 'Send a message in an AI conversation and get a reply.',
                        'auth_required' => true,
                        'request' => [['name' => 'message', 'type' => 'string', 'required' => true, 'rules' => 'max:2000', 'description' => 'Your message.']],
                        'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 200, 'body' => ['reply' => ['role' => 'assistant', 'content' => 'Check back soon for personalized recommendations.']]],
                        'error_responses' => [['status' => 403, 'description' => 'Not your conversation', 'body' => ['message' => 'Forbidden.']]],
                    ],
                    [
                        'name' => 'Get Recommendations', 'method' => 'GET', 'url' => '/api/v1/ai/recommendations',
                        'description' => 'Get AI-personalized product recommendations.',
                        'auth_required' => true, 'request' => [], 'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 200, 'body' => ['recommendations' => [['id' => 1, 'name' => 'Wireless Earbuds', 'price' => '29999.00']]]],
                        'error_responses' => [],
                    ],
                    [
                        'name' => 'Track Recommendation Event', 'method' => 'POST', 'url' => '/api/v1/ai/recommendation-events',
                        'description' => 'Track user interaction with AI recommendations (click, add_to_cart, purchase).',
                        'auth_required' => true,
                        'request' => [
                            ['name' => 'product_id', 'type' => 'integer', 'required' => false, 'rules' => 'exists:products,id', 'description' => 'Related product.'],
                            ['name' => 'event_type', 'type' => 'string', 'required' => true, 'rules' => 'max:50', 'description' => 'Event type (click, add_to_cart, purchase).'],
                            ['name' => 'metadata', 'type' => 'object', 'required' => false, 'rules' => '', 'description' => 'Additional event data.'],
                        ],
                        'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 200, 'body' => ['message' => 'Event tracked.']],
                        'error_responses' => [],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function cargoShippingEndpoints(): array
    {
        return [
            [
                'group' => 'Cargo & Shipping',
                'description' => 'Fulfillment centers, dropoff management, cargo shipments, delivery runs, and tracking.',
                'endpoints' => [
                    [
                        'name' => 'List Fulfillment Centers', 'method' => 'GET', 'url' => '/api/v1/cargo/fulfillment-centers',
                        'description' => 'List active fulfillment centers. Requires delivery_agent or admin role.',
                        'auth_required' => true, 'request' => [], 'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 200, 'body' => ['fulfillment_centers' => [['id' => 1, 'name' => 'Dar es Salaam Hub', 'city' => 'Dar es Salaam']]]],
                        'error_responses' => [],
                    ],
                    [
                        'name' => 'Receive Dropoff', 'method' => 'POST', 'url' => '/api/v1/cargo/dropoffs/{dropoff}/receive',
                        'description' => 'Mark a vendor dropoff as received at a fulfillment center.',
                        'auth_required' => true,
                        'request' => [['name' => 'fulfillment_center_id', 'type' => 'integer', 'required' => true, 'rules' => 'exists:fulfillment_centers,id', 'description' => 'Receiving center.']],
                        'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 200, 'body' => ['message' => 'Dropoff received.', 'dropoff' => ['status' => 'received']]],
                        'error_responses' => [],
                    ],
                    [
                        'name' => 'Start QC', 'method' => 'POST', 'url' => '/api/v1/cargo/dropoffs/{dropoff}/qc/start',
                        'description' => 'Start quality control inspection on a dropoff.',
                        'auth_required' => true, 'request' => [], 'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 200, 'body' => ['message' => 'QC started.', 'dropoff' => ['status' => 'qc_in_progress']]],
                        'error_responses' => [],
                    ],
                    [
                        'name' => 'Complete QC', 'method' => 'POST', 'url' => '/api/v1/cargo/dropoffs/{dropoff}/qc/complete',
                        'description' => 'Complete QC and mark as passed or failed.',
                        'auth_required' => true,
                        'request' => [
                            ['name' => 'passed', 'type' => 'boolean', 'required' => true, 'rules' => '', 'description' => 'Whether QC passed.'],
                            ['name' => 'notes', 'type' => 'string', 'required' => false, 'rules' => '', 'description' => 'QC notes.'],
                        ],
                        'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 200, 'body' => ['message' => 'QC completed.', 'dropoff' => ['status' => 'qc_passed']]],
                        'error_responses' => [],
                    ],
                    [
                        'name' => 'Create Shipment', 'method' => 'POST', 'url' => '/api/v1/cargo/shipments',
                        'description' => 'Create a new cargo shipment. Admin only.',
                        'auth_required' => true,
                        'request' => [
                            ['name' => 'order_id', 'type' => 'integer', 'required' => true, 'rules' => 'exists:orders,id', 'description' => 'Order to ship.'],
                            ['name' => 'destination_address', 'type' => 'string', 'required' => true, 'rules' => '', 'description' => 'Delivery address.'],
                            ['name' => 'recipient_name', 'type' => 'string', 'required' => true, 'rules' => 'max:255', 'description' => 'Recipient name.'],
                            ['name' => 'recipient_phone', 'type' => 'string', 'required' => false, 'rules' => 'max:20', 'description' => 'Recipient phone.'],
                        ],
                        'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 201, 'body' => ['message' => 'Shipment created.', 'shipment' => ['tracking_number' => 'NTK-SHP-ABC123', 'status' => 'pending']]],
                        'error_responses' => [],
                    ],
                    [
                        'name' => 'Track Shipment', 'method' => 'GET', 'url' => '/api/v1/shipments/{shipment}/tracking',
                        'description' => 'View tracking information for a shipment.',
                        'auth_required' => true, 'request' => [], 'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 200, 'body' => ['shipment' => ['tracking_number' => 'NTK-SHP-ABC123', 'status' => 'in_transit'], 'tracking_events' => [['event' => 'shipped', 'location' => 'Dar es Salaam']]]],
                        'error_responses' => [],
                    ],
                    [
                        'name' => 'Mark Delivered', 'method' => 'POST', 'url' => '/api/v1/cargo/shipments/{shipment}/mark-delivered',
                        'description' => 'Mark a shipment as delivered. For delivery agents.',
                        'auth_required' => true, 'request' => [], 'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 200, 'body' => ['message' => 'Shipment marked as delivered.']],
                        'error_responses' => [],
                    ],
                    [
                        'name' => 'My Delivery Runs', 'method' => 'GET', 'url' => '/api/v1/cargo/my/delivery-runs',
                        'description' => 'List delivery runs assigned to the current delivery agent.',
                        'auth_required' => true, 'request' => [], 'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 200, 'body' => ['delivery_runs' => [['id' => 1, 'status' => 'dispatched', 'scheduled_date' => '2026-02-10']]]],
                        'error_responses' => [],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function messagingDisputeEndpoints(): array
    {
        return [
            [
                'group' => 'Messaging & Disputes',
                'description' => 'User-to-user messaging and order dispute management.',
                'endpoints' => [
                    [
                        'name' => 'Start Conversation', 'method' => 'POST', 'url' => '/api/v1/messages/conversations',
                        'description' => 'Start a new conversation with another user.',
                        'auth_required' => true,
                        'request' => [
                            ['name' => 'receiver_id', 'type' => 'integer', 'required' => true, 'rules' => 'exists:users,id', 'description' => 'User to message.'],
                            ['name' => 'subject', 'type' => 'string', 'required' => false, 'rules' => 'max:255', 'description' => 'Conversation subject.'],
                            ['name' => 'message', 'type' => 'string', 'required' => true, 'rules' => '', 'description' => 'First message content.'],
                            ['name' => 'order_id', 'type' => 'integer', 'required' => false, 'rules' => 'exists:orders,id', 'description' => 'Related order (optional).'],
                        ],
                        'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 201, 'body' => ['message' => 'Conversation started.', 'conversation' => ['id' => 1, 'subject' => 'Order question']]],
                        'error_responses' => [],
                    ],
                    [
                        'name' => 'File Dispute', 'method' => 'POST', 'url' => '/api/v1/disputes',
                        'description' => 'File a dispute for an order. Customer role required.',
                        'auth_required' => true,
                        'request' => [
                            ['name' => 'order_id', 'type' => 'integer', 'required' => true, 'rules' => 'exists:orders,id', 'description' => 'Order to dispute.'],
                            ['name' => 'reason', 'type' => 'string', 'required' => true, 'rules' => 'max:255', 'description' => 'Dispute reason.'],
                            ['name' => 'description', 'type' => 'string', 'required' => true, 'rules' => '', 'description' => 'Detailed description of the issue.'],
                        ],
                        'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 201, 'body' => ['message' => 'Dispute filed.', 'dispute' => ['id' => 1, 'status' => 'open', 'reason' => 'Wrong item received']]],
                        'error_responses' => [['status' => 422, 'description' => 'Active dispute exists', 'body' => ['message' => 'An active dispute already exists for this order.']]],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function analyticsNotificationEndpoints(): array
    {
        return [
            [
                'group' => 'Analytics & Notifications',
                'description' => 'Vendor analytics dashboard and user notifications.',
                'endpoints' => [
                    [
                        'name' => 'Vendor Analytics', 'method' => 'GET', 'url' => '/api/v1/analytics/vendor/overview',
                        'description' => 'Get analytics overview for the authenticated vendor.',
                        'auth_required' => true, 'request' => [], 'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 200, 'body' => ['analytics' => ['total_products' => 25, 'active_products' => 20, 'total_orders' => 150, 'total_revenue' => 450000.00]]],
                        'error_responses' => [['status' => 404, 'description' => 'No vendor profile', 'body' => ['message' => 'No vendor profile found.']]],
                    ],
                    [
                        'name' => 'List Notifications', 'method' => 'GET', 'url' => '/api/v1/notifications',
                        'description' => 'Get paginated notifications for the authenticated user.',
                        'auth_required' => true, 'request' => [['name' => 'per_page', 'type' => 'integer', 'required' => false, 'rules' => '', 'description' => 'Items per page.']], 'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 200, 'body' => ['notifications' => [], 'unread_count' => 3, 'meta' => ['total' => 15]]],
                        'error_responses' => [],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function adminEndpoints(): array
    {
        return [
            [
                'group' => 'Admin',
                'description' => 'Administrative endpoints for platform management. Requires admin role.',
                'endpoints' => [
                    [
                        'name' => 'Dashboard Statistics', 'method' => 'GET', 'url' => '/api/v1/admin/dashboard',
                        'description' => 'Retrieve high-level platform statistics.', 'auth_required' => true, 'request' => [],
                        'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 200, 'body' => ['message' => 'Dashboard statistics retrieved.', 'data' => ['total_users' => 150, 'active_users' => 142, 'total_vendors' => 25, 'total_products' => 340, 'total_orders' => 1200, 'total_revenue' => 58750.00]]],
                        'error_responses' => [['status' => 403, 'description' => 'Forbidden', 'body' => ['message' => 'Forbidden. Admin access required.']]],
                    ],
                    [
                        'name' => 'List Users', 'method' => 'GET', 'url' => '/api/v1/admin/users',
                        'description' => 'Paginated list of all users with filtering.', 'auth_required' => true,
                        'request' => [
                            ['name' => 'status', 'type' => 'string', 'required' => false, 'rules' => 'in:active,blocked', 'description' => 'Filter by status.'],
                            ['name' => 'role', 'type' => 'string', 'required' => false, 'rules' => '', 'description' => 'Filter by role name.'],
                            ['name' => 'search', 'type' => 'string', 'required' => false, 'rules' => '', 'description' => 'Search by name or email.'],
                        ],
                        'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 200, 'body' => ['message' => 'Users retrieved.', 'data' => ['data' => [['id' => 1, 'name' => 'John Doe', 'status' => 'active']], 'total' => 150]]],
                        'error_responses' => [],
                    ],
                    [
                        'name' => 'Update User Status', 'method' => 'PATCH', 'url' => '/api/v1/admin/users/{user}/status',
                        'description' => 'Block or unblock a user.', 'auth_required' => true,
                        'request' => [['name' => 'status', 'type' => 'string', 'required' => true, 'rules' => 'in:active,blocked', 'description' => 'New status.']],
                        'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 200, 'body' => ['message' => 'User status updated to blocked.']],
                        'error_responses' => [['status' => 403, 'description' => 'Cannot change own status', 'body' => ['message' => 'You cannot change your own status.']]],
                    ],
                    [
                        'name' => 'Assign Role', 'method' => 'POST', 'url' => '/api/v1/admin/users/{user}/assign-role',
                        'description' => 'Assign a role to a user.', 'auth_required' => true,
                        'request' => [['name' => 'role', 'type' => 'string', 'required' => true, 'rules' => 'exists:roles,name', 'description' => 'Role name.']],
                        'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 200, 'body' => ['message' => "Role 'vendor' assigned to John Doe."]],
                        'error_responses' => [],
                    ],
                    [
                        'name' => 'Admin Vendors', 'method' => 'GET', 'url' => '/api/v1/admin/vendors',
                        'description' => 'List all vendors with optional status filter.', 'auth_required' => true,
                        'request' => [['name' => 'status', 'type' => 'string', 'required' => false, 'rules' => '', 'description' => 'Filter by vendor status.']],
                        'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 200, 'body' => ['vendors' => [['id' => 1, 'shop_name' => 'TechShop', 'logo' => 'https://cdn.natakahii.com/vendors/logos/techshop.png', 'status' => 'approved']], 'meta' => ['total' => 25]]],
                        'error_responses' => [],
                    ],
                    [
                        'name' => 'Review Vendor Verification', 'method' => 'POST', 'url' => '/api/v1/admin/vendors/{vendor}/verification/review',
                        'description' => 'Approve or suspend a vendor.', 'auth_required' => true,
                        'request' => [['name' => 'status', 'type' => 'string', 'required' => true, 'rules' => 'in:approved,suspended', 'description' => 'New vendor status.']],
                        'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 200, 'body' => ['message' => 'Vendor approved.']],
                        'error_responses' => [],
                    ],
                    [
                        'name' => 'List All Categories', 'method' => 'GET', 'url' => '/api/v1/admin/categories',
                        'description' => 'List all categories including inactive ones with children and product counts.', 'auth_required' => true,
                        'request' => [],
                        'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 200, 'body' => ['categories' => [['id' => 1, 'name' => 'Electronics', 'slug' => 'electronics', 'is_active' => true, 'sort_order' => 1, 'children' => [], 'products_count' => 45]]]],
                        'error_responses' => [],
                    ],
                    [
                        'name' => 'Create Category', 'method' => 'POST', 'url' => '/api/v1/admin/categories',
                        'description' => 'Create a new category. Slug is auto-generated from name if not provided.', 'auth_required' => true,
                        'request' => [
                            ['name' => 'name', 'type' => 'string', 'required' => true, 'rules' => 'max:255', 'description' => 'Category name.'],
                            ['name' => 'slug', 'type' => 'string', 'required' => false, 'rules' => 'max:255|unique:categories', 'description' => 'URL-friendly slug (auto-generated if not provided).'],
                            ['name' => 'parent_id', 'type' => 'integer', 'required' => false, 'rules' => 'exists:categories,id', 'description' => 'Parent category ID for subcategories.'],
                            ['name' => 'is_active', 'type' => 'boolean', 'required' => false, 'rules' => '', 'description' => 'Active status. Default: true.'],
                            ['name' => 'sort_order', 'type' => 'integer', 'required' => false, 'rules' => 'min:0', 'description' => 'Display order. Auto-assigned if not provided.'],
                        ],
                        'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 201, 'body' => ['message' => 'Category created successfully', 'category' => ['id' => 1, 'name' => 'Electronics', 'slug' => 'electronics', 'is_active' => true, 'sort_order' => 1]]],
                        'error_responses' => [
                            ['status' => 422, 'description' => 'Validation failed', 'body' => ['message' => 'The name field is required.', 'errors' => ['name' => ['The name field is required.']]]],
                        ],
                    ],
                    [
                        'name' => 'Update Category', 'method' => 'PATCH', 'url' => '/api/v1/admin/categories/{category}',
                        'description' => 'Update an existing category.', 'auth_required' => true,
                        'request' => [
                            ['name' => 'name', 'type' => 'string', 'required' => false, 'rules' => 'max:255', 'description' => 'Updated category name.'],
                            ['name' => 'slug', 'type' => 'string', 'required' => false, 'rules' => 'max:255|unique:categories', 'description' => 'Updated slug.'],
                            ['name' => 'parent_id', 'type' => 'integer', 'required' => false, 'rules' => 'exists:categories,id', 'description' => 'Updated parent category.'],
                            ['name' => 'is_active', 'type' => 'boolean', 'required' => false, 'rules' => '', 'description' => 'Updated active status.'],
                            ['name' => 'sort_order', 'type' => 'integer', 'required' => false, 'rules' => 'min:0', 'description' => 'Updated sort order.'],
                        ],
                        'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 200, 'body' => ['message' => 'Category updated successfully', 'category' => ['id' => 1, 'name' => 'Electronics & Gadgets', 'slug' => 'electronics-gadgets']]],
                        'error_responses' => [
                            ['status' => 404, 'description' => 'Category not found', 'body' => ['message' => 'Not found.']],
                        ],
                    ],
                    [
                        'name' => 'Delete Category', 'method' => 'DELETE', 'url' => '/api/v1/admin/categories/{category}',
                        'description' => 'Delete a category. Cannot delete if it has products or subcategories.', 'auth_required' => true,
                        'request' => [],
                        'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 200, 'body' => ['message' => 'Category deleted successfully']],
                        'error_responses' => [
                            ['status' => 422, 'description' => 'Cannot delete', 'body' => ['message' => 'Cannot delete category with existing products']],
                            ['status' => 404, 'description' => 'Category not found', 'body' => ['message' => 'Not found.']],
                        ],
                    ],
                    [
                        'name' => 'Toggle Category Status', 'method' => 'PATCH', 'url' => '/api/v1/admin/categories/{category}/toggle-status',
                        'description' => 'Toggle category active/inactive status.', 'auth_required' => true,
                        'request' => [],
                        'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 200, 'body' => ['message' => 'Category status updated successfully', 'category' => ['id' => 1, 'is_active' => false]]],
                        'error_responses' => [
                            ['status' => 404, 'description' => 'Category not found', 'body' => ['message' => 'Not found.']],
                        ],
                    ],
                    [
                        'name' => 'Admin Products', 'method' => 'GET', 'url' => '/api/v1/admin/products',
                        'description' => 'List all products with optional filters.', 'auth_required' => true,
                        'request' => [
                            ['name' => 'status', 'type' => 'string', 'required' => false, 'rules' => '', 'description' => 'Filter by product status.'],
                            ['name' => 'vendor_id', 'type' => 'integer', 'required' => false, 'rules' => '', 'description' => 'Filter by vendor.'],
                        ],
                        'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 200, 'body' => ['products' => [], 'meta' => ['total' => 340]]],
                        'error_responses' => [],
                    ],
                    [
                        'name' => 'Moderate Product', 'method' => 'PATCH', 'url' => '/api/v1/admin/products/{product}/moderation',
                        'description' => 'Change product status (activate, draft, out of stock).', 'auth_required' => true,
                        'request' => [['name' => 'status', 'type' => 'string', 'required' => true, 'rules' => 'in:active,draft,out_of_stock', 'description' => 'New product status.']],
                        'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 200, 'body' => ['message' => 'Product status changed to active.']],
                        'error_responses' => [],
                    ],
                    [
                        'name' => 'Admin Orders', 'method' => 'GET', 'url' => '/api/v1/admin/orders',
                        'description' => 'List all orders with optional status filters.', 'auth_required' => true,
                        'request' => [['name' => 'status', 'type' => 'string', 'required' => false, 'rules' => '', 'description' => 'Filter by order status.']],
                        'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 200, 'body' => ['orders' => [], 'meta' => ['total' => 1200]]],
                        'error_responses' => [],
                    ],
                    [
                        'name' => 'Admin Payments', 'method' => 'GET', 'url' => '/api/v1/admin/payments',
                        'description' => 'List all payments with optional status filter.', 'auth_required' => true,
                        'request' => [['name' => 'status', 'type' => 'string', 'required' => false, 'rules' => '', 'description' => 'Filter by payment status.']],
                        'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 200, 'body' => ['payments' => [], 'meta' => ['total' => 980]]],
                        'error_responses' => [],
                    ],
                    [
                        'name' => 'Process Refund', 'method' => 'POST', 'url' => '/api/v1/admin/refunds',
                        'description' => 'Process a refund for an order.', 'auth_required' => true,
                        'request' => [
                            ['name' => 'order_id', 'type' => 'integer', 'required' => true, 'rules' => 'exists:orders,id', 'description' => 'Order to refund.'],
                            ['name' => 'amount', 'type' => 'number', 'required' => true, 'rules' => 'min:0.01', 'description' => 'Refund amount.'],
                            ['name' => 'reason', 'type' => 'string', 'required' => true, 'rules' => '', 'description' => 'Refund reason.'],
                        ],
                        'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 201, 'body' => ['message' => 'Refund processed.']],
                        'error_responses' => [],
                    ],
                    [
                        'name' => 'Resolve Dispute', 'method' => 'POST', 'url' => '/api/v1/admin/disputes/{dispute}/resolve',
                        'description' => 'Resolve or reject a customer dispute.', 'auth_required' => true,
                        'request' => [
                            ['name' => 'status', 'type' => 'string', 'required' => true, 'rules' => 'in:resolved,rejected', 'description' => 'Resolution status.'],
                            ['name' => 'resolution', 'type' => 'string', 'required' => true, 'rules' => '', 'description' => 'Resolution description.'],
                        ],
                        'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 200, 'body' => ['message' => 'Dispute resolved.']],
                        'error_responses' => [],
                    ],
                    [
                        'name' => 'Admin Analytics', 'method' => 'GET', 'url' => '/api/v1/admin/analytics/overview',
                        'description' => 'Platform-wide analytics overview.', 'auth_required' => true, 'request' => [],
                        'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 200, 'body' => ['analytics' => ['users' => 150, 'vendors' => 20, 'products' => 340, 'orders_total' => 1200, 'revenue_total' => 58750.00]]],
                        'error_responses' => [],
                    ],
                    [
                        'name' => 'Platform Reports', 'method' => 'GET', 'url' => '/api/v1/admin/reports',
                        'description' => 'Get platform summary reports.', 'auth_required' => true, 'request' => [],
                        'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 200, 'body' => ['reports' => ['total_users' => 150, 'total_vendors' => 25, 'total_products' => 340, 'total_orders' => 1200, 'total_revenue' => 58750.00]]],
                        'error_responses' => [],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function superAdminEndpoints(): array
    {
        return [
            [
                'group' => 'Super Admin',
                'description' => 'Super admin only endpoints. Manage admin accounts, settings, fees, plans, and audit logs.',
                'endpoints' => [
                    [
                        'name' => 'List Admin Accounts', 'method' => 'GET', 'url' => '/api/v1/admin/super/admins',
                        'description' => 'List all admin users.', 'auth_required' => true, 'request' => [],
                        'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 200, 'body' => ['admins' => [['id' => 1, 'name' => 'Super Admin', 'email' => 'admin@natakahii.com']]]],
                        'error_responses' => [['status' => 403, 'description' => 'Super admin required', 'body' => ['message' => 'Forbidden. Super admin access required.']]],
                    ],
                    [
                        'name' => 'Create Admin', 'method' => 'POST', 'url' => '/api/v1/admin/super/admins',
                        'description' => 'Create a new admin account.', 'auth_required' => true,
                        'request' => [
                            ['name' => 'name', 'type' => 'string', 'required' => true, 'rules' => 'max:255', 'description' => 'Admin name.'],
                            ['name' => 'email', 'type' => 'string', 'required' => true, 'rules' => 'email|unique:users', 'description' => 'Admin email.'],
                            ['name' => 'password', 'type' => 'string', 'required' => true, 'rules' => 'min:8', 'description' => 'Admin password.'],
                        ],
                        'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 201, 'body' => ['message' => 'Admin account created.', 'admin' => ['id' => 2, 'name' => 'New Admin']]],
                        'error_responses' => [],
                    ],
                    [
                        'name' => 'Delete Admin', 'method' => 'DELETE', 'url' => '/api/v1/admin/super/admins/{user}',
                        'description' => 'Delete an admin account. Cannot delete yourself.', 'auth_required' => true,
                        'request' => [], 'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 200, 'body' => ['message' => 'Admin account deleted.']],
                        'error_responses' => [['status' => 403, 'description' => 'Cannot delete self', 'body' => ['message' => 'Cannot delete your own account.']]],
                    ],
                    [
                        'name' => 'Platform Settings', 'method' => 'GET', 'url' => '/api/v1/admin/super/settings',
                        'description' => 'Get all platform settings grouped by category.', 'auth_required' => true,
                        'request' => [], 'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 200, 'body' => ['settings' => ['general' => [['key' => 'platform_name', 'value' => 'NatakaHii']]]]],
                        'error_responses' => [],
                    ],
                    [
                        'name' => 'Update Settings', 'method' => 'PUT', 'url' => '/api/v1/admin/super/settings',
                        'description' => 'Bulk update platform settings.', 'auth_required' => true,
                        'request' => [['name' => 'settings', 'type' => 'array', 'required' => true, 'rules' => '', 'description' => 'Array of {key, value, group} objects.']],
                        'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 200, 'body' => ['message' => 'Settings updated.']],
                        'error_responses' => [],
                    ],
                    [
                        'name' => 'Create Platform Fee Rule', 'method' => 'POST', 'url' => '/api/v1/admin/super/platform-fees',
                        'description' => 'Create a new platform fee rule.', 'auth_required' => true,
                        'request' => [
                            ['name' => 'name', 'type' => 'string', 'required' => true, 'rules' => 'max:255', 'description' => 'Rule name.'],
                            ['name' => 'type', 'type' => 'string', 'required' => true, 'rules' => 'in:percentage,flat', 'description' => 'Fee type.'],
                            ['name' => 'value', 'type' => 'number', 'required' => true, 'rules' => 'min:0', 'description' => 'Fee value.'],
                        ],
                        'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 201, 'body' => ['message' => 'Platform fee rule created.', 'rule' => ['name' => 'Commission', 'type' => 'percentage', 'value' => '10.00']]],
                        'error_responses' => [],
                    ],
                    [
                        'name' => 'Create Subscription Plan', 'method' => 'POST', 'url' => '/api/v1/admin/super/subscription-plans',
                        'description' => 'Create a new subscription plan for vendors.', 'auth_required' => true,
                        'request' => [
                            ['name' => 'name', 'type' => 'string', 'required' => true, 'rules' => 'max:255', 'description' => 'Plan name.'],
                            ['name' => 'price', 'type' => 'number', 'required' => true, 'rules' => 'min:0', 'description' => 'Plan price.'],
                            ['name' => 'billing_cycle', 'type' => 'string', 'required' => true, 'rules' => 'in:monthly,yearly', 'description' => 'Billing cycle.'],
                            ['name' => 'features', 'type' => 'array', 'required' => false, 'rules' => '', 'description' => 'Array of feature strings.'],
                        ],
                        'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 201, 'body' => ['message' => 'Subscription plan created.', 'plan' => ['name' => 'Premium', 'price' => '50000.00']]],
                        'error_responses' => [],
                    ],
                    [
                        'name' => 'Audit Logs', 'method' => 'GET', 'url' => '/api/v1/admin/super/audit-logs',
                        'description' => 'View platform audit logs with optional filters.', 'auth_required' => true,
                        'request' => [
                            ['name' => 'action', 'type' => 'string', 'required' => false, 'rules' => '', 'description' => 'Filter by action type.'],
                            ['name' => 'user_id', 'type' => 'integer', 'required' => false, 'rules' => '', 'description' => 'Filter by user.'],
                        ],
                        'headers' => ['Authorization' => 'Bearer {token}'],
                        'success_response' => ['status' => 200, 'body' => ['audit_logs' => [['id' => 1, 'action' => 'user.blocked', 'user' => ['name' => 'Admin']]], 'meta' => ['total' => 500]]],
                        'error_responses' => [],
                    ],
                ],
            ],
        ];
    }
}
