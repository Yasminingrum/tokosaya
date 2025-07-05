<?php
// File: app/Services/OrderService.php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Payment;
use App\Services\CartService;
use App\Services\PaymentService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    protected $cartService;
    protected $paymentService;

    public function __construct(CartService $cartService, PaymentService $paymentService)
    {
        $this->cartService = $cartService;
        $this->paymentService = $paymentService;
    }

    /**
     * Create new order
     */
    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Generate order number
            $orderNumber = $this->generateOrderNumber();

            // Create order
            $order = Order::create([
                'user_id' => $data['user_id'],
                'order_number' => $orderNumber,
                'status' => 'pending',
                'payment_status' => 'pending',

                // Amounts
                'subtotal_cents' => $data['summary']['subtotal_cents'],
                'shipping_cents' => $data['summary']['shipping_cents'],
                'tax_cents' => $data['summary']['tax_cents'],
                'discount_cents' => $data['summary']['discount_cents'],
                'total_cents' => $data['summary']['total_cents'],

                // Shipping info
                'shipping_name' => $data['shipping']['shipping_name'],
                'shipping_phone' => $data['shipping']['shipping_phone'],
                'shipping_address' => $data['shipping']['shipping_address'],
                'shipping_city' => $data['shipping']['shipping_city'],
                'shipping_state' => $data['shipping']['shipping_state'],
                'shipping_postal_code' => $data['shipping']['shipping_postal_code'],
                'shipping_country' => $data['shipping']['shipping_country'],

                // Billing info
                'billing_name' => $data['billing']['billing_name'] ?? $data['shipping']['shipping_name'],
                'billing_phone' => $data['billing']['billing_phone'] ?? $data['shipping']['shipping_phone'],
                'billing_address' => $data['billing']['billing_address'] ?? $data['shipping']['shipping_address'],
                'billing_city' => $data['billing']['billing_city'] ?? $data['shipping']['shipping_city'],
                'billing_state' => $data['billing']['billing_state'] ?? $data['shipping']['shipping_state'],
                'billing_postal_code' => $data['billing']['billing_postal_code'] ?? $data['shipping']['shipping_postal_code'],
                'billing_country' => $data['billing']['billing_country'] ?? $data['shipping']['shipping_country'],

                // Additional data
                'notes' => $data['notes'] ?? null,
                'coupon_code' => $data['coupon']['code'] ?? null,
                'shipping_method_id' => $data['shipping_method_id'],
                'payment_method_id' => $data['payment_method_id']
            ]);

            // Create order items
            foreach ($data['items'] as $cartItem) {
                $product = $cartItem->product;
                $variant = $cartItem->variant;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'variant_id' => $variant ? $variant->id : null,
                    'product_name' => $product->name,
                    'product_sku' => $variant ? $variant->sku : $product->sku,
                    'variant_name' => $variant ? "{$variant->variant_name}: {$variant->variant_value}" : null,
                    'quantity' => $cartItem->quantity,
                    'unit_price_cents' => $cartItem->unit_price_cents,
                    'total_price_cents' => $cartItem->total_price_cents,
                    'cost_price_cents' => $product->cost_price_cents
                ]);

                // Update stock
                if ($product->track_stock) {
                    if ($variant) {
                        $variant->decrement('stock_quantity', $cartItem->quantity);
                    } else {
                        $product->decrement('stock_quantity', $cartItem->quantity);
                    }
                }

                // Update product sales stats
                $product->increment('sale_count', $cartItem->quantity);
                $product->increment('revenue_cents', $cartItem->total_price_cents);
                $product->update(['last_sold_at' => now()]);
            }

            // Record coupon usage
            if (isset($data['coupon']) && $data['coupon']) {
                CouponUsage::create([
                    'coupon_id' => $data['coupon']['id'],
                    'user_id' => $data['user_id'],
                    'order_id' => $order->id,
                    'discount_cents' => $data['summary']['discount_cents']
                ]);

                // Update coupon usage count
                Coupon::where('id', $data['coupon']['id'])->increment('used_count');
            }

            return $order;
        });
    }

    /**
     * Update order status
     */
    public function updateStatus(Order $order, $status, $notes = null)
    {
        $order->update([
            'status' => $status,
            'internal_notes' => $notes ? ($order->internal_notes . "\n" . now() . ": " . $notes) : $order->internal_notes
        ]);

        // Update status timestamps
        switch ($status) {
            case 'confirmed':
                $order->update(['confirmed_at' => now()]);
                break;
            case 'shipped':
                $order->update(['shipped_at' => now()]);
                break;
            case 'delivered':
                $order->update(['delivered_at' => now()]);
                break;
            case 'cancelled':
                $order->update(['cancelled_at' => now()]);
                $this->restoreStock($order);
                break;
        }

        return $order;
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus(Order $order, $status)
    {
        $order->update(['payment_status' => $status]);

        // Auto-confirm order when payment is successful
        if ($status === 'paid' && $order->status === 'pending') {
            $this->updateStatus($order, 'confirmed', 'Auto-confirmed after successful payment');
        }

        return $order;
    }

    /**
     * Validate coupon
     */
    public function validateCoupon(Coupon $coupon, $cart)
    {
        // Check if coupon is active
        if (!$coupon->is_active) {
            return ['valid' => false, 'message' => 'This coupon is not active'];
        }

        // Check dates
        if ($coupon->starts_at && $coupon->starts_at > now()) {
            return ['valid' => false, 'message' => 'This coupon is not yet valid'];
        }

        if ($coupon->expires_at && $coupon->expires_at < now()) {
            return ['valid' => false, 'message' => 'This coupon has expired'];
        }

        // Check usage limits
        if ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
            return ['valid' => false, 'message' => 'This coupon has reached its usage limit'];
        }

        // Check per-customer usage limit
        if ($coupon->usage_limit_per_customer) {
            $userUsage = CouponUsage::where('coupon_id', $coupon->id)
                ->where('user_id', auth()->id())
                ->count();

            if ($userUsage >= $coupon->usage_limit_per_customer) {
                return ['valid' => false, 'message' => 'You have already used this coupon the maximum number of times'];
            }
        }

        // Check minimum order amount
        if ($coupon->minimum_order_cents && $cart->total_cents < $coupon->minimum_order_cents) {
            $minAmount = format_currency($coupon->minimum_order_cents);
            return ['valid' => false, 'message' => "Minimum order amount of {$minAmount} required"];
        }

        // Calculate discount amount
        $discountAmount = $this->calculateCouponDiscount($coupon, $cart->total_cents);

        return [
            'valid' => true,
            'discount_amount' => $discountAmount,
            'message' => 'Coupon is valid'
        ];
    }

    /**
     * Calculate coupon discount
     */
    public function calculateCouponDiscount($coupon, $amount)
    {
        switch ($coupon['type']) {
            case 'fixed':
                $discount = $coupon['value_cents'];
                break;
            case 'percentage':
                $discount = (int) round($amount * ($coupon['value_cents'] / 10000));
                break;
            case 'free_shipping':
                $discount = 0; // Handled separately in shipping calculation
                break;
            default:
                $discount = 0;
        }

        // Apply maximum discount limit
        if (isset($coupon['maximum_discount_cents']) && $coupon['maximum_discount_cents'] > 0) {
            $discount = min($discount, $coupon['maximum_discount_cents']);
        }

        // Don't exceed order amount
        return min($discount, $amount);
    }

    /**
     * Calculate tax
     */
    public function calculateTax($amount, $shippingAddress)
    {
        // Simple tax calculation - can be enhanced with tax service integration
        $taxRate = 0; // Default no tax

        // Example: Indonesia VAT
        if ($shippingAddress['shipping_country'] === 'ID') {
            $taxRate = 0.11; // 11% VAT
        }

        return (int) round($amount * $taxRate);
    }

    /**
     * Restore stock when order is cancelled
     */
    protected function restoreStock(Order $order)
    {
        foreach ($order->items as $item) {
            $product = $item->product;

            if ($product->track_stock) {
                if ($item->variant) {
                    $item->variant->increment('stock_quantity', $item->quantity);
                } else {
                    $product->increment('stock_quantity', $item->quantity);
                }
            }

            // Update product sales stats
            $product->decrement('sale_count', $item->quantity);
            $product->decrement('revenue_cents', $item->total_price_cents);
        }
    }

    /**
     * Generate unique order number
     */
    protected function generateOrderNumber()
    {
        $prefix = config('tokosaya.order_prefix', 'TS');
        $date = now()->format('Ymd');

        do {
            $number = $prefix . $date . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (Order::where('order_number', $number)->exists());

        return $number;
    }
}
