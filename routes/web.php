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
use Illuminate\Support\Facades\Route;

/*
| Web Application Routes
|
| These routes handle user-facing and non-admin functionality for the application.
| Admin routes have been moved to admin.php, except for admin-specific AJAX endpoints.
|
|
*/

// Public Routes (Accessible by all users)
Route::group([], function () {
    // Homepage
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // About page
    Route::get('/about', [HomeController::class, 'about'])->name('about');

    // Contact page
    Route::get('/contact', [HomeController::class, 'contact'])->name('contact');

    // Handle contact form submission
    Route::post('/contact', [HomeController::class, 'contactStore'])->name('contact.store');

    // Privacy policy page
    Route::get('/privacy-policy', [HomeController::class, 'privacy'])->name('privacy');

    // Terms of service page
    Route::get('/terms-of-service', [HomeController::class, 'terms'])->name('terms');

    // FAQ page
    Route::get('/faq', [HomeController::class, 'faq'])->name('faq');

    // Sitemap page
    Route::get('/sitemap', [HomeController::class, 'sitemap'])->name('sitemap');

    // Search redirect
    Route::get('/search', [HomeController::class, 'search'])->name('search');

    // Products listing
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');

    // Single product
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

    // Product reviews
    Route::get('/products/{product}/reviews', [ReviewController::class, 'show'])->name('reviews.show');

    // Categories listing
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');

    // Category with products
    Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');

    // Products by brand
    Route::get('/brand/{brand}', [ProductController::class, 'brand'])->name('products.brand');

    // Featured products
    Route::get('/products/featured', [ProductController::class, 'featured'])->name('products.featured');

    // Cart contents
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');

    // Add product to cart
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');

    // Update cart item quantity
    Route::put('/cart/item/{itemId}', [CartController::class, 'update'])->name('cart.update');

    // Remove item from cart
    Route::delete('/cart/item/{itemId}', [CartController::class, 'remove'])->name('cart.remove');

    // Remove multiple items from cart
    Route::post('/cart/remove-multiple', [CartController::class, 'removeSelected'])->name('cart.remove-multiple');

    // Save item for later (TAMBAHKAN INI)
    Route::post('/cart/save-for-later', [CartController::class, 'saveForLater'])->name('cart.save-for-later');

    // Move saved item to cart (TAMBAHKAN INI)
    Route::post('/cart/move-to-cart', [CartController::class, 'moveToCart'])->name('cart.move-to-cart');

    // Remove saved item (TAMBAHKAN INI)
    Route::post('/cart/remove-saved', [CartController::class, 'removeSavedItem'])->name('cart.remove-saved');

    // Get mini cart HTML
    Route::get('/cart/mini', [CartController::class, 'mini'])->name('cart.mini');

    // Clear entire cart
    Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

    // Payment gateway callback
    Route::post('/payment/callback/{gateway}', [PaymentController::class, 'callback'])->name('payment.callback');

    // View public shared wishlist
    Route::get('/wishlist/public/{token}', [WishlistController::class, 'public'])->name('wishlist.public');
});

// AJAX Routes
Route::group(['prefix' => 'api'], function () {
    // Search suggestions
    Route::get('/search-suggestions', [ProductController::class, 'searchSuggestions'])->name('products.search.suggestions');

    // Track visitor
    Route::post('/track-visitor', [HomeController::class, 'trackVisitor'])->name('track.visitor');

    // Quick stats
    Route::get('/quick-stats', [HomeController::class, 'quickStats'])->name('quick.stats');

    // Category tree for select dropdown
    Route::get('/categories/tree', [CategoryController::class, 'getTree'])->name('categories.tree');

    // Get cart count
    Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count');

    // Get cart total
    Route::get('/cart/total', [CartController::class, 'total'])->name('cart.total');

    Route::get('/cart/mini', [CartController::class, 'mini'])->name('cart.mini');

    // Apply coupon to cart
    Route::post('/cart/coupon', [CartController::class, 'applyCoupon'])->name('cart.coupon.apply');

    // Remove applied coupon from cart
    Route::delete('/cart/coupon', [CartController::class, 'removeCoupon'])->name('cart.coupon.remove');

    // Checkout AJAX Routes
    Route::prefix('checkout')->group(function () {
        // Calculate shipping rates
        Route::get('/shipping/calculate', [CheckoutController::class, 'calculateShipping'])->middleware('cart.not_empty')->name('checkout.shipping.calculate');

        // Validate coupon code
        Route::get('/coupon/validate', [CheckoutController::class, 'validateCoupon'])->middleware('cart.not_empty')->name('checkout.coupon.validate');

        // Apply coupon code
        Route::post('/coupon', [CheckoutController::class, 'applyCoupon'])->middleware('cart.not_empty')->name('checkout.coupon.apply');

        // Remove applied coupon
        Route::delete('/coupon', [CheckoutController::class, 'removeCoupon'])->middleware('cart.not_empty')->name('checkout.coupon.remove');
    });

    // Verify payment status
    Route::get('/payment/verify', [PaymentController::class, 'verify'])->middleware('auth')->name('payment.verify');

    // Order AJAX Routes
    Route::prefix('orders')->group(function () {
        // Add note to order
        Route::post('/{order}/note', [OrderController::class, 'addNote'])->middleware('auth:admin')->name('admin.orders.add_note');

        // Bulk update orders
        Route::post('/bulk-update', [OrderController::class, 'bulkUpdate'])->middleware('auth:admin')->name('admin.orders.bulk_update');
    });

    // Wishlist AJAX Routes
    Route::prefix('wishlist')->middleware(['auth', 'user.status'])->group(function () {
        // Add product to wishlist
        Route::post('/add', [WishlistController::class, 'add'])->name('wishlist.add');

        // Remove product from wishlist
        Route::post('/remove/{product}', [WishlistController::class, 'remove'])->name('wishlist.remove');

        // Clear entire wishlist
        Route::post('/clear', [WishlistController::class, 'clear'])->name('wishlist.clear');

        // Toggle product in wishlist
        Route::post('/toggle/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

        // Move product to cart
        Route::post('/move-to-cart/{product}', [WishlistController::class, 'moveToCart'])->name('wishlist.move_to_cart');

        // Check if product is in wishlist
        Route::get('/check/{product}', [WishlistController::class, 'check'])->name('wishlist.check');

        // Get wishlist count
        Route::get('/count', [WishlistController::class, 'count'])->name('wishlist.count');

        // Get wishlist suggestions
        Route::get('/suggestions', [WishlistController::class, 'suggestions'])->name('wishlist.suggestions');

        // Share wishlist
        Route::post('/share', [WishlistController::class, 'share'])->name('wishlist.share');

        // Bulk operations on wishlist
        Route::post('/bulk-action', [WishlistController::class, 'bulkAction'])->name('wishlist.bulk_action');
    });

    // Review AJAX Routes
    Route::prefix('reviews')->group(function () {
        // Mark review as helpful
        Route::post('/{review}/helpful', [ReviewController::class, 'helpful'])->middleware(['auth', 'user.status'])->name('reviews.helpful');

        // Remove helpful mark from review
        Route::post('/{review}/unhelpful', [ReviewController::class, 'unhelpful'])->middleware(['auth', 'user.status'])->name('reviews.unhelpful');

        // Bulk operations on reviews
        Route::post('/bulk-action', [ReviewController::class, 'bulkAction'])->middleware('auth:admin')->name('admin.reviews.bulk_action');
    });
});

// Newsletter Routes
Route::group([], function () {
    // Newsletter subscription
    Route::post('/newsletter/subscribe', [HomeController::class, 'newsletterSubscribe'])->name('newsletter.subscribe');

    // Newsletter unsubscribe
    Route::get('/newsletter/unsubscribe/{token}', [HomeController::class, 'newsletterUnsubscribe'])->name('newsletter.unsubscribe');
});

// Guest Routes (Accessible by non-authenticated users)
Route::middleware('guest')->group(function () {
    // Show login form
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');

    // Handle login
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

    // Show registration form
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');

    // Handle registration
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
});

// Authenticated Routes (Accessible by authenticated users)
Route::middleware(['auth', 'user.status'])->group(function () {
    // Handle logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Show user dashboard
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');

    // Review Routes
    Route::prefix('reviews')->group(function () {
        // Store a new review
        Route::post('/{product}', [ReviewController::class, 'store'])->name('reviews.store');

        // Update an existing review
        Route::post('/{review}/update', [ReviewController::class, 'update'])->name('reviews.update');

        // Get user's reviews
        Route::get('/', [ReviewController::class, 'userReviews'])->name('reviews.user');
    });

    // Wishlist Routes
    Route::prefix('wishlist')->group(function () {
        // Display user's wishlist
        Route::get('/', [WishlistController::class, 'index'])->name('wishlist.index');
    });

    // Order Routes
    Route::prefix('orders')->group(function () {
        // Display customer orders
        Route::get('/', [OrderController::class, 'index'])->name('orders.index');

        // Show single order
        Route::get('/{order}', [OrderController::class, 'show'])->name('orders.show');

        // Create order from cart
        Route::post('/store', [OrderController::class, 'store'])->name('orders.store');

        // Cancel order
        Route::post('/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

        // Reorder (add order items back to cart)
        Route::post('/{order}/reorder', [OrderController::class, 'reorder'])->name('orders.reorder');

        // Track order
        Route::get('/{order}/track', [OrderController::class, 'track'])->name('orders.track');

        // Show order review form
        Route::get('/{order}/review', [OrderController::class, 'review'])->name('orders.review');

        // Store order reviews
        Route::post('/{order}/review', [OrderController::class, 'storeReview'])->name('orders.store_review');
    });

    // Checkout Routes
    Route::prefix('checkout')->group(function () {
        // Checkout page
        Route::get('/', [CheckoutController::class, 'index'])->middleware('cart.not_empty')->name('checkout.index');

        // Handle shipping step
        Route::post('/shipping', [CheckoutController::class, 'shipping'])->middleware('cart.not_empty')->name('checkout.shipping');

        // Handle payment step
        Route::post('/payment', [CheckoutController::class, 'payment'])->middleware('cart.not_empty')->name('checkout.payment');

        // Order review page
        Route::get('/review', [CheckoutController::class, 'review'])->middleware('cart.not_empty')->name('checkout.review');

        // Process order
        Route::post('/process', [CheckoutController::class, 'process'])->middleware('cart.not_empty')->name('checkout.process');

        // Order success page
        Route::get('/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');

        // Order failed page
        Route::get('/failed', [CheckoutController::class, 'failed'])->name('checkout.failed');
    });

    // Payment Routes
    Route::prefix('payment')->group(function () {
        // Process payment for an order
        Route::post('/process', [PaymentController::class, 'process'])->name('payment.process');

        // Payment success page
        Route::get('/success', [PaymentController::class, 'success'])->name('payment.success');

        // Payment failed page
        Route::get('/failed', [PaymentController::class, 'failed'])->name('payment.failed');
    });
});

// Profile Routes (Accessible by authenticated users)
Route::middleware(['auth', 'user.status'])->prefix('profile')->group(function () {
    // Show profile dashboard
    Route::get('/', [ProfileController::class, 'index'])->name('profile.index');

    // Show profile edit form
    Route::get('/edit', [ProfileController::class, 'edit'])->name('profile.edit');

    // Update profile
    Route::put('/', [ProfileController::class, 'update'])->name('profile.update');

    // Upload avatar
    Route::post('/avatar', [ProfileController::class, 'uploadAvatar'])->name('profile.avatar.upload');

    // Change password
    Route::put('/password', [ProfileController::class, 'changePassword'])->name('profile.password.update');

    // Show order history
    Route::get('/orders', [ProfileController::class, 'orders'])->name('profile.orders');

    // Show addresses
    Route::get('/addresses', [ProfileController::class, 'addresses'])->name('profile.addresses');

    // Store new address
    Route::post('/addresses', [ProfileController::class, 'storeAddress'])->name('profile.addresses.store');

    // Update address
    Route::put('/addresses/{addressId}', [ProfileController::class, 'updateAddress'])->name('profile.addresses.update');

    // Delete address
    Route::delete('/addresses/{addressId}', [ProfileController::class, 'deleteAddress'])->name('profile.addresses.delete');

    // Set default address
    Route::put('/addresses/{addressId}/default', [ProfileController::class, 'setDefaultAddress'])->name('profile.addresses.set_default');

    // Show reviews
    Route::get('/reviews', [ProfileController::class, 'reviews'])->name('profile.reviews');

    // Show notifications
    Route::get('/notifications', [ProfileController::class, 'notifications'])->name('profile.notifications');

    // Show loyalty/rewards
    Route::get('/loyalty', [ProfileController::class, 'loyalty'])->name('profile.loyalty');

    // Show downloads
    Route::get('/downloads', [ProfileController::class, 'downloads'])->name('profile.downloads');

    // Export user data
    Route::get('/export-data', [ProfileController::class, 'exportData'])->name('profile.export_data');

    // Delete account
    Route::post('/delete-account', [ProfileController::class, 'deleteAccount'])->name('profile.delete_account');
});

// Custom 404 Route
Route::fallback([HomeController::class, 'notFound'])->name('notfound');

// Health Check Route
Route::get('/health', [HomeController::class, 'health'])->name('health');
