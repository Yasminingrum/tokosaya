<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Admin\AdminDashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Laravel 12 Compatible
|--------------------------------------------------------------------------
|
| Struktur routes yang disesuaikan dengan Laravel 12
| Menggunakan middleware built-in dan custom middleware yang sederhana
|
*/

// ========================================================================
// PUBLIC ROUTES (Accessible by everyone)
// ========================================================================

// Homepage & Static Pages
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/contact', [HomeController::class, 'contactStore'])->name('contact.store');
Route::get('/privacy-policy', [HomeController::class, 'privacy'])->name('privacy');
Route::get('/terms-of-service', [HomeController::class, 'terms'])->name('terms');
Route::get('/faq', [HomeController::class, 'faq'])->name('faq');
Route::get('/sitemap', [HomeController::class, 'sitemap'])->name('sitemap');

// Product Catalog (Public)
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
Route::get('/products/{product}/reviews', [ReviewController::class, 'show'])->name('reviews.show');
Route::get('/search', [HomeController::class, 'search'])->name('search');

// Categories (Public)
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');

// Brands (Public)
Route::get('/brands', [ProductController::class, 'brandIndex'])->name('products.brand');
Route::get('/brands/{brand}', [ProductController::class, 'brand'])->name('brands.show');

// Featured Products
Route::get('/products/featured', [ProductController::class, 'featured'])->name('products.featured');

// Cart Routes (Public - Guest can access)
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::put('/item/{item}', [CartController::class, 'update'])->name('update');
    Route::delete('/item/{item}', [CartController::class, 'remove'])->name('remove');
    Route::post('/clear', [CartController::class, 'clear'])->name('clear');
    Route::get('/count', [CartController::class, 'count'])->name('count');
});

// Checkout Routes
Route::prefix('checkout')->middleware('auth')->name('checkout.')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
    Route::post('/process', [CheckoutController::class, 'process'])->name('process');
    Route::get('/success/{order}', [CheckoutController::class, 'success'])->name('success');
});

// Newsletter Routes
Route::post('/newsletter/subscribe', [HomeController::class, 'newsletterSubscribe'])->name('newsletter.subscribe');
Route::get('/newsletter/unsubscribe/{token}', [HomeController::class, 'newsletterUnsubscribe'])->name('newsletter.unsubscribe');

// Payment Callback (Public)
Route::post('/payment/callback/{gateway}', [PaymentController::class, 'callback'])->name('payment.callback');

// Health Check
Route::get('/health', [HomeController::class, 'health'])->name('health');

// ========================================================================
// API ROUTES (AJAX endpoints)
// ========================================================================

Route::prefix('api')->group(function () {
    // Public API endpoints
    Route::get('/search-suggestions', [ProductController::class, 'searchSuggestions'])->name('products.search.suggestions');
    Route::post('/track-visitor', [HomeController::class, 'trackVisitor'])->name('track.visitor');
    Route::post('/products/track-view', [ProductController::class, 'trackView'])->name('products.track-view');
    Route::get('/quick-stats', [HomeController::class, 'quickStats'])->name('quick.stats');
    Route::get('/categories/tree', [CategoryController::class, 'getTree'])->name('categories.tree');

    // Cart API (Public)
    Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count');
    Route::get('/cart/total', [CartController::class, 'total'])->name('cart.total');
    Route::get('/cart/mini', [CartController::class, 'mini'])->name('cart.mini');
    Route::post('/cart/coupon', [CartController::class, 'applyCoupon'])->name('cart.coupon.apply');
    Route::delete('/cart/coupon', [CartController::class, 'removeCoupon'])->name('cart.coupon.remove');
});

// ========================================================================
// GUEST ROUTES (Only for non-authenticated users)
// ========================================================================

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

    // Password Reset
    Route::get('/password/reset', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/password/email', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/password/reset/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/password/reset', [AuthController::class, 'resetPassword'])->name('password.update');
});

// ========================================================================
// AUTHENTICATED ROUTES (All logged-in users)
// ========================================================================

Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');

    // Reviews (Authenticated users only)
    Route::prefix('reviews')->group(function () {
        Route::post('/store', [ReviewController::class, 'store'])->name('reviews.store');
        Route::post('/{review}/update', [ReviewController::class, 'update'])->name('reviews.update');
        Route::get('/', [ReviewController::class, 'userReviews'])->name('reviews.user');
        Route::post('/{review}/helpful', [ReviewController::class, 'helpful'])->name('reviews.helpful');
        Route::post('/{review}/unhelpful', [ReviewController::class, 'unhelpful'])->name('reviews.unhelpful');
    });

    // wishlist routes
    Route::prefix('wishlist')->middleware('auth')->group(function () {
        Route::get('/', [WishlistController::class, 'index'])->name('wishlist.index');
        Route::post('/add', [WishlistController::class, 'add'])->name('wishlist.add');
        Route::post('/remove/{product}', [WishlistController::class, 'remove'])->name('wishlist.remove');
        Route::post('/clear', [WishlistController::class, 'clear'])->name('wishlist.clear');

        Route::post('/toggle/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

        Route::post('/move-to-cart/{product}', [WishlistController::class, 'moveToCart'])->name('wishlist.move_to_cart');

        Route::get('/check/{product}', [WishlistController::class, 'check'])->name('wishlist.check');

        Route::get('/count', [WishlistController::class, 'count'])->name('wishlist.count');
        Route::post('/share', [WishlistController::class, 'share'])->name('wishlist.share');
    });

    Route::post('wishlist/toggle/{product}', [WishlistController::class, 'toggle'])
    ->middleware('auth')
    ->name('wishlist.toggle');

    // Orders (Authenticated users only)
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::post('/store', [OrderController::class, 'store'])->name('orders.store');
        Route::post('/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
        Route::post('/{order}/reorder', [OrderController::class, 'reorder'])->name('orders.reorder');
        Route::get('/{order}/track', [OrderController::class, 'track'])->name('orders.track');
        Route::get('/{order}/review', [OrderController::class, 'review'])->name('orders.review');
        Route::post('/{order}/review', [OrderController::class, 'storeReview'])->name('orders.store_review');
    });

    // Checkout (Authenticated users only)
    Route::prefix('checkout')->group(function () {
        Route::get('/', [CheckoutController::class, 'index'])->name('checkout.index');
        Route::post('/shipping', [CheckoutController::class, 'shipping'])->name('checkout.shipping');
        Route::post('/payment', [CheckoutController::class, 'payment'])->name('checkout.payment');
        Route::get('/review', [CheckoutController::class, 'review'])->name('checkout.review');
        Route::post('/process', [CheckoutController::class, 'process'])->name('checkout.process');
        Route::get('/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');
        Route::get('/failed', [CheckoutController::class, 'failed'])->name('checkout.failed');

        // Checkout API
        Route::get('/shipping/calculate', [CheckoutController::class, 'calculateShipping'])->name('checkout.shipping.calculate');
        Route::post('/coupon', [CheckoutController::class, 'applyCoupon'])->name('checkout.coupon.apply');
        Route::delete('/coupon', [CheckoutController::class, 'removeCoupon'])->name('checkout.coupon.remove');
    });

    // Payment (Authenticated users only)
    Route::prefix('payment')->group(function () {
        Route::post('/process', [PaymentController::class, 'process'])->name('payment.process');
        Route::get('/success', [PaymentController::class, 'success'])->name('payment.success');
        Route::get('/failed', [PaymentController::class, 'failed'])->name('payment.failed');
        Route::get('/verify', [PaymentController::class, 'verify'])->name('payment.verify');
    });

    // Profile Routes (Authenticated users only)
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('profile.index');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/', [ProfileController::class, 'update'])->name('profile.update');
        Route::post('/avatar', [ProfileController::class, 'uploadAvatar'])->name('profile.avatar.upload');
        Route::put('/password', [ProfileController::class, 'changePassword'])->name('profile.password.update');
        Route::get('/orders', [ProfileController::class, 'orders'])->name('profile.orders');
        Route::get('/addresses', [ProfileController::class, 'addresses'])->name('profile.addresses');
        Route::post('/addresses', [ProfileController::class, 'storeAddress'])->name('profile.addresses.store');
        Route::put('/addresses/{addressId}', [ProfileController::class, 'updateAddress'])->name('profile.addresses.update');
        Route::delete('/addresses/{addressId}', [ProfileController::class, 'deleteAddress'])->name('profile.addresses.delete');
        Route::put('/addresses/{addressId}/default', [ProfileController::class, 'setDefaultAddress'])->name('profile.addresses.set_default');
        Route::get('/reviews', [ProfileController::class, 'reviews'])->name('profile.reviews');
        Route::get('/notifications', [ProfileController::class, 'notifications'])->name('profile.notifications');
        Route::get('/loyalty', [ProfileController::class, 'loyalty'])->name('profile.loyalty');
        Route::get('/downloads', [ProfileController::class, 'downloads'])->name('profile.downloads');
        Route::get('/export-data', [ProfileController::class, 'exportData'])->name('profile.export_data');
        Route::post('/delete-account', [ProfileController::class, 'deleteAccount'])->name('profile.delete_account');
    });
});

// ========================================================================
// ADMIN ROUTES (Admin and Super Admin only)
// ========================================================================

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    // Admin Dashboard
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard.index');

    // Dashboard API
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/stats', [AdminDashboardController::class, 'getStats'])->name('stats');
        Route::get('/sales-chart', [AdminDashboardController::class, 'getSalesChart'])->name('sales-chart');
        Route::get('/recent-orders', [AdminDashboardController::class, 'getRecentOrders'])->name('recent-orders');
        Route::get('/top-products', [AdminDashboardController::class, 'getTopProducts'])->name('top-products');
        Route::get('/low-stock', [AdminDashboardController::class, 'getLowStock'])->name('low-stock');
        Route::get('/live-stats', [AdminDashboardController::class, 'getLiveStats'])->name('live-stats');
        Route::get('/quick-stats', [AdminDashboardController::class, 'getQuickStats'])->name('quick-stats');
    });

    // Products Management
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

    // Categories Management
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

    // Orders Management
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'adminIndex'])->name('index');
        Route::get('/{order}', [OrderController::class, 'adminShow'])->name('show');
        Route::post('/{order}/status', [OrderController::class, 'updateStatus'])->name('status.update');
        Route::get('/{order}/invoice', [OrderController::class, 'printInvoice'])->name('print_invoice');
        Route::post('/{order}/note', [OrderController::class, 'addNote'])->name('add_note');
        Route::post('/bulk-update', [OrderController::class, 'bulkUpdate'])->name('bulk_update');
    });

    // Payments Management
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [PaymentController::class, 'adminIndex'])->name('index');
        Route::get('/{payment}', [PaymentController::class, 'adminShow'])->name('show');
        Route::post('/{payment}/approve', [PaymentController::class, 'approve'])->name('approve');
        Route::post('/{payment}/reject', [PaymentController::class, 'reject'])->name('reject');
    });

    // Reviews Management
    Route::prefix('reviews')->name('reviews.')->group(function () {
        Route::get('/', [ReviewController::class, 'adminIndex'])->name('index');
        Route::get('/{review}', [ReviewController::class, 'adminShow'])->name('show');
        Route::post('/{review}/approve', [ReviewController::class, 'approve'])->name('approve');
        Route::post('/{review}/reject', [ReviewController::class, 'reject'])->name('reject');
        Route::delete('/{review}', [ReviewController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-action', [ReviewController::class, 'bulkAction'])->name('bulk_action');
    });

    // Simple Admin Views
    Route::get('/users', function () {
        return view('admin.users.index');
    })->name('users.index');

    Route::get('/analytics', function () {
        return view('admin.analytics');
    })->name('analytics');

    Route::get('/settings', function () {
        return view('admin.settings');
    })->name('settings');

    // Admin Search
    Route::get('/search', [AdminDashboardController::class, 'quickSearch'])->name('search');
});

// ========================================================================
// SUPER ADMIN ROUTES (Super Admin only)
// ========================================================================

Route::middleware(['auth', 'role:super_admin'])->prefix('super-admin')->name('super-admin.')->group(function () {    Route::get('/', function () {
        return view('admin.super-admin.index');
    })->name('index');

    Route::get('/users', function () {
        return view('admin.super-admin.users');
    })->name('users');

    Route::get('/system', function () {
        return view('admin.super-admin.system');
    })->name('system');

    Route::get('/logs', function () {
        return view('admin.super-admin.logs');
    })->name('logs');
});

// ========================================================================
// FALLBACK ROUTE
// ========================================================================

Route::fallback([HomeController::class, 'notFound'])->name('notfound');
