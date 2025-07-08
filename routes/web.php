<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\{
    HomeController,
    AuthController,
    ProductController,
    CartController,
    OrderController,
    CheckoutController,
    PaymentController,
    CategoryController,
    WishlistController,
    ReviewController,
    ProfileController
};
use App\Http\Controllers\Admin\AdminDashboardController;

/*
|--------------------------------------------------------------------------
| TokoSaya Web Routes - Complete & Optimized
|--------------------------------------------------------------------------
|
| Complete route configuration based on existing controllers and views
| with proper middleware and route organization
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

// Static pages with fallback closures
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::get('/faq', [HomeController::class, 'faq'])->name('faq');
Route::get('/privacy', [HomeController::class, 'privacy'])->name('privacy');
Route::get('/terms', [HomeController::class, 'terms'])->name('terms');

// Contact form submission
Route::post('/contact', [HomeController::class, 'contactStore'])->name('contact.store');

// Newsletter subscription
Route::post('/newsletter/subscribe', [HomeController::class, 'newsletterSubscribe'])->name('newsletter.subscribe');
Route::get('/newsletter/unsubscribe/{token}', [HomeController::class, 'newsletterUnsubscribe'])->name('newsletter.unsubscribe');

// ============================================================================
// SEARCH ROUTES
// ============================================================================

Route::prefix('search')->name('search.')->group(function () {
    Route::get('/', [ProductController::class, 'search'])->name('index');
    Route::post('/', [ProductController::class, 'search'])->name('perform');
    Route::get('/suggestions', [HomeController::class, 'searchSuggestions'])->name('suggestions');
});

// Alternative route untuk kompatibilitas
Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');

// Global search redirect
Route::post('/search', [HomeController::class, 'search'])->name('global.search');

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

    // Password Reset
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

// Logout (authenticated users only)
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Dashboard redirect
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('profile.index');
    })->name('dashboard');
});

// ============================================================================
// PRODUCT CATALOG ROUTES
// ============================================================================

Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('/{product:slug}', [ProductController::class, 'show'])->name('show');

    // AJAX routes
    Route::get('/ajax/list', [ProductController::class, 'index'])->name('ajax.list');
    Route::get('/featured', [ProductController::class, 'featured'])->name('featured');
    Route::get('/trending', [HomeController::class, 'trending'])->name('trending');
});

// Simple product listing (fallback)
Route::get('/products-simple', function() {
    $products = \App\Models\Product::where('status', 'active')
                                   ->with(['category:id,name,slug', 'brand:id,name,slug'])
                                   ->paginate(12);
    $categories = \App\Models\Category::where('is_active', true)->get();
    $brands = \App\Models\Brand::where('is_active', true)->get();
    return view('products.simple', compact('products', 'categories', 'brands'));
})->name('products.simple');

// ============================================================================
// CATEGORY ROUTES
// ============================================================================

Route::prefix('categories')->name('categories.')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('index');
    Route::get('/{category:slug}', [CategoryController::class, 'show'])->name('show');
});

// ============================================================================
// BRAND ROUTES
// ============================================================================

Route::prefix('brands')->name('brands.')->group(function () {
    Route::get('/', function () {
        $brands = \App\Models\Brand::where('is_active', true)->withCount('products')->get();
        return view('brands.index', compact('brands'));
    })->name('index');

    Route::get('/{brand:slug}', function (\App\Models\Brand $brand) {
        $products = \App\Models\Product::where('brand_id', $brand->id)
                                      ->where('status', 'active')
                                      ->with(['category', 'images'])
                                      ->paginate(24);
        return view('brands.show', compact('brand', 'products'));
    })->name('show');
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
    Route::get('/mini', [CartController::class, 'getMini'])->name('mini');
    Route::get('/data', function() {
        // Return cart data for AJAX
        return response()->json([
            'count' => session('cart_count', 0),
            'total' => session('cart_total', 0)
        ]);
    })->name('data');

    // Coupon operations
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
    Route::post('/toggle', [WishlistController::class, 'toggle'])->name('toggle');
    Route::get('/count', [WishlistController::class, 'getCount'])->name('count');
});

// ============================================================================
// CHECKOUT ROUTES
// ============================================================================

Route::middleware(['auth'])->prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
    Route::get('/shipping', [CheckoutController::class, 'shipping'])->name('shipping');
    Route::post('/shipping', [CheckoutController::class, 'storeShipping'])->name('shipping.store');
    Route::get('/payment', [CheckoutController::class, 'payment'])->name('payment');
    Route::post('/payment', [CheckoutController::class, 'storePayment'])->name('payment.store');
    Route::get('/review', [CheckoutController::class, 'review'])->name('review');
    Route::post('/place-order', [CheckoutController::class, 'placeOrder'])->name('place-order');
    Route::get('/success/{order}', [CheckoutController::class, 'success'])->name('success');

    // Shipping calculations
    Route::post('/calculate-shipping', [CheckoutController::class, 'calculateShipping'])->name('calculate-shipping');
});

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

    // Manual payment confirmation
    Route::middleware('auth')->group(function () {
        Route::get('/{payment}/upload-proof', [PaymentController::class, 'showUploadProof'])->name('upload-proof');
        Route::post('/{payment}/upload-proof', [PaymentController::class, 'uploadProof'])->name('upload-proof.store');
    });
});

// ============================================================================
// USER PROFILE ROUTES (Authenticated Users) - COMPLETE
// ============================================================================

Route::middleware('auth')->prefix('profile')->name('profile.')->group(function () {
    // Main profile pages
    Route::get('/', [ProfileController::class, 'index'])->name('index');
    Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
    Route::put('/update', [ProfileController::class, 'update'])->name('update');

    // Avatar management
    Route::post('/upload-avatar', [ProfileController::class, 'uploadAvatar'])->name('upload-avatar');

    // Password management
    Route::post('/change-password', [ProfileController::class, 'changePassword'])->name('changePassword');

    // Order management (profile context)
    Route::get('/orders', [ProfileController::class, 'orders'])->name('orders');

    // Review management
    Route::get('/reviews', [ProfileController::class, 'reviews'])->name('reviews');

    // Notification management
    Route::get('/notifications', [ProfileController::class, 'notifications'])->name('notifications');
    Route::post('/notifications/{notification}/mark-read', function($notificationId) {
        if (!Auth::check()) {
            return response()->json(['error' => 'Authentication required'], 401);
        }

        \App\Models\Notification::where('id', $notificationId)
            ->where('user_id', Auth::id())
            ->update(['is_read' => true, 'read_at' => now()]);
        return response()->json(['success' => true]);
    })->name('notifications.mark-read');

    // Loyalty program
    Route::get('/loyalty', [ProfileController::class, 'loyalty'])->name('loyalty');

    // Downloads
    Route::get('/downloads', [ProfileController::class, 'downloads'])->name('downloads');

    // GDPR compliance routes
    Route::get('/export-data', [ProfileController::class, 'exportData'])->name('export-data');
    Route::post('/delete-account', [ProfileController::class, 'deleteAccount'])->name('delete-account');

    // Address management - COMPLETE
    Route::prefix('addresses')->name('addresses.')->group(function () {
        Route::get('/', [ProfileController::class, 'addresses'])->name('index');
        Route::post('/', [ProfileController::class, 'storeAddress'])->name('store');
        Route::put('/{address}', [ProfileController::class, 'updateAddress'])->name('update');
        Route::delete('/{address}', [ProfileController::class, 'deleteAddress'])->name('destroy');
        Route::post('/{address}/set-default', [ProfileController::class, 'setDefaultAddress'])->name('set-default');
    });

    // Wishlist alias (redirect to main wishlist)
    Route::get('/wishlist', function() {
        return redirect()->route('wishlist.index');
    })->name('wishlist');
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
// ADMIN ROUTES (Basic)
// ============================================================================

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard.index');
});

// ============================================================================
// API ROUTES FOR AJAX CALLS
// ============================================================================

Route::prefix('api')->name('api.')->group(function () {
    // Search suggestions
    Route::get('/search-suggestions', [HomeController::class, 'searchSuggestions'])->name('search-suggestions');

    // Product quick stats (authenticated)
    Route::middleware('auth')->group(function() {
        Route::get('/quick-stats', [HomeController::class, 'quickStats'])->name('quick-stats');

        // Wishlist API
        Route::post('/wishlist/{product}', function($productId) {
            if (!Auth::check()) {
                return response()->json(['error' => 'Authentication required'], 401);
            }

            $user = Auth::user();
            $exists = \App\Models\Wishlist::where('user_id', $user->id)
                                          ->where('product_id', $productId)
                                          ->exists();

            if ($exists) {
                \App\Models\Wishlist::where('user_id', $user->id)
                                    ->where('product_id', $productId)
                                    ->delete();
                return response()->json(['success' => true, 'action' => 'removed', 'message' => 'Removed from wishlist']);
            } else {
                \App\Models\Wishlist::create([
                    'user_id' => $user->id,
                    'product_id' => $productId
                ]);
                return response()->json(['success' => true, 'action' => 'added', 'message' => 'Added to wishlist']);
            }
        })->name('wishlist.toggle');
    });

    // Public APIs
    Route::get('/trending-products', [HomeController::class, 'trending'])->name('trending-products');
    Route::post('/track-visitor', [HomeController::class, 'trackVisitor'])->name('track-visitor');
});

// ============================================================================
// UTILITY ROUTES
// ============================================================================

// Sitemap
Route::get('/sitemap', [HomeController::class, 'sitemap'])->name('sitemap');

// Visitor tracking
Route::post('/track-visit', [HomeController::class, 'trackVisitor'])->name('track-visit');

// Error pages
Route::get('/404', [HomeController::class, 'notFound'])->name('404');

// ============================================================================
// DEVELOPMENT ROUTES (Non-production only)
// ============================================================================

if (app()->environment(['local', 'testing'])) {
    Route::prefix('dev')->name('dev.')->group(function () {
        Route::get('/clear-cache', function () {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('view:clear');
            Artisan::call('route:clear');
            return 'All caches cleared!';
        })->name('clear-cache');

        Route::get('/debug-auth', function() {
            try {
                $dbCheck = DB::connection()->getPdo() ? 'Connected' : 'Failed';
                $columns = DB::select("DESCRIBE users");
                $rolesCount = DB::table('roles')->count();
                $roles = DB::table('roles')->get();
                $sampleUser = DB::table('users')->first();
                $authConfig = config('auth');

                return response()->json([
                    'database' => $dbCheck,
                    'user_columns' => $columns,
                    'roles_count' => $rolesCount,
                    'roles' => $roles,
                    'sample_user' => $sampleUser,
                    'auth_config' => $authConfig
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        })->name('debug-auth');
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

Route::bind('brand', function ($value) {
    return \App\Models\Brand::where('slug', $value)->firstOrFail();
});

Route::bind('address', function ($value) {
    if (!Auth::check()) {
        abort(401, 'Authentication required');
    }
    return \App\Models\CustomerAddress::where('id', $value)
                                      ->where('user_id', Auth::id())
                                      ->firstOrFail();
});

Route::bind('order', function ($value) {
    if (!Auth::check()) {
        abort(401, 'Authentication required');
    }
    return \App\Models\Order::where('id', $value)
                            ->where('user_id', Auth::id())
                            ->firstOrFail();
});

// ============================================================================
// FALLBACK ROUTE
// ============================================================================

Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
