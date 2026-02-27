<?php

use App\Http\Controllers\Api\Admin\AdminAnalyticsController;
use App\Http\Controllers\Api\Admin\AdminController;
use App\Http\Controllers\Api\Admin\CategoryAdminController;
use App\Http\Controllers\Api\Admin\DisputeAdminController;
use App\Http\Controllers\Api\Admin\EscrowAdminController;
use App\Http\Controllers\Api\Admin\OrderAdminController;
use App\Http\Controllers\Api\Admin\PaymentAdminController;
use App\Http\Controllers\Api\Admin\ProductAdminController;
use App\Http\Controllers\Api\Admin\RefundAdminController;
use App\Http\Controllers\Api\Admin\ReportAdminController;
use App\Http\Controllers\Api\Admin\ShipmentAdminController;
use App\Http\Controllers\Api\Admin\Super\AdminAccountController;
use App\Http\Controllers\Api\Admin\Super\AuditLogController;
use App\Http\Controllers\Api\Admin\Super\PlatformFeeRuleController;
use App\Http\Controllers\Api\Admin\Super\SettingsController;
use App\Http\Controllers\Api\Admin\Super\SubscriptionPlanController;
use App\Http\Controllers\Api\Admin\SupportAdminController;
use App\Http\Controllers\Api\Admin\UserManagementController;
use App\Http\Controllers\Api\Admin\VendorAdminController;
use App\Http\Controllers\Api\AIChatController;
use App\Http\Controllers\Api\AlertController;
use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Cargo\CargoShipmentController;
use App\Http\Controllers\Api\Cargo\DeliveryRunController;
use App\Http\Controllers\Api\Cargo\FulfillmentCenterController;
use App\Http\Controllers\Api\Cargo\ShipmentTrackingController;
use App\Http\Controllers\Api\Cargo\VendorDropoffController as CargoDropoffController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\DisputeController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\MessagingController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\PublicVendorController;
use App\Http\Controllers\Api\Shipping\ShippingQuoteController;
use App\Http\Controllers\Api\Social\NotInterestedController;
use App\Http\Controllers\Api\Social\ProductLikeController;
use App\Http\Controllers\Api\Social\ProductShareController;
use App\Http\Controllers\Api\Social\ProductViewController;
use App\Http\Controllers\Api\Social\VendorFollowController;
use App\Http\Controllers\Api\UserProfileController;
use App\Http\Controllers\Api\VariantController;
use App\Http\Controllers\Api\Vendor\VendorDropoffController;
use App\Http\Controllers\Api\Vendor\VendorProductController;
use App\Http\Controllers\Api\Vendor\VendorProfileController;
use App\Http\Controllers\Api\VideoController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\Api\VendorApplicationController;

Route::prefix('v1')->group(function () {

    /**
     * =========================
     * AUTHENTICATION (PUBLIC)
     * =========================
     */
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

    /**
     * =========================
     * USER PROFILE
     * =========================
     */
    Route::middleware('auth:api')->prefix('profile')->group(function () {
        Route::patch('/', [UserProfileController::class, 'update']);
        Route::post('/photo', [UserProfileController::class, 'updatePhoto']);
        Route::delete('/photo', [UserProfileController::class, 'destroyPhoto']);
    });

    /**
     * =========================
     * VENDOR APPLICATION
     * =========================
     */
    Route::middleware('auth:api')->prefix('vendor-application')->group(function () {
        Route::get('/status', [VendorApplicationController::class, 'status']);
        Route::post('/', [VendorApplicationController::class, 'store']);
    });

    /**
     * =========================
     * CATALOG (PUBLIC)
     * =========================
     */
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{category}/filters', [CategoryController::class, 'filters']);

    Route::get('/vendors', [PublicVendorController::class, 'index']);

    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{product}', [ProductController::class, 'show']);
    Route::get('/products/{product}/variants/resolve', [VariantController::class, 'resolve']);

    /**
     * =========================
     * MEDIA & VIDEO COMMERCE
     * =========================
     */
    Route::get('/products/{product}/media', [MediaController::class, 'index']);
    Route::get('/videos/feed', [VideoController::class, 'feed']);

    Route::middleware(['auth:api', 'role:vendor'])->prefix('vendor')->group(function () {
        Route::post('/products/{product}/media', [MediaController::class, 'store']);
        Route::patch('/media/{media}', [MediaController::class, 'update']);
        Route::delete('/media/{media}', [MediaController::class, 'destroy']);
    });

    /**
     * =========================
     * SOCIAL COMMERCE & ENGAGEMENT
     * =========================
     */
    Route::post('/products/{product}/view', [ProductViewController::class, 'store']);
    Route::post('/products/{product}/shares', [ProductShareController::class, 'store']);

    Route::middleware(['auth:api', 'role:customer'])->group(function () {
        Route::post('/products/{product}/likes', [ProductLikeController::class, 'store']);
        Route::delete('/products/{product}/likes', [ProductLikeController::class, 'destroy']);

        Route::post('/vendors/{vendor}/follow', [VendorFollowController::class, 'store']);
        Route::delete('/vendors/{vendor}/follow', [VendorFollowController::class, 'destroy']);
        Route::get('/me/following/vendors', [VendorFollowController::class, 'index']);

        Route::post('/products/{product}/not-interested', [NotInterestedController::class, 'store']);
        Route::get('/me/not-interested', [NotInterestedController::class, 'index']);
    });

    /**
     * =========================
     * CART / WISHLIST / ALERTS
     * =========================
     */
    Route::middleware('auth:api')->group(function () {
        Route::get('/cart', [CartController::class, 'show']);
        Route::post('/cart/items', [CartController::class, 'addItem']);
    });

    Route::middleware(['auth:api', 'role:customer'])->group(function () {
        Route::get('/wishlists', [WishlistController::class, 'index']);
        Route::post('/wishlists/toggle', [WishlistController::class, 'toggle']);
        Route::post('/alerts', [AlertController::class, 'store']);
    });

    /**
     * =========================
     * SHIPPING QUOTES
     * =========================
     */
    Route::prefix('shipping')->group(function () {
        Route::post('/quotes', [ShippingQuoteController::class, 'store']);
        Route::post('/quotes/{quote}/select', [ShippingQuoteController::class, 'select']);
    });

    /**
     * =========================
     * CHECKOUT / PAYMENTS
     * =========================
     */
    Route::middleware('auth:api')->group(function () {
        Route::post('/checkout', [CheckoutController::class, 'store']);
        Route::post('/payments', [PaymentController::class, 'store']);
    });
    Route::post('/payments/webhook/{provider}', [PaymentController::class, 'webhook']);

    /**
     * =========================
     * AI SHOPPING ASSISTANT
     * =========================
     */
    Route::prefix('ai')->group(function () {
        Route::post('/ask', [AIChatController::class, 'ask']);

        Route::middleware('auth:api')->group(function () {
            Route::get('/conversations', [AIChatController::class, 'index']);
            Route::post('/conversations', [AIChatController::class, 'store']);
            Route::get('/conversations/{conversation}', [AIChatController::class, 'show']);
            Route::post('/conversations/{conversation}/messages', [AIChatController::class, 'sendMessage']);
            Route::post('/recommendation-events', [AIChatController::class, 'trackRecommendationEvent']);
            Route::get('/recommendations', [AIChatController::class, 'recommendations']);
        });
    });

    /**
     * =========================
     * SHIPPING / CARGO / TRACKING
     * =========================
     */
    Route::middleware(['auth:api', 'role:vendor'])->prefix('vendor')->group(function () {
        Route::post('/dropoffs', [VendorDropoffController::class, 'store']);
        Route::get('/dropoffs', [VendorDropoffController::class, 'index']);
    });

    Route::middleware(['auth:api', 'role_any:delivery_agent,admin'])->prefix('cargo')->group(function () {
        Route::get('/fulfillment-centers', [FulfillmentCenterController::class, 'index']);
        Route::get('/dropoffs', [CargoDropoffController::class, 'index']);

        Route::post('/dropoffs/{dropoff}/receive', [CargoDropoffController::class, 'receive']);
        Route::patch('/dropoffs/{dropoff}/items', [CargoDropoffController::class, 'updateItems']);

        Route::post('/dropoffs/{dropoff}/qc/start', [CargoDropoffController::class, 'qcStart']);
        Route::post('/dropoffs/{dropoff}/qc/complete', [CargoDropoffController::class, 'qcComplete']);

        Route::get('/my/delivery-runs', [DeliveryRunController::class, 'myRuns']);

        Route::post('/shipments/{shipment}/tracking-events', [ShipmentTrackingController::class, 'store']);
        Route::post('/shipments/{shipment}/mark-delivered', [ShipmentTrackingController::class, 'markDelivered']);
    });

    Route::middleware(['auth:api', 'admin.level:normal_admin,super_admin'])->prefix('cargo')->group(function () {
        Route::post('/shipments', [CargoShipmentController::class, 'store']);
    });

    Route::middleware('auth:api')->get('/shipments/{shipment}/tracking', [ShipmentTrackingController::class, 'show']);

    /**
     * =========================
     * VENDOR (Inventory & Store)
     * =========================
     */
    Route::middleware(['auth:api', 'role:vendor'])->prefix('vendor')->group(function () {
        Route::post('/profile', [VendorProfileController::class, 'store']);
        Route::post('/products', [VendorProductController::class, 'store']);
    });

    /**
     * =========================
     * MESSAGING
     * =========================
     */
    Route::middleware('auth:api')->prefix('messages')->group(function () {
        Route::post('/conversations', [MessagingController::class, 'startConversation']);
    });

    /**
     * =========================
     * DISPUTES
     * =========================
     */
    Route::middleware(['auth:api', 'role:customer'])->post('/disputes', [DisputeController::class, 'store']);

    /**
     * =========================
     * ANALYTICS
     * =========================
     */
    Route::middleware(['auth:api', 'role:vendor'])->prefix('analytics')->group(function () {
        Route::get('/vendor/overview', [AnalyticsController::class, 'vendorOverview']);
    });

    /**
     * =========================
     * NOTIFICATIONS
     * =========================
     */
    Route::middleware('auth:api')->get('/notifications', [NotificationController::class, 'index']);

    /**
     * =========================
     * ADMIN (NORMAL ADMIN)
     * =========================
     */
    Route::middleware(['auth:api', 'admin.level:normal_admin,super_admin'])->prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard']);

        Route::get('/users', [UserManagementController::class, 'index']);
        Route::get('/users/{user}', [UserManagementController::class, 'show']);
        Route::patch('/users/{user}/status', [UserManagementController::class, 'updateStatus']);
        Route::post('/users/{user}/assign-role', [UserManagementController::class, 'assignRole']);
        Route::delete('/users/{user}/revoke-role', [UserManagementController::class, 'revokeRole']);

        Route::get('/roles', [UserManagementController::class, 'roles']);

        Route::get('/vendors', [VendorAdminController::class, 'index']);
        Route::post('/vendors/{vendor}/verification/review', [VendorAdminController::class, 'reviewVerification']);

        Route::get('/vendor-applications', [VendorApplicationController::class, 'index']);
        Route::get('/vendor-applications/{application}', [VendorApplicationController::class, 'show']);
        Route::patch('/vendor-applications/{application}/status', [VendorApplicationController::class, 'updateStatus']);

        Route::get('/categories', [CategoryAdminController::class, 'index']);
        Route::post('/categories', [CategoryAdminController::class, 'store']);
        Route::patch('/categories/{category}', [CategoryAdminController::class, 'update']);
        Route::delete('/categories/{category}', [CategoryAdminController::class, 'destroy']);
        Route::patch('/categories/{category}/toggle-status', [CategoryAdminController::class, 'toggleStatus']);

        Route::get('/products', [ProductAdminController::class, 'index']);
        Route::patch('/products/{product}/moderation', [ProductAdminController::class, 'moderate']);

        Route::get('/orders', [OrderAdminController::class, 'index']);
        Route::get('/payments', [PaymentAdminController::class, 'index']);
        Route::get('/escrow/orders/{order}', [EscrowAdminController::class, 'showOrder']);

        Route::post('/refunds', [RefundAdminController::class, 'store']);

        Route::get('/shipments', [ShipmentAdminController::class, 'index']);
        Route::get('/cargo/shipments/{shipment}/inspections', [ShipmentAdminController::class, 'inspections']);

        Route::get('/disputes', [DisputeAdminController::class, 'index']);
        Route::post('/disputes/{dispute}/resolve', [DisputeAdminController::class, 'resolve']);

        Route::get('/support/tickets', [SupportAdminController::class, 'tickets']);

        Route::get('/reports', [ReportAdminController::class, 'index']);
        Route::post('/reports/{report}/action', [ReportAdminController::class, 'action']);

        Route::post('/delivery-runs', [DeliveryRunController::class, 'store']);
        Route::post('/delivery-runs/{run}/shipments', [DeliveryRunController::class, 'assignShipments']);
        Route::post('/delivery-runs/{run}/dispatch', [DeliveryRunController::class, 'dispatch']);

        Route::get('/analytics/overview', [AdminAnalyticsController::class, 'overview']);
    });

    /**
     * =========================
     * ADMIN (SUPER ADMIN)
     * =========================
     */
    Route::middleware(['auth:api', 'admin.level:super_admin'])->prefix('admin/super')->group(function () {
        Route::get('/admins', [AdminAccountController::class, 'index']);
        Route::post('/admins', [AdminAccountController::class, 'store']);
        Route::patch('/admins/{user}', [AdminAccountController::class, 'update']);
        Route::delete('/admins/{user}', [AdminAccountController::class, 'destroy']);

        Route::get('/settings', [SettingsController::class, 'show']);
        Route::put('/settings', [SettingsController::class, 'update']);

        Route::post('/platform-fees', [PlatformFeeRuleController::class, 'store']);
        Route::post('/subscription-plans', [SubscriptionPlanController::class, 'store']);

        Route::get('/audit-logs', [AuditLogController::class, 'index']);
    });
});
