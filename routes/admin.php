<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\OrderController;

/*
|--------------------------------------------------------------------------
| TokoSaya Admin Routes
|--------------------------------------------------------------------------
|
| These routes handle admin functionality for the application, based on existing
| controllers. Routes for ReviewController, ProductController, CategoryController,
| PaymentController, and OrderController have been added to manage respective resources.
|
| Prefix: /admin
| Middleware: web, auth, AdminMiddleware
| Name: admin.*
|
*/

Route::middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])->prefix('admin')->name('admin.')->group(function () {

    // ========================================================================
    // ADMIN DASHBOARD
    // ========================================================================

    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard.index');

    // Dashboard data endpoints
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/stats', [AdminDashboardController::class, 'getStats'])->name('stats');
        Route::get('/sales-chart', [AdminDashboardController::class, 'getSalesChart'])->name('sales-chart');
        Route::get('/recent-orders', [AdminDashboardController::class, 'getRecentOrders'])->name('recent-orders');
        Route::get('/top-products', [AdminDashboardController::class, 'getTopProducts'])->name('top-products');
        Route::get('/low-stock', [AdminDashboardController::class, 'getLowStock'])->name('low-stock');
    });

    // ========================================================================
    // PRODUCTS MANAGEMENT
    // ========================================================================

    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [ProductController::class, 'adminIndex'])->name('index');
        Route::get('/create', [ProductController::class, 'create'])->name('create');
        Route::post('/', [ProductController::class, 'store'])->name('store');
        Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
        Route::put('/{product}', [ProductController::class, 'update'])->name('update');
        Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');
        Route::post('/{product}/stock', [ProductController::class, 'updateStock'])->name('stock.update');
        Route::put('/{product}/status', [ProductController::class, 'toggleStatus'])->name('status.toggle');
    });

    // ========================================================================
    // CATEGORIES MANAGEMENT
    // ========================================================================

    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'adminIndex'])->name('index');
        Route::get('/create', [CategoryController::class, 'create'])->name('create');
        Route::post('/', [CategoryController::class, 'store'])->name('store');
        Route::get('/{category}/edit', [CategoryController::class, 'edit'])->name('edit');
        Route::put('/{category}', [CategoryController::class, 'update'])->name('update');
        Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('destroy');
        Route::post('/reorder', [CategoryController::class, 'reorder'])->name('reorder');
        Route::put('/{category}/status', [CategoryController::class, 'toggleStatus'])->name('status.toggle');
    });

    // ========================================================================
    // PAYMENTS MANAGEMENT
    // ========================================================================

    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [PaymentController::class, 'adminIndex'])->name('index');
        Route::get('/{payment}', [PaymentController::class, 'adminShow'])->name('show');
        Route::post('/{payment}/approve', [PaymentController::class, 'approve'])->name('approve');
        Route::post('/{payment}/reject', [PaymentController::class, 'reject'])->name('reject');
    });

    // ========================================================================
    // ORDERS MANAGEMENT
    // ========================================================================

    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'adminIndex'])->name('index');
        Route::get('/{order}', [OrderController::class, 'adminShow'])->name('show');
        Route::post('/{order}/status', [OrderController::class, 'updateStatus'])->name('status.update');
        Route::get('/{order}/invoice', [OrderController::class, 'printInvoice'])->name('orders.print_invoice');
    });

    // ========================================================================
    // REVIEWS MANAGEMENT
    // ========================================================================

    Route::prefix('reviews')->name('reviews.')->group(function () {
        Route::get('/', [ReviewController::class, 'adminIndex'])->name('index');
        Route::get('/{review}', [ReviewController::class, 'adminShow'])->name('show');
        Route::post('/{review}/approve', [ReviewController::class, 'approve'])->name('approve');
        Route::post('/{review}/reject', [ReviewController::class, 'reject'])->name('reject');
        Route::post('/{review}/destroy', [ReviewController::class, 'destroy'])->name('destroy');
    });

    // ========================================================================
    // SIMPLE ADMIN ROUTES (Using Views Only)
    // ========================================================================

    Route::get('/users', function () {
        return view('admin.users.index');
    })->name('users.index');

    Route::get('/analytics', function () {
        return view('admin.analytics');
    })->name('analytics');

    Route::get('/settings', function () {
        return view('admin.settings');
    })->name('settings');

    // ========================================================================
    // AJAX ENDPOINTS FOR EXISTING DASHBOARD
    // ========================================================================

    Route::prefix('ajax')->name('ajax.')->group(function () {
        Route::get('/dashboard/live-stats', [AdminDashboardController::class, 'getLiveStats'])->name('dashboard.live-stats');
        Route::get('/dashboard/quick-stats', [AdminDashboardController::class, 'getQuickStats'])->name('dashboard.quick-stats');
        Route::get('/search', [AdminDashboardController::class, 'quickSearch'])->name('search');
    });

});

/*
|--------------------------------------------------------------------------
| Admin Route Information
|--------------------------------------------------------------------------
|
| Currently includes routes for AdminDashboardController, ReviewController,
| ProductController, CategoryController, PaymentController, and OrderController.
|
| To expand admin functionality, create additional controllers such as:
| - AdminUserController
| - AdminBrandController
| - AdminCouponController
| - AdminSettingController
|
| Then add corresponding routes under the appropriate sections above.
|
*/
