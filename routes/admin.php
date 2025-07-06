<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminDashboardController;

/*
|--------------------------------------------------------------------------
| TokoSaya Admin Routes (Based on Existing Controllers)
|--------------------------------------------------------------------------
|
| These routes use only the admin controllers that actually exist in the
| project. Currently, only AdminDashboardController is documented as existing.
| Additional admin routes should be added as you create more admin controllers.
|
| Prefix: /admin
| Middleware: web, auth, admin
|
*/

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    // ========================================================================
    // ADMIN DASHBOARD
    // ========================================================================

    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard.index');

    // Dashboard data endpoints (add these methods to AdminDashboardController as needed)
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/stats', [AdminDashboardController::class, 'getStats'])->name('stats');
        Route::get('/sales-chart', [AdminDashboardController::class, 'getSalesChart'])->name('sales-chart');
        Route::get('/recent-orders', [AdminDashboardController::class, 'getRecentOrders'])->name('recent-orders');
        Route::get('/top-products', [AdminDashboardController::class, 'getTopProducts'])->name('top-products');
        Route::get('/low-stock', [AdminDashboardController::class, 'getLowStock'])->name('low-stock');
    });

    // ========================================================================
    // PLACEHOLDER ROUTES FOR FUTURE ADMIN CONTROLLERS
    // ========================================================================

    /*
    | Uncomment and modify these routes as you create the corresponding controllers:
    |
    | // Products Management
    | Route::resource('products', AdminProductController::class);
    |
    | // Categories Management
    | Route::resource('categories', AdminCategoryController::class);
    |
    | // Brands Management
    | Route::resource('brands', AdminBrandController::class);
    |
    | // Orders Management
    | Route::resource('orders', AdminOrderController::class);
    |
    | // Users Management
    | Route::resource('users', AdminUserController::class);
    |
    | // Reviews Management
    | Route::resource('reviews', AdminReviewController::class);
    |
    | // Coupons Management
    | Route::resource('coupons', AdminCouponController::class);
    |
    | // Settings Management
    | Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
    | Route::put('/settings', [AdminSettingController::class, 'update'])->name('settings.update');
    |
    */

    // ========================================================================
    // SIMPLE ADMIN ROUTES (Using Views Only)
    // ========================================================================

    // You can create these simple routes that just return views
    // until you create the proper controllers

    Route::get('/products', function () {
        return view('admin.products.index');
    })->name('products.index');

    Route::get('/orders', function () {
        return view('admin.orders.index');
    })->name('orders.index');

    Route::get('/users', function () {
        return view('admin.users.index');
    })->name('users.index');

    Route::get('/categories', function () {
        return view('admin.categories.index');
    })->name('categories.index');

    Route::get('/reviews', function () {
        return view('admin.reviews.index');
    })->name('reviews.index');

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
        // Dashboard live data (add these methods to AdminDashboardController)
        Route::get('/dashboard/live-stats', [AdminDashboardController::class, 'getLiveStats'])->name('dashboard.live-stats');
        Route::get('/dashboard/quick-stats', [AdminDashboardController::class, 'getQuickStats'])->name('dashboard.quick-stats');

        // Quick search (if method exists)
        Route::get('/search', [AdminDashboardController::class, 'quickSearch'])->name('search');
    });

});

/*
|--------------------------------------------------------------------------
| Admin Route Information
|--------------------------------------------------------------------------
|
| Currently using only AdminDashboardController which exists in your project.
|
| To expand admin functionality, create additional controllers such as:
| - AdminProductController
| - AdminOrderController
| - AdminUserController
| - AdminCategoryController
| - AdminReviewController
| - AdminSettingController
|
| Then uncomment and modify the placeholder routes above.
|
| Example of creating a new admin controller:
| php artisan make:controller Admin/AdminProductController --resource
|
*/
