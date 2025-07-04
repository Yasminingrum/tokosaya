<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CouponUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'coupon_id',
        'user_id',
        'order_id',
        'discount_cents',
        'used_at',
    ];

    protected $casts = [
        'discount_cents' => 'integer',
        'used_at' => 'datetime',
    ];

    // Relationships
    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Helper methods
    public function getDiscountAttribute()
    {
        return $this->discount_cents / 100;
    }

    public function getFormattedDiscountAttribute()
    {
        return 'Rp ' . number_format($this->discount, 0, ',', '.');
    }

    // Events
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($usage) {
            $usage->used_at = now();
        });
    }
}
