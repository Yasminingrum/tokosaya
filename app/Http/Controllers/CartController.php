<?php

// ================================================================================================
// 1. FIXED CartController.php - GANTI SELURUH FILE
// ================================================================================================

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
    public function __construct()
    {
        // Simplified middleware - only check auth for specific methods
        $this->middleware('auth')->only(['index', 'add', 'update', 'remove', 'clear']);
    }

    /**
     * Display cart contents - FIXED
     */
    public function index()
    {
        try {
            $cart = $this->getOrCreateCart();
            $cartItems = $cart->items()->with(['product.images', 'variant'])->get();
            $summary = $this->calculateCartSummary($cartItems);
            $recentlyViewed = collect(); // ✅ TAMBAHKAN INI

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'items' => $cartItems,
                    'summary' => $summary
                ]);
            }

            return view('cart.index', compact('cartItems', 'summary', 'recentlyViewed')); // ✅ TAMBAHKAN recentlyViewed

        } catch (\Exception $e) {
            Log::error('Cart index error: ' . $e->getMessage());

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to load cart'
                ], 500);
            }

            return view('cart.index', [
                'cartItems' => collect(),
                'summary' => $this->getEmptyCartSummary(),
                'recentlyViewed' => collect() // ✅ TAMBAHKAN INI
            ])->with('error', 'Failed to load cart');
        }
    }

    /**
     * Add product to cart - SIMPLIFIED
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
            return back()->withErrors($validator->errors());
        }

        try {
            DB::beginTransaction();

            $product = Product::findOrFail($request->product_id);
            $variant = $request->variant_id ? ProductVariant::findOrFail($request->variant_id) : null;

            // Check product status
            if ($product->status !== 'active') {
                throw new \Exception('Product not available');
            }

            // Check stock
            $availableStock = $variant ? $variant->stock_quantity : $product->stock_quantity;
            if ($availableStock < $request->quantity) {
                throw new \Exception('Insufficient stock');
            }

            $cart = $this->getOrCreateCart();

            // Check existing item
            $existingItem = $cart->items()
                ->where('product_id', $product->id)
                ->where('variant_id', $variant?->id)
                ->first();

            $unitPrice = $variant ? $variant->price_cents : $product->price_cents;

            if ($existingItem) {
                // Update quantity
                $newQuantity = $existingItem->quantity + $request->quantity;
                if ($newQuantity > $availableStock) {
                    throw new \Exception('Total quantity exceeds stock');
                }

                $existingItem->update([
                    'quantity' => $newQuantity,
                    'total_price_cents' => $unitPrice * $newQuantity
                ]);
            } else {
                // Create new item
                $cart->items()->create([
                    'product_id' => $product->id,
                    'variant_id' => $variant?->id,
                    'quantity' => $request->quantity,
                    'unit_price_cents' => $unitPrice,
                    'total_price_cents' => $unitPrice * $request->quantity
                ]);
            }

            // Update cart totals
            $this->updateCartTotals($cart);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product added to cart successfully',
                    'cart_count' => $cart->fresh()->item_count,
                    'cart_total' => number_format($cart->fresh()->total_cents / 100, 0, ',', '.')
                ]);
            }

            return back()->with('success', 'Product added to cart successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Add to cart error: ' . $e->getMessage());

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
            return back()->withErrors($validator->errors());
        }

        try {
            DB::beginTransaction();

            $cart = $this->getOrCreateCart();
            $cartItem = $cart->items()->findOrFail($itemId);

            // Check stock
            $availableStock = $cartItem->variant ?
                $cartItem->variant->stock_quantity :
                $cartItem->product->stock_quantity;

            if ($request->quantity > $availableStock) {
                throw new \Exception('Quantity exceeds available stock');
            }

            // Update item
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
                    'message' => 'Cart updated successfully',
                    'item_total' => number_format($cartItem->total_price_cents / 100, 0, ',', '.'),
                    'cart_total' => number_format($cart->fresh()->total_cents / 100, 0, ',', '.')
                ]);
            }

            return back()->with('success', 'Cart updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update cart error: ' . $e->getMessage());

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

            $cartItem->delete();

            // Update cart totals
            $this->updateCartTotals($cart);

            DB::commit();

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Item removed from cart',
                    'cart_count' => $cart->fresh()->item_count,
                    'cart_total' => number_format($cart->fresh()->total_cents / 100, 0, ',', '.')
                ]);
            }

            return back()->with('success', 'Item removed from cart!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Remove cart item error: ' . $e->getMessage());

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to remove item'
                ], 500);
            }

            return back()->with('error', 'Failed to remove item');
        }
    }

    /**
     * Clear entire cart
     */
    public function clear()
    {
        try {
            $cart = $this->getOrCreateCart();
            $cart->items()->delete();

            $cart->update([
                'item_count' => 0,
                'total_cents' => 0
            ]);

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cart cleared successfully'
                ]);
            }

            return back()->with('success', 'Cart cleared successfully!');

        } catch (\Exception $e) {
            Log::error('Clear cart error: ' . $e->getMessage());

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to clear cart'
                ], 500);
            }

            return back()->with('error', 'Failed to clear cart');
        }
    }

    /**
     * Get cart count (AJAX)
     */
    public function count()
    {
        try {
            $cart = $this->getOrCreateCart();

            return response()->json([
                'success' => true,
                'count' => $cart->item_count
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'count' => 0
            ]);
        }
    }

    // ============================================================================
    // HELPER METHODS
    // ============================================================================

    /**
     * Get or create shopping cart - SIMPLIFIED
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
 * Calculate cart summary - FIXED
 */
    private function calculateCartSummary($cartItems)
    {
        $subtotal = $cartItems->sum('total_price_cents');
        $itemCount = $cartItems->sum('quantity');
        $shipping = 0; // Free shipping untuk sekarang
        $total = $subtotal + $shipping;

        return [
            'subtotal' => $subtotal,
            'shipping' => $shipping, // ✅ TAMBAHKAN INI
            'total' => $total,
            'item_count' => $itemCount,
            'formatted' => [
                'subtotal' => 'Rp ' . number_format($subtotal / 100, 0, ',', '.'),
                'shipping' => 'Rp ' . number_format($shipping / 100, 0, ',', '.'), // ✅ TAMBAHKAN INI
                'total' => 'Rp ' . number_format($total / 100, 0, ',', '.')
            ]
        ];
    }

    /**
 * Get empty cart summary - FIXED
 */
    private function getEmptyCartSummary()
    {
        return [
            'subtotal' => 0,
            'shipping' => 0,
            'total' => 0,
            'item_count' => 0,
            'formatted' => [
                'subtotal' => 'Rp 0',
                'shipping' => 'Rp 0',
                'total' => 'Rp 0'
            ]
        ];
    }
}
