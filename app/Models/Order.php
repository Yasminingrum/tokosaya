<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'payment_status',
        'subtotal_cents',
        'tax_cents',
        'shipping_cents',
        'discount_cents',
        'total_cents',
        'shipping_name',
        'shipping_phone',
        'shipping_address',
        'shipping_city',
        'shipping_state',
        'shipping_postal_code',
        'shipping_country',
        'billing_name',
        'billing_phone',
        'billing_address',
        'billing_city',
        'billing_state',
        'billing_postal_code',
        'billing_country',
        'notes',
        'internal_notes',
        'coupon_code',
        'tracking_number',
        'shipping_method_id',
        'payment_method_id',
        'confirmed_at',
        'shipped_at',
        'delivered_at',
        'cancelled_at',
    ];

    protected $casts = [
        'subtotal_cents' => 'integer',
        'tax_cents' => 'integer',
        'shipping_cents' => 'integer',
        'discount_cents' => 'integer',
        'total_cents' => 'integer',
        'confirmed_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function shippingMethod()
    {
        return $this->belongsTo(ShippingMethod::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class, 'coupon_code', 'code');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeShipped($query)
    {
        return $query->where('status', 'shipped');
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // Price helpers
    public function getSubtotalAttribute()
    {
        return $this->subtotal_cents / 100;
    }

    public function getTaxAttribute()
    {
        return $this->tax_cents / 100;
    }

    public function getShippingAttribute()
    {
        return $this->shipping_cents / 100;
    }

    public function getDiscountAttribute()
    {
        return $this->discount_cents / 100;
    }

    public function getTotalAttribute()
    {
        return $this->total_cents / 100;
    }

    public function getFormattedSubtotalAttribute()
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }

    public function getFormattedTaxAttribute()
    {
        return 'Rp ' . number_format($this->tax, 0, ',', '.');
    }

    public function getFormattedShippingAttribute()
    {
        return 'Rp ' . number_format($this->shipping, 0, ',', '.');
    }

    public function getFormattedDiscountAttribute()
    {
        return 'Rp ' . number_format($this->discount, 0, ',', '.');
    }

    public function getFormattedTotalAttribute()
    {
        return 'Rp ' . number_format($this->total, 0, ',', '.');
    }

    // Status helpers
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isConfirmed()
    {
        return $this->status === 'confirmed';
    }

    public function isProcessing()
    {
        return $this->status === 'processing';
    }

    public function isShipped()
    {
        return $this->status === 'shipped';
    }

    public function isDelivered()
    {
        return $this->status === 'delivered';
    }

    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    public function isRefunded()
    {
        return $this->status === 'refunded';
    }

    public function isPaid()
    {
        return $this->payment_status === 'paid';
    }

    public function isPaymentPending()
    {
        return $this->payment_status === 'pending';
    }

    public function isPaymentFailed()
    {
        return $this->payment_status === 'failed';
    }

    public function canBeCancelled()
    {
        return in_array($this->status, ['pending', 'confirmed']) && !$this->isPaid();
    }

    public function canBeConfirmed()
    {
        return $this->status === 'pending' && $this->isPaid();
    }

    public function canBeShipped()
    {
        return in_array($this->status, ['confirmed', 'processing']) && $this->isPaid();
    }

    public function canBeDelivered()
    {
        return $this->status === 'shipped';
    }

    // Address helpers
    public function getFullShippingAddressAttribute()
    {
        $address = $this->shipping_address;
        $address .= ', ' . $this->shipping_city;
        $address .= ', ' . $this->shipping_state;
        $address .= ' ' . $this->shipping_postal_code;

        if ($this->shipping_country !== 'ID') {
            $address .= ', ' . $this->shipping_country;
        }

        return $address;
    }

    public function getFullBillingAddressAttribute()
    {
        if (!$this->billing_address) {
            return null;
        }

        $address = $this->billing_address;
        $address .= ', ' . $this->billing_city;
        $address .= ', ' . $this->billing_state;
        $address .= ' ' . $this->billing_postal_code;

        if ($this->billing_country !== 'ID') {
            $address .= ', ' . $this->billing_country;
        }

        return $address;
    }

    public function hasSeparateBillingAddress()
    {
        return !empty($this->billing_address);
    }

    // Other helpers
    public function getTotalItemsAttribute()
    {
        return $this->items->sum('quantity');
    }

    public function getTotalWeightGrams()
    {
        return $this->items->sum(function ($item) {
            $weight = $item->variant
                ? $item->variant->final_weight_grams
                : $item->product->weight_grams;

            return $weight * $item->quantity;
        });
    }

    public function hasDigitalItems()
    {
        return $this->items()->whereHas('product', function ($query) {
            $query->where('digital', true);
        })->exists();
    }

    public function hasPhysicalItems()
    {
        return $this->items()->whereHas('product', function ($query) {
            $query->where('digital', false);
        })->exists();
    }

    public function hasDiscount()
    {
        return $this->discount_cents > 0;
    }

    /**
     * Check if this order has any reviews
     *
     * @return bool
     */
    public function hasReview()
    {
        return $this->items()->whereHas('review')->exists();
    }

    /**
     * Get all reviews for this order
     */
    public function reviews()
    {
        return ProductReview::whereIn('order_item_id', $this->items()->pluck('id'));
    }

    /**
     * Check if a specific product in this order has been reviewed
     *
     * @param int $productId
     * @return bool
     */
    public function hasReviewForProduct($productId)
    {
        return $this->items()
            ->where('product_id', $productId)
            ->whereHas('review')
            ->exists();
    }

    /**
     * Get count of reviewed items in this order
     *
     * @return int
     */
    public function getReviewedItemsCount()
    {
        return $this->items()->whereHas('review')->count();
    }

    /**
     * Check if all items in this order have been reviewed
     *
     * @return bool
     */
    public function isFullyReviewed()
    {
        $totalItems = $this->items()->count();
        $reviewedItems = $this->getReviewedItemsCount();

        return $totalItems > 0 && $totalItems === $reviewedItems;
    }

    public function hasShipping()
    {
        return $this->shipping_cents > 0;
    }

    public function hasTax()
    {
        return $this->tax_cents > 0;
    }

    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'pending' => 'badge-warning',
            'confirmed' => 'badge-info',
            'processing' => 'badge-primary',
            'shipped' => 'badge-secondary',
            'delivered' => 'badge-success',
            'cancelled' => 'badge-danger',
            'refunded' => 'badge-dark',
            default => 'badge-light',
        };
    }

    public function getPaymentStatusBadgeClassAttribute()
    {
        return match($this->payment_status) {
            'pending' => 'badge-warning',
            'paid' => 'badge-success',
            'failed' => 'badge-danger',
            'refunded' => 'badge-dark',
            'partial' => 'badge-info',
            default => 'badge-light',
        };
    }

    public function getDaysFromCreatedAttribute()
    {
        return $this->created_at->diffInDays(now());
    }

    public function isExpired($hours = 24)
    {
        return $this->isPending() &&
               $this->isPaymentPending() &&
               $this->created_at->addHours($hours)->isPast();
    }

    // Actions
    public function confirm()
    {
        $this->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);
    }

    public function markAsProcessing()
    {
        $this->update(['status' => 'processing']);
    }

    public function ship($trackingNumber = null)
    {
        $this->update([
            'status' => 'shipped',
            'tracking_number' => $trackingNumber,
            'shipped_at' => now(),
        ]);
    }

    public function deliver()
    {
        $this->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);
    }

    public function cancel($reason = null)
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'internal_notes' => $this->internal_notes . "\nCancelled: " . $reason,
        ]);

        // Restore stock
        foreach ($this->items as $item) {
            if ($item->variant_id) {
                $item->variant->increment('stock_quantity', $item->quantity);
            } else {
                $item->product->increment('stock_quantity', $item->quantity);
            }
        }
    }

    public function markAsPaid()
    {
        $this->update(['payment_status' => 'paid']);

        if ($this->isPending()) {
            $this->confirm();
        }
    }

    // Static methods
    public static function generateOrderNumber($prefix = 'TS')
    {
        $date = now()->format('Ymd');
        $count = static::whereDate('created_at', today())->count() + 1;

        return $prefix . $date . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    public static function createFromCart(ShoppingCart $cart, array $shippingData, array $paymentData = [])
    {
        $subtotal = $cart->total_cents;
        $shipping = $shippingData['shipping_cost_cents'] ?? 0;
        $tax = $shippingData['tax_cents'] ?? 0;
        $discount = $shippingData['discount_cents'] ?? 0;

        $order = static::create([
            'user_id' => $cart->user_id,
            'order_number' => static::generateOrderNumber(),
            'subtotal_cents' => $subtotal,
            'tax_cents' => $tax,
            'shipping_cents' => $shipping,
            'discount_cents' => $discount,
            'total_cents' => $subtotal + $shipping + $tax - $discount,
            'shipping_name' => $shippingData['name'],
            'shipping_phone' => $shippingData['phone'],
            'shipping_address' => $shippingData['address'],
            'shipping_city' => $shippingData['city'],
            'shipping_state' => $shippingData['state'],
            'shipping_postal_code' => $shippingData['postal_code'],
            'shipping_country' => $shippingData['country'] ?? 'ID',
            'billing_name' => $shippingData['billing_name'] ?? null,
            'billing_phone' => $shippingData['billing_phone'] ?? null,
            'billing_address' => $shippingData['billing_address'] ?? null,
            'billing_city' => $shippingData['billing_city'] ?? null,
            'billing_state' => $shippingData['billing_state'] ?? null,
            'billing_postal_code' => $shippingData['billing_postal_code'] ?? null,
            'billing_country' => $shippingData['billing_country'] ?? null,
            'notes' => $shippingData['notes'] ?? null,
            'coupon_code' => $shippingData['coupon_code'] ?? null,
            'shipping_method_id' => $shippingData['shipping_method_id'] ?? null,
            'payment_method_id' => $paymentData['payment_method_id'] ?? null,
        ]);

        // Create order items
        foreach ($cart->items as $cartItem) {
            $order->items()->create([
                'product_id' => $cartItem->product_id,
                'variant_id' => $cartItem->variant_id,
                'product_name' => $cartItem->product->name,
                'product_sku' => $cartItem->variant ? $cartItem->variant->sku : $cartItem->product->sku,
                'variant_name' => $cartItem->variant ? $cartItem->variant->display_name : null,
                'quantity' => $cartItem->quantity,
                'unit_price_cents' => $cartItem->unit_price_cents,
                'total_price_cents' => $cartItem->total_price_cents,
                'cost_price_cents' => $cartItem->product->cost_price_cents,
            ]);

            // Reserve stock
            if ($cartItem->variant_id) {
                $cartItem->variant->increment('reserved_quantity', $cartItem->quantity);
            } else {
                $cartItem->product->increment('reserved_quantity', $cartItem->quantity);
            }
        }

        // Clear cart
        $cart->clear();

        return $order;
    }

    // Events
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = static::generateOrderNumber();
            }
        });
    }
}
