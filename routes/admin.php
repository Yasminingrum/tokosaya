<?php
// File: routes/admin.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\BrandController as AdminBrandController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Admin\BannerController as AdminBannerController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\ShippingController as AdminShippingController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Admin routes with authentication and authorization middleware
|
*/

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {

    // Dashboard
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/analytics', [AdminDashboardController::class, 'analytics'])->name('analytics');
    Route::get('/reports', [AdminDashboardController::class, 'reports'])->name('reports');
    Route::get('/sales-chart', [AdminDashboardController::class, 'salesChart'])->name('sales-chart');
    Route::get('/overview', [AdminDashboardController::class, 'overview'])->name('overview');

    // Product Management
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [AdminProductController::class, 'index'])->name('index');
        Route::get('/create', [AdminProductController::class, 'create'])->name('create');
        Route::post('/', [AdminProductController::class, 'store'])->name('store');
        Route::get('/{product}', [AdminProductController::class, 'show'])->name('show');
        Route::get('/{product}/edit', [AdminProductController::class, 'edit'])->name('edit');
        Route::put('/{product}', [AdminProductController::class, 'update'])->name('update');
        Route::delete('/{product}', [AdminProductController::class, 'destroy'])->name('destroy');

        // Bulk operations
        Route::put('/bulk-update', [AdminProductController::class, 'bulkUpdate'])->name('bulk-update');
        Route::post('/import', [AdminProductController::class, 'import'])->name('import');
        Route::get('/export', [AdminProductController::class, 'export'])->name('export');

        // Product actions
        Route::post('/{product}/duplicate', [AdminProductController::class, 'duplicate'])->name('duplicate');
        Route::put('/{product}/stock', [AdminProductController::class, 'updateStock'])->name('update-stock');
        Route::put('/{product}/toggle', [AdminProductController::class, 'toggleStatus'])->name('toggle');

        // Product Images
        Route::prefix('{product}/images')->name('images.')->group(function () {
            Route::post('/', [ProductImageController::class, 'store'])->name('store');
            Route::put('/{image}', [ProductImageController::class, 'update'])->name('update');
            Route::delete('/{image}', [ProductImageController::class, 'destroy'])->name('destroy');
            Route::put('/reorder', [ProductImageController::class, 'reorder'])->name('reorder');
            Route::put('/{image}/primary', [ProductImageController::class, 'setPrimary'])->name('primary');
        });

        // Product Variants
        Route::prefix('{product}/variants')->name('variants.')->group(function () {
            Route::get('/', [ProductVariantController::class, 'index'])->name('index');
            Route::get('/create', [ProductVariantController::class, 'create'])->name('create');
            Route::post('/', [ProductVariantController::class, 'store'])->name('store');
            Route::get('/{variant}/edit', [ProductVariantController::class, 'edit'])->name('edit');
            Route::put('/{variant}', [ProductVariantController::class, 'update'])->name('update');
            Route::delete('/{variant}', [ProductVariantController::class, 'destroy'])->name('destroy');
            Route::put('/{variant}/stock', [ProductVariantController::class, 'updateStock'])->name('update-stock');
        });
    });

    // Category Management
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [AdminCategoryController::class, 'index'])->name('index');
        Route::get('/create', [AdminCategoryController::class, 'create'])->name('create');
        Route::post('/', [AdminCategoryController::class, 'store'])->name('store');
        Route::get('/{category}/edit', [AdminCategoryController::class, 'edit'])->name('edit');
        Route::put('/{category}', [AdminCategoryController::class, 'update'])->name('update');
        Route::delete('/{category}', [AdminCategoryController::class, 'destroy'])->name('destroy');
        Route::put('/reorder', [AdminCategoryController::class, 'reorder'])->name('reorder');
        Route::put('/{category}/toggle', [AdminCategoryController::class, 'toggleStatus'])->name('toggle');
        Route::get('/tree', [AdminCategoryController::class, 'getTree'])->name('tree');
    });

    // Brand Management
    Route::prefix('brands')->name('brands.')->group(function () {
        Route::get('/', [AdminBrandController::class, 'index'])->name('index');
        Route::get('/create', [AdminBrandController::class, 'create'])->name('create');
        Route::post('/', [AdminBrandController::class, 'store'])->name('store');
        Route::get('/{brand}/edit', [AdminBrandController::class, 'edit'])->name('edit');
        Route::put('/{brand}', [AdminBrandController::class, 'update'])->name('update');
        Route::delete('/{brand}', [AdminBrandController::class, 'destroy'])->name('destroy');
    });

    // Order Management
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [AdminOrderController::class, 'index'])->name('index');
        Route::get('/{order}', [AdminOrderController::class, 'show'])->name('show');
        Route::put('/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('update-status');
        Route::post('/{order}/notes', [AdminOrderController::class, 'addNote'])->name('add-note');
        Route::get('/{order}/invoice', [AdminOrderController::class, 'printInvoice'])->name('invoice');
        Route::get('/{order}/label', [AdminOrderController::class, 'printLabel'])->name('label');
        Route::get('/export', [AdminOrderController::class, 'export'])->name('export');
        Route::put('/bulk-update', [AdminOrderController::class, 'bulkUpdate'])->name('bulk-update');
    });

    // User Management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [AdminUserController::class, 'index'])->name('index');
        Route::get('/create', [AdminUserController::class, 'create'])->name('create');
        Route::post('/', [AdminUserController::class, 'store'])->name('store');
        Route::get('/{user}', [AdminUserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [AdminUserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [AdminUserController::class, 'update'])->name('update');
        Route::delete('/{user}', [AdminUserController::class, 'destroy'])->name('destroy');
        Route::put('/{user}/toggle', [AdminUserController::class, 'toggleStatus'])->name('toggle');
        Route::put('/{user}/reset-password', [AdminUserController::class, 'resetPassword'])->name('reset-password');
    });

    // Payment Management
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [AdminPaymentController::class, 'index'])->name('index');
        Route::get('/{payment}', [AdminPaymentController::class, 'show'])->name('show');
        Route::put('/{payment}/approve', [AdminPaymentController::class, 'approve'])->name('approve');
        Route::put('/{payment}/reject', [AdminPaymentController::class, 'reject'])->name('reject');
    });

    // Inventory Management
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/', [InventoryController::class, 'dashboard'])->name('dashboard');
        Route::get('/alerts', [InventoryController::class, 'alerts'])->name('alerts');
        Route::get('/low-stock', [InventoryController::class, 'lowStock'])->name('low-stock');
        Route::get('/out-of-stock', [InventoryController::class, 'outOfStock'])->name('out-of-stock');
        Route::put('/adjust', [InventoryController::class, 'adjustStock'])->name('adjust');
        Route::put('/bulk-update', [InventoryController::class, 'bulkUpdate'])->name('bulk-update');
        Route::get('/reports', [InventoryController::class, 'reports'])->name('reports');
    });

    // Coupon Management
    Route::prefix('coupons')->name('coupons.')->group(function () {
        Route::get('/', [AdminCouponController::class, 'index'])->name('index');
        Route::get('/create', [AdminCouponController::class, 'create'])->name('create');
        Route::post('/', [AdminCouponController::class, 'store'])->name('store');
        Route::get('/{coupon}/edit', [AdminCouponController::class, 'edit'])->name('edit');
        Route::put('/{coupon}', [AdminCouponController::class, 'update'])->name('update');
        Route::delete('/{coupon}', [AdminCouponController::class, 'destroy'])->name('destroy');
        Route::get('/{coupon}/usage', [AdminCouponController::class, 'usage'])->name('usage');
    });

    // Banner Management
    Route::prefix('banners')->name('banners.')->group(function () {
        Route::get('/', [AdminBannerController::class, 'index'])->name('index');
        Route::get('/create', [AdminBannerController::class, 'create'])->name('create');
        Route::post('/', [AdminBannerController::class, 'store'])->name('store');
        Route::get('/{banner}/edit', [AdminBannerController::class, 'edit'])->name('edit');
        Route::put('/{banner}', [AdminBannerController::class, 'update'])->name('update');
        Route::delete('/{banner}', [AdminBannerController::class, 'destroy'])->name('destroy');
        Route::put('/reorder', [AdminBannerController::class, 'reorder'])->name('reorder');
    });

    // Review Management
    Route::prefix('reviews')->name('reviews.')->group(function () {
        Route::get('/', [AdminReviewController::class, 'index'])->name('index');
        Route::get('/{review}', [AdminReviewController::class, 'show'])->name('show');
        Route::put('/{review}/approve', [AdminReviewController::class, 'approve'])->name('approve');
        Route::put('/{review}/reject', [AdminReviewController::class, 'reject'])->name('reject');
        Route::delete('/{review}', [AdminReviewController::class, 'destroy'])->name('destroy');
    });

    // Shipping Management
    Route::prefix('shipping')->name('shipping.')->group(function () {
        // Shipping Methods
        Route::prefix('methods')->name('methods.')->group(function () {
            Route::get('/', [AdminShippingController::class, 'methods'])->name('index');
            Route::get('/create', [AdminShippingController::class, 'createMethod'])->name('create');
            Route::post('/', [AdminShippingController::class, 'storeMethod'])->name('store');
            Route::get('/{method}/edit', [AdminShippingController::class, 'editMethod'])->name('edit');
            Route::put('/{method}', [AdminShippingController::class, 'updateMethod'])->name('update');
            Route::delete('/{method}', [AdminShippingController::class, 'destroyMethod'])->name('destroy');
        });

        // Shipping Zones
        Route::prefix('zones')->name('zones.')->group(function () {
            Route::get('/', [AdminShippingController::class, 'zones'])->name('index');
            Route::get('/create', [AdminShippingController::class, 'createZone'])->name('create');
            Route::post('/', [AdminShippingController::class, 'storeZone'])->name('store');
            Route::get('/{zone}/edit', [AdminShippingController::class, 'editZone'])->name('edit');
            Route::put('/{zone}', [AdminShippingController::class, 'updateZone'])->name('update');
            Route::delete('/{zone}', [AdminShippingController::class, 'destroyZone'])->name('destroy');
        });

        // Shipping Rates
        Route::prefix('rates')->name('rates.')->group(function () {
            Route::get('/', [AdminShippingController::class, 'rates'])->name('index');
            Route::get('/create', [AdminShippingController::class, 'createRate'])->name('create');
            Route::post('/', [AdminShippingController::class, 'storeRate'])->name('store');
            Route::get('/{rate}/edit', [AdminShippingController::class, 'editRate'])->name('edit');
            Route::put('/{rate}', [AdminShippingController::class, 'updateRate'])->name('update');
            Route::delete('/{rate}', [AdminShippingController::class, 'destroyRate'])->name('destroy');
        });
    });

    // Analytics
    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/', [AnalyticsController::class, 'dashboard'])->name('dashboard');
        Route::get('/sales', [AnalyticsController::class, 'sales'])->name('sales');
        Route::get('/products', [AnalyticsController::class, 'products'])->name('products');
        Route::get('/customers', [AnalyticsController::class, 'customers'])->name('customers');
        Route::get('/traffic', [AnalyticsController::class, 'traffic'])->name('traffic');
        Route::get('/export', [AnalyticsController::class, 'export'])->name('export');
    });

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportsController::class, 'index'])->name('index');
        Route::get('/sales', [ReportsController::class, 'sales'])->name('sales');
        Route::get('/inventory', [ReportsController::class, 'inventory'])->name('inventory');
        Route::get('/customers', [ReportsController::class, 'customers'])->name('customers');
        Route::get('/products', [ReportsController::class, 'products'])->name('products');
        Route::get('/financial', [ReportsController::class, 'financial'])->name('financial');
        Route::post('/generate', [ReportsController::class, 'generate'])->name('generate');
        Route::get('/{report}/export', [ReportsController::class, 'export'])->name('export');
    });

    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::get('/general', [SettingsController::class, 'general'])->name('general');
        Route::get('/ecommerce', [SettingsController::class, 'ecommerce'])->name('ecommerce');
        Route::get('/shipping', [SettingsController::class, 'shipping'])->name('shipping');
        Route::get('/payment', [SettingsController::class, 'payment'])->name('payment');
        Route::get('/email', [SettingsController::class, 'email'])->name('email');
        Route::put('/', [SettingsController::class, 'update'])->name('update');
    });

    // Media Management
    Route::prefix('media')->name('media.')->group(function () {
        Route::get('/', [MediaController::class, 'index'])->name('index');
        Route::post('/upload', [MediaController::class, 'upload'])->name('upload');
        Route::delete('/{media}', [MediaController::class, 'delete'])->name('delete');
        Route::put('/organize', [MediaController::class, 'organize'])->name('organize');
        Route::get('/browse', [MediaController::class, 'browse'])->name('browse');
    });

    // CMS Pages
    Route::prefix('pages')->name('pages.')->group(function () {
        Route::get('/', [PageController::class, 'adminIndex'])->name('index');
        Route::get('/create', [PageController::class, 'create'])->name('create');
        Route::post('/', [PageController::class, 'store'])->name('store');
        Route::get('/{page}/edit', [PageController::class, 'edit'])->name('edit');
        Route::put('/{page}', [PageController::class, 'update'])->name('update');
        Route::delete('/{page}', [PageController::class, 'destroy'])->name('destroy');
    });

    // Newsletter Management
    Route::prefix('newsletter')->name('newsletter.')->group(function () {
        Route::get('/', [NewsletterController::class, 'adminIndex'])->name('index');
        Route::post('/send', [NewsletterController::class, 'send'])->name('send');
        Route::get('/templates', [NewsletterController::class, 'templates'])->name('templates');
    });

    // Notification Management
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [AdminNotificationController::class, 'index'])->name('index');
        Route::post('/send', [AdminNotificationController::class, 'send'])->name('send');
        Route::get('/templates', [AdminNotificationController::class, 'templates'])->name('templates');
    });

    // System Management
    Route::prefix('system')->name('system.')->group(function () {
        Route::get('/', [SystemController::class, 'info'])->name('info');
        Route::get('/maintenance', [SystemController::class, 'maintenance'])->name('maintenance');
        Route::post('/cache/clear', [SystemController::class, 'cache'])->name('cache.clear');
        Route::get('/logs', [SystemController::class, 'logs'])->name('logs');
        Route::post('/backup', [SystemController::class, 'backup'])->name('backup');
        Route::post('/optimize', [SystemController::class, 'optimize'])->name('optimize');
    });

    // Activity Logs
    Route::prefix('activity-logs')->name('activity-logs.')->group(function () {
        Route::get('/', [ActivityLogController::class, 'index'])->name('index');
        Route::get('/{log}', [ActivityLogController::class, 'show'])->name('show');
        Route::delete('/{log}', [ActivityLogController::class, 'destroy'])->name('destroy');
        Route::get('/export', [ActivityLogController::class, 'export'])->name('export');
    });

    // Role & Permission Management
    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('index');
        Route::get('/create', [RoleController::class, 'create'])->name('create');
        Route::post('/', [RoleController::class, 'store'])->name('store');
        Route::get('/{role}', [RoleController::class, 'show'])->name('show');
        Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('edit');
        Route::put('/{role}', [RoleController::class, 'update'])->name('update');
        Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy');
    });
});
