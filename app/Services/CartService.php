<?php
// File: app/Services/CartService.php

namespace App\Services;

use App\Models\ShoppingCart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class CartService
{
    protected $cart;

    /**
     * Initialize cart for current session/user
     */
    public function initializeCart()
    {
        if (Auth::check()) {
            // Get or create cart for authenticated user
            $this->cart = ShoppingCart::firstOrCreate(
                ['user_id' => Auth::id()],
                [
                    'session_id' => Session::getId(),
                    'expires_at' => now()->addDays(30)
                ]
            );

            // Merge guest cart if exists
            $this->mergeGuestCart();
        } else {
            // Get or create guest cart
            $guestToken = Session::get('guest_cart_token', Str::random(32));
            Session::put('guest_cart_token', $guestToken);

            $this->cart = ShoppingCart::firstOrCreate(
                ['guest_token' => $guestToken],
                [
                    'session_id' => Session::getId(),
                    'expires_at' => now()->addDays(7)
                ]
            );
        }

        return $this->cart;
    }

    /**
     * Get current cart
     */
    public function getCart()
    {
        if (!$this->cart) {
            $this->initializeCart();
        }

        return $this->cart;
    }

    /**
     * Get cart items
     */
    public function getItems()
    {
        $cart = $this->getCart();

        return CartItem::where('cart_id', $cart->id)
            ->with(['product.images', 'variant'])
            ->get();
    }

    /**
     * Add item to cart
     */
    public function addItem(Product $product, $quantity = 1, ProductVariant $variant = null)
    {
        // Validate product availability
        if ($product->status !== 'active') {
            throw new \Exception('Product is not available for purchase');
        }

        // Check stock availability
        $availableStock = $variant ? $variant->stock_quantity : $product->stock_quantity;

        if ($product->track_stock && $availableStock < $quantity) {
            throw new \Exception('Insufficient stock available');
        }

        $cart = $this->getCart();

        // Check if item already exists in cart
        $existingItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->where('variant_id', $variant ? $variant->id : null)
            ->first();

        if ($existingItem) {
            // Update quantity
            $newQuantity = $existingItem->quantity + $quantity;

            if ($product->track_stock && $availableStock < $newQuantity) {
                throw new \Exception('Cannot add more items. Stock limit reached.');
            }

            $existingItem->update([
                'quantity' => $newQuantity,
                'total_price_cents' => $existingItem->unit_price_cents * $newQuantity
            ]);

            $cartItem = $existingItem;
        } else {
            // Calculate price
            $unitPrice = $product->price_cents;
            if ($variant && $variant->price_adjustment_cents) {
                $unitPrice += $variant->price_adjustment_cents;
            }

            // Create new cart item
            $cartItem = CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'variant_id' => $variant ? $variant->id : null,
                'quantity' => $quantity,
                'unit_price_cents' => $unitPrice,
                'total_price_cents' => $unitPrice * $quantity
            ]);
        }

        // Update cart totals
        $this->updateCartTotals();

        return [
            'item' => $cartItem,
            'cart_count' => $this->getItemCount(),
            'cart_total' => $this->getTotal()
        ];
    }

    /**
     * Update item quantity
     */
    public function updateQuantity($itemId, $quantity)
    {
        $cart = $this->getCart();

        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('id', $itemId)
            ->firstOrFail();

        // Validate stock
        $product = $cartItem->product;
        $variant = $cartItem->variant;
        $availableStock = $variant ? $variant->stock_quantity : $product->stock_quantity;

        if ($product->track_stock && $availableStock < $quantity) {
            throw new \Exception('Insufficient stock available');
        }

        // Update item
        $cartItem->update([
            'quantity' => $quantity,
            'total_price_cents' => $cartItem->unit_price_cents * $quantity
        ]);

        // Update cart totals
        $this->updateCartTotals();

        return [
            'item_total' => $cartItem->total_price_cents,
            'cart_total' => $this->getTotal()
        ];
    }

    /**
     * Remove item from cart
     */
    public function removeItem($itemId)
    {
        $cart = $this->getCart();

        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('id', $itemId)
            ->firstOrFail();

        $cartItem->delete();

        // Update cart totals
        $this->updateCartTotals();

        return true;
    }

    /**
     * Clear entire cart
     */
    public function clearCart()
    {
        $cart = $this->getCart();

        CartItem::where('cart_id', $cart->id)->delete();

        $cart->update([
            'item_count' => 0,
            'total_cents' => 0
        ]);

        return true;
    }

    /**
     * Get cart item count
     */
    public function getItemCount()
    {
        $cart = $this->getCart();
        return $cart ? $cart->item_count : 0;
    }

    /**
     * Get cart total
     */
    public function getTotal()
    {
        $cart = $this->getCart();
        return $cart ? $cart->total_cents : 0;
    }

    /**
     * Get cart summary
     */
    public function getSummary()
    {
        $cart = $this->getCart();
        $items = $this->getItems();

        return [
            'item_count' => $cart->item_count,
            'subtotal_cents' => $cart->total_cents,
            'weight_grams' => $items->sum(function($item) {
                $weight = $item->variant ? $item->variant->weight_grams : $item->product->weight_grams;
                return $weight * $item->quantity;
            }),
            'items' => $items
        ];
    }

    /**
     * Update cart totals
     */
    protected function updateCartTotals()
    {
        $cart = $this->getCart();

        $items = CartItem::where('cart_id', $cart->id)->get();

        $cart->update([
            'item_count' => $items->sum('quantity'),
            'total_cents' => $items->sum('total_price_cents')
        ]);
    }

    /**
     * Merge guest cart with user cart when user logs in
     */
    protected function mergeGuestCart()
    {
        $guestToken = Session::get('guest_cart_token');

        if ($guestToken) {
            $guestCart = ShoppingCart::where('guest_token', $guestToken)->first();

            if ($guestCart) {
                $guestItems = CartItem::where('cart_id', $guestCart->id)->get();

                foreach ($guestItems as $guestItem) {
                    try {
                        $this->addItem(
                            $guestItem->product,
                            $guestItem->quantity,
                            $guestItem->variant
                        );
                    } catch (\Exception $e) {
                        // Skip items that can't be merged (out of stock, etc.)
                        continue;
                    }
                }

                // Delete guest cart
                $guestCart->delete();
                Session::forget('guest_cart_token');
            }
        }
    }
}
