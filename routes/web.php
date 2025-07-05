<?php
// File: routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ShoppingCartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotificationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/contact', [HomeController::class, 'contactStore'])->name('contact.store');
Route::get('/search', [HomeController::class, 'search'])->name('search');
Route::get('/search/suggestions', [HomeController::class, 'searchSuggestions'])->name('search.suggestions');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])->name('login.authenticate');
    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'store'])->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [AuthController::class, 'changePassword'])->name('profile.password');
});

// Product Routes
Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('/search', [ProductController::class, 'search'])->name('search');
    Route::get('/{product:slug}', [ProductController::class, 'show'])->name('show');

    // Product reviews
    Route::middleware('auth')->group(function () {
        Route::post('/{product}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
        Route::put('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
        Route::post('/reviews/{review}/helpful', [ReviewController::class, 'helpful'])->name('reviews.helpful');
    });
});

// Category Routes
Route::prefix('categories')->name('categories.')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('index');
    Route::get('/{category:slug}', [CategoryController::class, 'show'])->name('show');
    Route::get('/{category:slug}/products', [ProductController::class, 'category'])->name('products');
});

// Brand Routes
Route::prefix('brands')->name('brands.')->group(function () {
    Route::get('/{brand:slug}', [ProductController::class, 'brand'])->name('show');
    Route::get('/{brand:slug}/products', [ProductController::class, 'brand'])->name('products');
});

// Shopping Cart Routes
Route::prefix('cart')->name('cart.')->middleware('cart')->group(function () {
    Route::get('/', [ShoppingCartController::class, 'index'])->name('index');
    Route::post('/add', [ShoppingCartController::class, 'add'])->name('add');
    Route::put('/items/{item}', [ShoppingCartController::class, 'update'])->name('update');
    Route::delete('/items/{item}', [ShoppingCartController::class, 'remove'])->name('remove');
    Route::delete('/clear', [ShoppingCartController::class, 'clear'])->name('clear');

    // AJAX Routes
    Route::prefix('ajax')->name('ajax.')->group(function () {
        Route::post('/add', [ShoppingCartController::class, 'addAjax'])->name('add');
        Route::put('/update', [ShoppingCartController::class, 'updateAjax'])->name('update');
        Route::delete('/remove', [ShoppingCartController::class, 'removeAjax'])->name('remove');
        Route::get('/count', [ShoppingCartController::class, 'count'])->name('count');
        Route::get('/total', [ShoppingCartController::class, 'total'])->name('total');
    });
});

// Checkout Routes
Route::prefix('checkout')->name('checkout.')->middleware(['auth', 'cart.not_empty'])->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
    Route::post('/shipping', [CheckoutController::class, 'shipping'])->name('shipping');
    Route::post('/payment', [CheckoutController::class, 'payment'])->name('payment');
    Route::get('/review', [CheckoutController::class, 'review'])->name('review');
    Route::post('/process', [CheckoutController::class, 'process'])->name('process');

    // AJAX Routes
    Route::post('/shipping/calculate', [CheckoutController::class, 'calculateShipping'])->name('shipping.calculate');
    Route::post('/coupon/validate', [CheckoutController::class, 'validateCoupon'])->name('coupon.validate');
    Route::post('/coupon/apply', [CheckoutController::class, 'applyCoupon'])->name('coupon.apply');
    Route::delete('/coupon', [CheckoutController::class, 'removeCoupon'])->name('coupon.remove');
});

// Checkout Success/Failed (no middleware)
Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/checkout/failed', [CheckoutController::class, 'failed'])->name('checkout.failed');

// Payment Routes
Route::prefix('payments')->name('payments.')->group(function () {
    Route::post('/process', [PaymentController::class, 'process'])->name('process')->middleware('auth');
    Route::post('/callback/{gateway}', [PaymentController::class, 'callback'])->name('callback');
    Route::get('/success', [PaymentController::class, 'success'])->name('success')->middleware('auth');
    Route::get('/failed', [PaymentController::class, 'failed'])->name('failed')->middleware('auth');
    Route::post('/verify', [PaymentController::class, 'verify'])->name('verify')->middleware('auth');
});

// Order Routes
Route::prefix('orders')->name('orders.')->middleware('auth')->group(function () {
    Route::get('/', [OrderController::class, 'index'])->name('index');
    Route::get('/{order}', [OrderController::class, 'show'])->name('show');
    Route::get('/{order}/track', [OrderController::class, 'track'])->name('track');
    Route::put('/{order}/cancel', [OrderController::class, 'cancel'])->name('cancel');
    Route::post('/{order}/reorder', [OrderController::class, 'reorder'])->name('reorder');
});

// Wishlist Routes
Route::prefix('wishlist')->name('wishlist.')->middleware('auth')->group(function () {
    Route::get('/', [WishlistController::class, 'index'])->name('index');
    Route::post('/add/{product}', [WishlistController::class, 'add'])->name('add');
    Route::delete('/remove/{product}', [WishlistController::class, 'remove'])->name('remove');
    Route::delete('/clear', [WishlistController::class, 'clear'])->name('clear');
    Route::post('/move-to-cart/{product}', [WishlistController::class, 'moveToCart'])->name('move-to-cart');

    // AJAX
    Route::post('/toggle/{product}', [WishlistController::class, 'toggle'])->name('toggle');
});

// Profile Routes
Route::prefix('profile')->name('profile.')->middleware('auth')->group(function () {
    Route::get('/', [ProfileController::class, 'index'])->name('index');
    Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
    Route::put('/', [ProfileController::class, 'update'])->name('update');
    Route::get('/addresses', [ProfileController::class, 'addresses'])->name('addresses');
    Route::post('/addresses', [ProfileController::class, 'storeAddress'])->name('addresses.store');
    Route::put('/addresses/{address}', [ProfileController::class, 'updateAddress'])->name('addresses.update');
    Route::delete('/addresses/{address}', [ProfileController::class, 'deleteAddress'])->name('addresses.delete');
    Route::put('/addresses/{address}/default', [ProfileController::class, 'setDefaultAddress'])->name('addresses.default');
});

// Notification Routes
Route::prefix('notifications')->name('notifications.')->middleware('auth')->group(function () {
    Route::get('/', [NotificationController::class, 'index'])->name('index');
    Route::put('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('read');
    Route::put('/read-all', [NotificationController::class, 'markAllAsRead'])->name('read-all');
    Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('destroy');
});

// Compare Routes (if implementing product comparison)
Route::prefix('compare')->name('compare.')->group(function () {
    Route::get('/', [CompareController::class, 'index'])->name('index');
    Route::post('/add/{product}', [CompareController::class, 'add'])->name('add');
    Route::delete('/remove/{product}', [CompareController::class, 'remove'])->name('remove');
    Route::delete('/clear', [CompareController::class, 'clear'])->name('clear');
});

// Newsletter Routes
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
Route::get('/newsletter/unsubscribe/{token}', [NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');

// Sitemap Routes
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap.index');
Route::get('/sitemap-products.xml', [SitemapController::class, 'products'])->name('sitemap.products');
Route::get('/sitemap-categories.xml', [SitemapController::class, 'categories'])->name('sitemap.categories');
Route::get('/sitemap-pages.xml', [SitemapController::class, 'pages'])->name('sitemap.pages');

// Static Pages
Route::get('/terms', [PageController::class, 'terms'])->name('terms');
Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy');
Route::get('/shipping-info', [PageController::class, 'shipping'])->name('shipping');
Route::get('/return-policy', [PageController::class, 'returns'])->name('returns');
Route::get('/faq', [PageController::class, 'faq'])->name('faq');

// Dynamic Pages
Route::get('/pages/{page:slug}', [PageController::class, 'show'])->name('pages.show');
