<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ShoppingCart;
use App\Models\CartItem;

/**
 * Middleware to ensure cart is not empty before proceeding to checkout
 *
 * This middleware validates that:
 * - User has items in their cart
 * - Cart items are still available (stock check)
 * - Cart items prices are still valid
 * - Cart session is not expired
 */
class CartNotEmptyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Get cart based on user authentication status
        $cart = $this->getCart($request);

        // Check if cart exists and has items
        if (!$cart || $cart->item_count == 0) {
            return redirect()->route('cart.index')
                ->with('error', 'Keranjang belanja Anda kosong. Silakan tambahkan produk terlebih dahulu.');
        }

        // Check if cart has expired (for guest carts)
        if ($cart->expires_at && $cart->expires_at->isPast()) {
            $this->clearExpiredCart($cart);
            return redirect()->route('cart.index')
                ->with('error', 'Sesi keranjang belanja Anda telah berakhir. Silakan tambahkan produk kembali.');
        }

        // Validate cart items availability and pricing
        $validationResult = $this->validateCartItems($cart);

        if (!$validationResult['valid']) {
            return redirect()->route('cart.index')
                ->with('warning', $validationResult['message'])
                ->with('updated_items', $validationResult['updated_items']);
        }

        // Add cart to request for use in controllers
        $request->merge(['validated_cart' => $cart]);

        return $next($request);
    }

    /**
     * Get cart based on user authentication status
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \App\Models\ShoppingCart|null
     */
    private function getCart(Request $request): ?ShoppingCart
    {
        if (Auth::check()) {
            // For authenticated users, get cart by user_id
            return ShoppingCart::where('user_id', Auth::id())
                ->with(['items.product', 'items.variant'])
                ->first();
        } else {
            // For guest users, get cart by session_id or guest_token
            $sessionId = $request->session()->getId();
            $guestToken = $request->session()->get('guest_cart_token');

            return ShoppingCart::where(function ($query) use ($sessionId, $guestToken) {
                $query->where('session_id', $sessionId);
                if ($guestToken) {
                    $query->orWhere('guest_token', $guestToken);
                }
            })
            ->with(['items.product', 'items.variant'])
            ->first();
        }
    }

    /**
     * Validate cart items for availability, stock, and pricing
     *
     * @param  \App\Models\ShoppingCart  $cart
     * @return array
     */
    private function validateCartItems(ShoppingCart $cart): array
    {
        $updatedItems = [];
        $hasChanges = false;
        $removedItems = [];

        foreach ($cart->items as $item) {
            $product = $item->product;
            $variant = $item->variant;

            // Check if product is still active
            if (!$product || $product->status !== 'active') {
                $item->delete();
                $removedItems[] = $product ? $product->name : 'Produk tidak tersedia';
                $hasChanges = true;
                continue;
            }

            // Check stock availability
            $availableStock = $variant ? $variant->stock_quantity : $product->stock_quantity;

            if ($availableStock <= 0) {
                $item->delete();
                $removedItems[] = $product->name . ' (stok habis)';
                $hasChanges = true;
                continue;
            }

            // Adjust quantity if requested quantity exceeds available stock
            if ($item->quantity > $availableStock) {
                $item->quantity = $availableStock;
                $item->total_price_cents = $item->unit_price_cents * $item->quantity;
                $item->save();

                $updatedItems[] = [
                    'product' => $product->name,
                    'old_quantity' => $item->quantity,
                    'new_quantity' => $availableStock,
                    'reason' => 'Stok terbatas'
                ];
                $hasChanges = true;
            }

            // Check if price has changed
            $currentPrice = $variant ?
                ($product->price_cents + $variant->price_adjustment_cents) :
                $product->price_cents;

            if ($item->unit_price_cents !== $currentPrice) {
                $item->unit_price_cents = $currentPrice;
                $item->total_price_cents = $currentPrice * $item->quantity;
                $item->save();

                $updatedItems[] = [
                    'product' => $product->name,
                    'price_change' => true,
                    'old_price' => $item->unit_price_cents,
                    'new_price' => $currentPrice,
                    'reason' => 'Harga berubah'
                ];
                $hasChanges = true;
            }
        }

        // Update cart totals if there were changes
        if ($hasChanges) {
            $this->updateCartTotals($cart);
        }

        // Check if cart is empty after validation
        $cart->refresh();
        if ($cart->item_count == 0) {
            return [
                'valid' => false,
                'message' => 'Semua produk dalam keranjang tidak tersedia. Keranjang telah dikosongkan.',
                'updated_items' => [],
                'removed_items' => $removedItems
            ];
        }

        if ($hasChanges) {
            $message = 'Beberapa item dalam keranjang telah diperbarui:';

            if (!empty($updatedItems)) {
                $message .= ' Kuantitas atau harga beberapa produk telah disesuaikan.';
            }

            if (!empty($removedItems)) {
                $message .= ' Produk berikut telah dihapus: ' . implode(', ', $removedItems);
            }

            return [
                'valid' => false,
                'message' => $message,
                'updated_items' => $updatedItems,
                'removed_items' => $removedItems
            ];
        }

        return [
            'valid' => true,
            'message' => 'Keranjang valid',
            'updated_items' => [],
            'removed_items' => []
        ];
    }

    /**
     * Update cart totals after item changes
     *
     * @param  \App\Models\ShoppingCart  $cart
     * @return void
     */
    private function updateCartTotals(ShoppingCart $cart): void
    {
        $cart->refresh();

        $totalItems = $cart->items->sum('quantity');
        $totalPrice = $cart->items->sum('total_price_cents');

        $cart->update([
            'item_count' => $totalItems,
            'total_cents' => $totalPrice,
            'updated_at' => now()
        ]);
    }

    /**
     * Clear expired cart and its items
     *
     * @param  \App\Models\ShoppingCart  $cart
     * @return void
     */
    private function clearExpiredCart(ShoppingCart $cart): void
    {
        try {
            // Delete all cart items first
            CartItem::where('cart_id', $cart->id)->delete();

            // Delete the cart
            $cart->delete();
        } catch (\Exception $e) {
            // Log error but don't break the flow
            \Log::error('Failed to clear expired cart: ' . $e->getMessage(), [
                'cart_id' => $cart->id,
                'user_id' => $cart->user_id,
                'session_id' => $cart->session_id
            ]);
        }
    }

    /**
     * Check if cart contains digital products only
     * This can be used for different checkout flows
     *
     * @param  \App\Models\ShoppingCart  $cart
     * @return bool
     */
    private function isDigitalOnly(ShoppingCart $cart): bool
    {
        foreach ($cart->items as $item) {
            if (!$item->product->digital) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check if cart meets minimum order requirements
     *
     * @param  \App\Models\ShoppingCart  $cart
     * @return array
     */
    private function checkMinimumOrder(ShoppingCart $cart): array
    {
        $minimumOrder = config('tokosaya.checkout.minimum_order_cents', 0);

        if ($cart->total_cents < $minimumOrder) {
            return [
                'valid' => false,
                'message' => 'Minimum pembelian adalah ' .
                    \App\Helpers\PriceHelper::format($minimumOrder) .
                    '. Silakan tambahkan produk senilai ' .
                    \App\Helpers\PriceHelper::format($minimumOrder - $cart->total_cents) .
                    ' lagi.'
            ];
        }

        return ['valid' => true];
    }

    /**
     * Check if all cart items can be shipped to user's default address
     *
     * @param  \App\Models\ShoppingCart  $cart
     * @return array
     */
    private function checkShippingAvailability(ShoppingCart $cart): array
    {
        if (!Auth::check()) {
            return ['valid' => true]; // Will be checked later in checkout
        }

        $user = Auth::user();
        $defaultAddress = $user->addresses()->where('is_default', true)->first();

        if (!$defaultAddress) {
            return [
                'valid' => false,
                'message' => 'Silakan tambahkan alamat pengiriman terlebih dahulu.'
            ];
        }

        // Check if any items have shipping restrictions
        foreach ($cart->items as $item) {
            if ($item->product->shipping_restrictions) {
                $restrictions = json_decode($item->product->shipping_restrictions, true);

                if (isset($restrictions['excluded_cities']) &&
                    in_array($defaultAddress->city, $restrictions['excluded_cities'])) {
                    return [
                        'valid' => false,
                        'message' => "Produk {$item->product->name} tidak dapat dikirim ke {$defaultAddress->city}."
                    ];
                }
            }
        }

        return ['valid' => true];
    }

    /**
     * Log cart validation for analytics
     *
     * @param  \App\Models\ShoppingCart  $cart
     * @param  array  $validationResult
     * @return void
     */
    private function logCartValidation(ShoppingCart $cart, array $validationResult): void
    {
        if (!$validationResult['valid']) {
            \Log::channel('business')->info('Cart validation failed', [
                'cart_id' => $cart->id,
                'user_id' => $cart->user_id,
                'session_id' => $cart->session_id,
                'item_count' => $cart->item_count,
                'total_cents' => $cart->total_cents,
                'validation_result' => $validationResult,
                'user_agent' => request()->userAgent(),
                'ip_address' => request()->ip()
            ]);
        }
    }
}
