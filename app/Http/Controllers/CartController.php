<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ShoppingCart;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    /**
     * ✅ FIXED: Constructor with improved role checking
     */
    public function __construct()
    {
        // Apply middleware to check if user can use cart
        $this->middleware(function ($request, $next) {
            // Check if user is logged in
            if (Auth::check()) {
                $user = Auth::user();

                // Check if user can use cart
                if (!$this->userCanUseCart($user)) {
                    if ($request->expectsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Admin tidak dapat menggunakan shopping cart'
                        ], 403);
                    }

                    return redirect()->route('admin.dashboard')
                        ->with('error', 'Admin tidak dapat mengakses shopping cart. Gunakan panel admin untuk mengelola produk.');
                }
            }

            return $next($request);
        });
    }

    /**
     * Check if user can use shopping cart
     */
    private function userCanUseCart($user): bool
    {
        // Jika user tidak memiliki role, anggap sebagai customer (default)
        if (!$user->role) {
            return true;
        }

        // Hanya role 'customer' yang bisa menggunakan cart
        // Admin dan staff tidak boleh menggunakan cart
        return $user->role->name === 'customer';
    }

    /**
     * Display cart contents
     */
    public function index()
    {
        try {
            $cart = $this->getOrCreateCart();
            $cartItems = $cart->items()->with(['product.images', 'variant'])->get();

            $summary = $this->calculateCartSummary($cartItems);

            // Get recently viewed products
            $recentlyViewed = $this->getRecentlyViewedProducts();

            return view('cart.index', compact('cartItems', 'summary', 'recentlyViewed'));

        } catch (\Exception $e) {
            Log::error('Error loading cart: ' . $e->getMessage());
            return view('cart.index', [
                'cartItems' => collect(),
                'summary' => $this->getEmptyCartSummary(),
                'recentlyViewed' => collect()
            ])->with('error', 'Terjadi kesalahan saat memuat keranjang.');
        }
    }

    /**
     * Add product to cart
     */
    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'quantity' => 'required|integer|min:1|max:10'
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->with('errors', $validator->errors());
        }

        try {
            DB::beginTransaction();

            $product = Product::findOrFail($request->product_id);
            $variant = $request->variant_id ? ProductVariant::findOrFail($request->variant_id) : null;

            // Check product status
            if ($product->status !== 'active') {
                throw new \Exception('Product tidak tersedia');
            }

            // Check stock
            $availableStock = $variant ? $variant->stock_quantity : $product->stock_quantity;
            if ($availableStock < $request->quantity) {
                throw new \Exception('Stok tidak mencukupi');
            }

            $cart = $this->getOrCreateCart();

            // Check if item already exists in cart
            $existingItem = $cart->items()
                ->where('product_id', $product->id)
                ->where('variant_id', $variant?->id)
                ->first();

            if ($existingItem) {
                // Update quantity
                $newQuantity = $existingItem->quantity + $request->quantity;

                if ($newQuantity > $availableStock) {
                    throw new \Exception('Total quantity melebihi stok yang tersedia');
                }

                $existingItem->update([
                    'quantity' => $newQuantity,
                    'total_price_cents' => $existingItem->unit_price_cents * $newQuantity
                ]);

                $cartItem = $existingItem;
            } else {
                // Create new cart item
                $unitPrice = $variant ?
                    ($product->price_cents + $variant->price_adjustment_cents) :
                    $product->price_cents;

                $cartItem = $cart->items()->create([
                    'product_id' => $product->id,
                    'variant_id' => $variant?->id,
                    'quantity' => $request->quantity,
                    'unit_price_cents' => $unitPrice,
                    'total_price_cents' => $unitPrice * $request->quantity
                ]);
            }

            // Update cart totals
            $this->updateCartTotals($cart);

            // Log activity
            if (Auth::check()) {
                Log::info('Product added to cart', [
                    'user_id' => Auth::id(),
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $request->quantity,
                    'variant_id' => $variant?->id
                ]);
            }

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product berhasil ditambahkan ke keranjang',
                    'cart_count' => $cart->fresh()->item_count,
                    'cart_total' => number_format($cart->fresh()->total_cents / 100, 0, ',', '.')
                ]);
            }

            return back()->with('success', 'Product berhasil ditambahkan ke keranjang!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error adding to cart: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 400);
            }

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request, $itemId)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1|max:10'
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->with('errors', $validator->errors());
        }

        try {
            DB::beginTransaction();

            $cart = $this->getOrCreateCart();
            $cartItem = $cart->items()->findOrFail($itemId);

            // Check stock availability
            $availableStock = $cartItem->variant ?
                $cartItem->variant->stock_quantity :
                $cartItem->product->stock_quantity;

            if ($request->quantity > $availableStock) {
                throw new \Exception('Quantity melebihi stok yang tersedia');
            }

            // Update cart item
            $cartItem->update([
                'quantity' => $request->quantity,
                'total_price_cents' => $cartItem->unit_price_cents * $request->quantity
            ]);

            // Update cart totals
            $this->updateCartTotals($cart);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Keranjang berhasil diperbarui',
                    'item_total' => number_format($cartItem->total_price_cents / 100, 0, ',', '.'),
                    'cart_total' => number_format($cart->fresh()->total_cents / 100, 0, ',', '.')
                ]);
            }

            return back()->with('success', 'Keranjang berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating cart item: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 400);
            }

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove item from cart
     */
    public function remove($itemId)
    {
        try {
            DB::beginTransaction();

            $cart = $this->getOrCreateCart();
            $cartItem = $cart->items()->findOrFail($itemId);

            // Log before deletion
            if (Auth::check()) {
                Log::info('Product removed from cart', [
                    'user_id' => Auth::id(),
                    'product_id' => $cartItem->product_id,
                    'product_name' => $cartItem->product->name,
                    'quantity' => $cartItem->quantity
                ]);
            }

            $cartItem->delete();

            // Update cart totals
            $this->updateCartTotals($cart);

            DB::commit();

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Item berhasil dihapus dari keranjang',
                    'cart_count' => $cart->fresh()->item_count,
                    'cart_total' => number_format($cart->fresh()->total_cents / 100, 0, ',', '.')
                ]);
            }

            return back()->with('success', 'Item berhasil dihapus dari keranjang!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error removing cart item: ' . $e->getMessage());

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 400);
            }

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Clear entire cart
     */
    public function clear()
    {
        try {
            DB::beginTransaction();

            $cart = $this->getOrCreateCart();

            // Log before clearing
            if (Auth::check()) {
                Log::info('Cart cleared', [
                    'user_id' => Auth::id(),
                    'items_count' => $cart->item_count
                ]);
            }

            $cart->items()->delete();
            $cart->update([
                'item_count' => 0,
                'total_cents' => 0
            ]);

            DB::commit();

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Keranjang berhasil dikosongkan'
                ]);
            }

            return redirect()->route('cart.index')->with('success', 'Keranjang berhasil dikosongkan!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error clearing cart: ' . $e->getMessage());

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 400);
            }

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Get cart count (AJAX)
     */
    public function count()
    {
        try {
            $cart = $this->getOrCreateCart();
            return response()->json(['count' => $cart->item_count]);
        } catch (\Exception $e) {
            Log::error('Error getting cart count: ' . $e->getMessage());
            return response()->json(['count' => 0]);
        }
    }

    /**
     * Get cart total (AJAX)
     */
    public function total()
    {
        try {
            $cart = $this->getOrCreateCart();

            return response()->json([
                'total' => $cart->total_cents,
                'formatted_total' => 'Rp ' . number_format($cart->total_cents / 100, 0, ',', '.')
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting cart total: ' . $e->getMessage());
            return response()->json([
                'total' => 0,
                'formatted_total' => 'Rp 0'
            ]);
        }
    }

    /**
     * Apply coupon to cart
     */
    public function applyCoupon(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'coupon_code' => 'required|string|max:30'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $cart = $this->getOrCreateCart();

            // Find and validate coupon
            $coupon = \App\Models\Coupon::where('code', $request->coupon_code)
                ->where('is_active', true)
                ->where('starts_at', '<=', now())
                ->where('expires_at', '>=', now())
                ->first();

            if (!$coupon) {
                throw new \Exception('Kode kupon tidak valid atau sudah expired');
            }

            // Check usage limits
            if ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
                throw new \Exception('Kupon sudah mencapai batas penggunaan');
            }

            // Check minimum order
            if ($coupon->minimum_order_cents && $cart->total_cents < $coupon->minimum_order_cents) {
                $minOrder = number_format($coupon->minimum_order_cents / 100, 0, ',', '.');
                throw new \Exception("Minimum order Rp {$minOrder} untuk menggunakan kupon ini");
            }

            // Calculate discount
            $discountAmount = $this->calculateCouponDiscount($coupon, $cart->total_cents);

            // Store coupon in session
            session(['applied_coupon' => [
                'code' => $coupon->code,
                'discount_amount' => $discountAmount,
                'type' => $coupon->type
            ]]);

            return response()->json([
                'success' => true,
                'message' => 'Kupon berhasil diterapkan',
                'discount_amount' => $discountAmount,
                'formatted_discount' => 'Rp ' . number_format($discountAmount / 100, 0, ',', '.'),
                'new_total' => $cart->total_cents - $discountAmount,
                'formatted_new_total' => 'Rp ' . number_format(($cart->total_cents - $discountAmount) / 100, 0, ',', '.')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Remove applied coupon
     */
    public function removeCoupon()
    {
        session()->forget('applied_coupon');

        return response()->json([
            'success' => true,
            'message' => 'Kupon berhasil dihapus'
        ]);
    }

    /**
     * Get or create shopping cart
     */
    private function getOrCreateCart()
    {
        if (Auth::check()) {
            $cart = ShoppingCart::where('user_id', Auth::id())->first();

            if (!$cart) {
                $cart = ShoppingCart::create([
                    'user_id' => Auth::id(),
                    'item_count' => 0,
                    'total_cents' => 0
                ]);
            }
        } else {
            $sessionId = session()->getId();
            $cart = ShoppingCart::where('session_id', $sessionId)->first();

            if (!$cart) {
                $cart = ShoppingCart::create([
                    'session_id' => $sessionId,
                    'item_count' => 0,
                    'total_cents' => 0,
                    'expires_at' => now()->addDays(7)
                ]);
            }
        }

        return $cart;
    }

    /**
     * Update cart totals
     */
    private function updateCartTotals($cart)
    {
        $totals = $cart->items()->selectRaw('
            SUM(quantity) as total_items,
            SUM(total_price_cents) as total_price
        ')->first();

        $cart->update([
            'item_count' => $totals->total_items ?? 0,
            'total_cents' => $totals->total_price ?? 0
        ]);
    }

    /**
     * Calculate cart summary
     */
    private function calculateCartSummary($cartItems)
    {
        $subtotal = $cartItems->sum('total_price_cents');
        $itemCount = $cartItems->sum('quantity');

        // Get applied coupon
        $appliedCoupon = session('applied_coupon');
        $discountAmount = $appliedCoupon['discount_amount'] ?? 0;

        // Calculate shipping (simple implementation)
        $shippingCost = $this->calculateShippingCost($subtotal);

        $total = $subtotal - $discountAmount + $shippingCost;

        return [
            'subtotal' => $subtotal,
            'discount' => $discountAmount,
            'shipping' => $shippingCost,
            'total' => $total,
            'item_count' => $itemCount,
            'applied_coupon' => $appliedCoupon,
            'formatted' => [
                'subtotal' => 'Rp ' . number_format($subtotal / 100, 0, ',', '.'),
                'discount' => 'Rp ' . number_format($discountAmount / 100, 0, ',', '.'),
                'shipping' => 'Rp ' . number_format($shippingCost / 100, 0, ',', '.'),
                'total' => 'Rp ' . number_format($total / 100, 0, ',', '.')
            ]
        ];
    }

    /**
     * Calculate coupon discount
     */
    private function calculateCouponDiscount($coupon, $cartTotal)
    {
        $discount = 0;

        switch ($coupon->type) {
            case 'fixed':
                $discount = $coupon->value_cents;
                break;
            case 'percentage':
                $discount = ($cartTotal * $coupon->value_cents) / 10000; // value_cents stores percentage * 100
                break;
            case 'free_shipping':
                $discount = $this->calculateShippingCost($cartTotal);
                break;
        }

        // Apply maximum discount limit
        if ($coupon->maximum_discount_cents && $discount > $coupon->maximum_discount_cents) {
            $discount = $coupon->maximum_discount_cents;
        }

        // Don't exceed cart total
        if ($discount > $cartTotal) {
            $discount = $cartTotal;
        }

        return (int) $discount;
    }

    /**
     * Calculate shipping cost (simple implementation)
     */
    private function calculateShippingCost($cartTotal)
    {
        // Free shipping for orders above 100k
        if ($cartTotal >= 10000000) { // 100k in cents
            return 0;
        }

        // Default shipping cost
        return 1000000; // 10k in cents
    }

    /**
     * Get empty cart summary
     */
    private function getEmptyCartSummary()
    {
        return [
            'subtotal' => 0,
            'discount' => 0,
            'shipping' => 0,
            'total' => 0,
            'item_count' => 0,
            'applied_coupon' => null,
            'formatted' => [
                'subtotal' => 'Rp 0',
                'discount' => 'Rp 0',
                'shipping' => 'Rp 0',
                'total' => 'Rp 0'
            ]
        ];
    }

    /**
     * Get recently viewed products
     */
    private function getRecentlyViewedProducts()
    {
        try {
            $key = 'recently_viewed_' . (Auth::id() ?: session()->getId());
            $productIds = session()->get($key, []);

            if (empty($productIds)) {
                return collect();
            }

            return Product::whereIn('id', array_slice($productIds, 0, 4))
                ->where('status', 'active')
                ->with('images')
                ->get();
        } catch (\Exception $e) {
            Log::error('Error getting recently viewed products: ' . $e->getMessage());
            return collect();
        }
    }
}
