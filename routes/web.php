<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    HomeController,
    AuthController,
    ProductController,
    CartController,
    OrderController,
    AdminDashboardController,
    CheckoutController,
    PaymentController,
    CategoryController,
    WishlistController,
    ReviewController,
    ProfileController
};

/*
|--------------------------------------------------------------------------
| TokoSaya Web Routes (Based on Existing Controllers)
|--------------------------------------------------------------------------
|
| These routes use only the controllers that actually exist in the project
| as documented in the README file structure.
|
*/

// ============================================================================
// HEALTH CHECK & SYSTEM STATUS
// ============================================================================

Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'timestamp' => now()->toISOString(),
        'version' => config('app.version', '1.0.0')
    ]);
})->name('health.check');

// ============================================================================
// HOMEPAGE & STATIC PAGES
// ============================================================================

Route::get('/', [HomeController::class, 'index'])->name('home');

// Static pages (you can create a simple controller or use closures)
Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');

Route::get('/faq', function () {
    return view('faq');
})->name('faq');

// ============================================================================
// AUTHENTICATION ROUTES
// ============================================================================

Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');

    // Registration
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.attempt');

    // Password Reset (if methods exist in AuthController)
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

// Logout (authenticated users only)
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ============================================================================
// PRODUCT CATALOG ROUTES
// ============================================================================

Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('/{product:slug}', [ProductController::class, 'show'])->name('show');

    // Additional product routes (if methods exist)
    Route::get('/featured', [ProductController::class, 'featured'])->name('featured');
    Route::get('/search', [ProductController::class, 'search'])->name('search');
});

// ============================================================================
// CATEGORY ROUTES
// ============================================================================

Route::prefix('categories')->name('categories.')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('index');
    Route::get('/{category:slug}', [CategoryController::class, 'show'])->name('show');
});

// ============================================================================
// SHOPPING CART ROUTES
// ============================================================================

Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::put('/update/{item}', [CartController::class, 'update'])->name('update');
    Route::delete('/remove/{item}', [CartController::class, 'remove'])->name('remove');
    Route::delete('/clear', [CartController::class, 'clear'])->name('clear');

    // Cart utilities
    Route::get('/count', [CartController::class, 'getCount'])->name('count');
    Route::get('/total', [CartController::class, 'getTotal'])->name('total');

    // Coupon operations (if methods exist)
    Route::post('/apply-coupon', [CartController::class, 'applyCoupon'])->name('apply-coupon');
    Route::delete('/remove-coupon', [CartController::class, 'removeCoupon'])->name('remove-coupon');
});

// ============================================================================
// WISHLIST ROUTES (Authenticated Users)
// ============================================================================

Route::middleware('auth')->prefix('wishlist')->name('wishlist.')->group(function () {
    Route::get('/', [WishlistController::class, 'index'])->name('index');
    Route::post('/add/{product}', [WishlistController::class, 'add'])->name('add');
    Route::delete('/remove/{product}', [WishlistController::class, 'remove'])->name('remove');
    Route::delete('/clear', [WishlistController::class, 'clear'])->name('clear');
    Route::post('/move-to-cart/{product}', [WishlistController::class, 'moveToCart'])->name('move-to-cart');
});

// ============================================================================
// CHECKOUT ROUTES
// ============================================================================

Route::middleware(['cart.not.empty'])->prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('index');

    // Authenticated checkout steps
    Route::middleware('auth')->group(function () {
        Route::get('/shipping', [CheckoutController::class, 'shipping'])->name('shipping');
        Route::post('/shipping', [CheckoutController::class, 'storeShipping'])->name('shipping.store');
        Route::get('/payment', [CheckoutController::class, 'payment'])->name('payment');
        Route::post('/payment', [CheckoutController::class, 'storePayment'])->name('payment.store');
        Route::get('/review', [CheckoutController::class, 'review'])->name('review');
        Route::post('/place-order', [CheckoutController::class, 'placeOrder'])->name('place-order');
    });

    // Shipping calculations
    Route::post('/calculate-shipping', [CheckoutController::class, 'calculateShipping'])->name('calculate-shipping');
});

// Checkout success
Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])
    ->middleware('auth')
    ->name('checkout.success');

// ============================================================================
// ORDER MANAGEMENT ROUTES (Authenticated Users)
// ============================================================================

Route::middleware('auth')->prefix('orders')->name('orders.')->group(function () {
    Route::get('/', [OrderController::class, 'index'])->name('index');
    Route::get('/{order}', [OrderController::class, 'show'])->name('show');
    Route::get('/{order}/invoice', [OrderController::class, 'invoice'])->name('invoice');
    Route::get('/{order}/tracking', [OrderController::class, 'tracking'])->name('tracking');

    // Order actions
    Route::post('/{order}/cancel', [OrderController::class, 'cancel'])->name('cancel');
    Route::post('/{order}/reorder', [OrderController::class, 'reorder'])->name('reorder');
});

// ============================================================================
// PAYMENT ROUTES
// ============================================================================

Route::prefix('payment')->name('payment.')->group(function () {
    Route::post('/process/{order}', [PaymentController::class, 'process'])->name('process');
    Route::get('/return/{order}', [PaymentController::class, 'return'])->name('return');
    Route::post('/notify/{order}', [PaymentController::class, 'notify'])->name('notify');
    Route::get('/cancel/{order}', [PaymentController::class, 'cancel'])->name('cancel');

    // Payment methods
    Route::get('/methods', [PaymentController::class, 'getMethods'])->name('methods');

    // Manual payment confirmation (if method exists)
    Route::middleware('auth')->group(function () {
        Route::get('/{payment}/upload-proof', [PaymentController::class, 'showUploadProof'])->name('upload-proof');
        Route::post('/{payment}/upload-proof', [PaymentController::class, 'uploadProof'])->name('upload-proof.store');
    });
});

// ============================================================================
// USER PROFILE ROUTES (Authenticated Users)
// ============================================================================

Route::middleware(['auth', 'verified'])->prefix('profile')->name('profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'index'])->name('index');
    Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
    Route::put('/update', [ProfileController::class, 'update'])->name('update');

    // Password management
    Route::get('/change-password', [ProfileController::class, 'changePassword'])->name('change-password');
    Route::put('/update-password', [ProfileController::class, 'updatePassword'])->name('update-password');

    // Address management
    Route::prefix('addresses')->name('addresses.')->group(function () {
        Route::get('/', [ProfileController::class, 'addresses'])->name('index');
        Route::get('/create', [ProfileController::class, 'createAddress'])->name('create');
        Route::post('/', [ProfileController::class, 'storeAddress'])->name('store');
        Route::get('/{address}/edit', [ProfileController::class, 'editAddress'])->name('edit');
        Route::put('/{address}', [ProfileController::class, 'updateAddress'])->name('update');
        Route::delete('/{address}', [ProfileController::class, 'destroyAddress'])->name('destroy');
        Route::post('/{address}/set-default', [ProfileController::class, 'setDefaultAddress'])->name('set-default');
    });
});

// ============================================================================
// PRODUCT REVIEWS ROUTES
// ============================================================================

Route::prefix('reviews')->name('reviews.')->group(function () {
    // Public review viewing
    Route::get('/product/{product}', [ReviewController::class, 'productReviews'])->name('product');
    Route::get('/{review}', [ReviewController::class, 'show'])->name('show');

    // Authenticated review actions
    Route::middleware('auth')->group(function () {
        Route::post('/product/{product}', [ReviewController::class, 'store'])->name('store');
        Route::put('/{review}', [ReviewController::class, 'update'])->name('update');
        Route::delete('/{review}', [ReviewController::class, 'destroy'])->name('destroy');

        // Review helpfulness
        Route::post('/{review}/helpful', [ReviewController::class, 'markHelpful'])->name('helpful');
    });
});

// ============================================================================
// ADMIN ROUTES (Basic - using existing AdminDashboardController)
// ============================================================================

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard.index');

    // Add more admin routes as you create the corresponding methods
    // in AdminDashboardController or create additional admin controllers
});

// ============================================================================
// ERROR PAGES
// ============================================================================

Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});

// ============================================================================
// DEVELOPMENT ROUTES (Non-production only)
// ============================================================================

if (app()->environment(['local', 'testing'])) {
    Route::prefix('dev')->group(function () {
        Route::get('/clear-cache', function () {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('view:clear');
            return 'Cache cleared!';
        })->name('dev.clear-cache');
    });
}

/*
|--------------------------------------------------------------------------
| Route Model Binding
|--------------------------------------------------------------------------
*/

Route::bind('product', function ($value) {
    return \App\Models\Product::where('slug', $value)->firstOrFail();
});

Route::bind('category', function ($value) {
    return \App\Models\Category::where('slug', $value)->firstOrFail();
});

/*
|--------------------------------------------------------------------------
| Route Information
|--------------------------------------------------------------------------
|
| This file contains only routes that use existing controllers from your
| project structure. Additional routes can be added as you create more
| controllers and methods.
|
| Existing Controllers Used:
| - HomeController
| - AuthController
| - ProductController
| - CartController
| - OrderController
| - AdminDashboardController
| - CheckoutController
| - PaymentController
| - CategoryController
| - WishlistController
| - ReviewController
| - ProfileController
|
*/
