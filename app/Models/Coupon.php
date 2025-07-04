<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'value_cents',
        'minimum_order_cents',
        'maximum_discount_cents',
        'usage_limit',
        'usage_limit_per_customer',
        'used_count',
        'is_active',
        'is_public',
        'applicable_to',
        'applicable_ids',
        'starts_at',
        'expires_at',
    ];

    protected $casts = [
        'value_cents' => 'integer',
        'minimum_order_cents' => 'integer',
        'maximum_discount_cents' => 'integer',
        'usage_limit' => 'integer',
        'usage_limit_per_customer' => 'integer',
        'used_count' => 'integer',
        'is_active' => 'boolean',
        'is_public' => 'boolean',
        'applicable_ids' => 'array',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function usage()
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'coupon_code', 'code');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeValid($query)
    {
        return $query->where('is_active', true)
                    ->where(function ($q) {
                        $q->whereNull('starts_at')
                          ->orWhere('starts_at', '<=', now());
                    })
                    ->where(function ($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>=', now());
                    });
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByCode($query, $code)
    {
        return $query->where('code', strtoupper($code));
    }

    // Value helpers
    public function getValueAttribute()
    {
        return $this->value_cents / 100;
    }

    public function getMinimumOrderAttribute()
    {
        return $this->minimum_order_cents / 100;
    }

    public function getMaximumDiscountAttribute()
    {
        return $this->maximum_discount_cents ? $this->maximum_discount_cents / 100 : null;
    }

    public function getFormattedValueAttribute()
    {
        if ($this->type === 'percentage') {
            return $this->value . '%';
        }

        return 'Rp ' . number_format($this->value, 0, ',', '.');
    }

    public function getFormattedMinimumOrderAttribute()
    {
        return 'Rp ' . number_format($this->minimum_order, 0, ',', '.');
    }

    public function getFormattedMaximumDiscountAttribute()
    {
        return $this->maximum_discount
            ? 'Rp ' . number_format($this->maximum_discount, 0, ',', '.')
            : null;
    }

    // Status helpers
    public function isActive()
    {
        return $this->is_active;
    }

    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isNotStarted()
    {
        return $this->starts_at && $this->starts_at->isFuture();
    }

    public function isValid()
    {
        return $this->is_active && !$this->isExpired() && !$this->isNotStarted();
    }

    public function hasUsageLimit()
    {
        return $this->usage_limit > 0;
    }

    public function hasCustomerLimit()
    {
        return $this->usage_limit_per_customer > 0;
    }

    public function isUsageLimitReached()
    {
        return $this->hasUsageLimit() && $this->used_count >= $this->usage_limit;
    }

    public function getRemainingUsageAttribute()
    {
        if (!$this->hasUsageLimit()) {
            return null;
        }

        return max(0, $this->usage_limit - $this->used_count);
    }

    public function getUsagePercentageAttribute()
    {
        if (!$this->hasUsageLimit()) {
            return 0;
        }

        return ($this->used_count / $this->usage_limit) * 100;
    }

    // Applicability helpers
    public function isApplicableToAll()
    {
        return $this->applicable_to === 'all';
    }

    public function isApplicableToCategories()
    {
        return $this->applicable_to === 'category';
    }

    public function isApplicableToProducts()
    {
        return $this->applicable_to === 'product';
    }

    public function isApplicableToUsers()
    {
        return $this->applicable_to === 'user';
    }

    public function canBeUsedBy($userId)
    {
        if (!$this->isValid()) {
            return false;
        }

        if ($this->isUsageLimitReached()) {
            return false;
        }

        // Check customer usage limit
        if ($this->hasCustomerLimit()) {
            $customerUsage = $this->usage()->where('user_id', $userId)->count();
            if ($customerUsage >= $this->usage_limit_per_customer) {
                return false;
            }
        }

        // Check if applicable to specific users
        if ($this->isApplicableToUsers()) {
            return in_array($userId, $this->applicable_ids ?? []);
        }

        return true;
    }

    public function canBeAppliedToCart($cart)
    {
        // Check minimum order amount
        if ($this->minimum_order_cents > 0 && $cart->total_cents < $this->minimum_order_cents) {
            return false;
        }

        // Check if applicable to specific categories or products
        if ($this->isApplicableToCategories() || $this->isApplicableToProducts()) {
            $applicableIds = $this->applicable_ids ?? [];

            foreach ($cart->items as $item) {
                if ($this->isApplicableToCategories() && in_array($item->product->category_id, $applicableIds)) {
                    return true;
                }

                if ($this->isApplicableToProducts() && in_array($item->product_id, $applicableIds)) {
                    return true;
                }
            }

            return false;
        }

        return true;
    }

    public function calculateDiscount($amountCents, $cart = null)
    {
        $discount = 0;

        switch ($this->type) {
            case 'fixed':
                $discount = $this->value_cents;
                break;

            case 'percentage':
                $discount = ($amountCents * $this->value_cents) / 10000; // value_cents is in basis points
                break;

            case 'free_shipping':
                // This should be handled in shipping calculation
                return 0;

            case 'buy_x_get_y':
                // Complex logic for buy X get Y - would need additional implementation
                return 0;
        }

        // Apply maximum discount limit
        if ($this->maximum_discount_cents > 0) {
            $discount = min($discount, $this->maximum_discount_cents);
        }

        // Don't exceed the order amount
        return min($discount, $amountCents);
    }

    public function getFormattedDiscountForAmount($amountCents)
    {
        $discount = $this->calculateDiscount($amountCents) / 100;
        return 'Rp ' . number_format($discount, 0, ',', '.');
    }

    public function getDaysUntilExpiryAttribute()
    {
        if (!$this->expires_at) {
            return null;
        }

        return $this->expires_at->diffInDays(now());
    }

    public function getTimeUntilExpiryAttribute()
    {
        if (!$this->expires_at) {
            return null;
        }

        return $this->expires_at->diffForHumans();
    }

    public function isNearExpiry($days = 7)
    {
        if (!$this->expires_at || $this->isExpired()) {
            return false;
        }

        return $this->expires_at->diffInDays(now()) <= $days;
    }

    // Actions
    public function incrementUsage($userId, $orderId, $discountAmount)
    {
        $this->increment('used_count');

        $this->usage()->create([
            'user_id' => $userId,
            'order_id' => $orderId,
            'discount_cents' => $discountAmount,
        ]);
    }

    public function activate()
    {
        $this->update(['is_active' => true]);
    }

    public function deactivate()
    {
        $this->update(['is_active' => false]);
    }

    // Static methods
    public static function findByCode($code)
    {
        return static::where('code', strtoupper($code))->first();
    }

    public static function findValidByCode($code)
    {
        return static::valid()->where('code', strtoupper($code))->first();
    }

    public static function generateCode($length = 8)
    {
        do {
            $code = strtoupper(str()->random($length));
        } while (static::where('code', $code)->exists());

        return $code;
    }

    // Events
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($coupon) {
            $coupon->code = strtoupper($coupon->code);
        });

        static::updating(function ($coupon) {
            if ($coupon->isDirty('code')) {
                $coupon->code = strtoupper($coupon->code);
            }
        });
    }
}
