<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'logo',
        'is_active',
        'sort_order',
        'estimated_min_days',
        'estimated_max_days',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'estimated_min_days' => 'integer',
        'estimated_max_days' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function rates()
    {
        return $this->hasMany(ShippingRate::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // Helper methods
    public function getLogoUrlAttribute()
    {
        if ($this->logo) {
            if (\Illuminate\Support\Str::startsWith($this->logo, ['http://', 'https://'])) {
                return $this->logo;
            }
            return asset('storage/' . $this->logo);
        }

        return asset('images/shipping/' . $this->code . '.png');
    }

    public function getEstimatedDeliveryAttribute()
    {
        if ($this->estimated_min_days === $this->estimated_max_days) {
            if ($this->estimated_min_days === 0) {
                return 'Same day';
            }
            return $this->estimated_min_days . ' day' . ($this->estimated_min_days > 1 ? 's' : '');
        }

        if ($this->estimated_min_days === 0) {
            return 'Same day - ' . $this->estimated_max_days . ' days';
        }

        return $this->estimated_min_days . '-' . $this->estimated_max_days . ' days';
    }

    public function isSameDay()
    {
        return $this->estimated_min_days === 0 && $this->estimated_max_days === 0;
    }

    public function isExpress()
    {
        return $this->estimated_max_days <= 2;
    }

    public function isPickup()
    {
        return $this->code === 'pickup';
    }

    public function getRateForZoneAndWeight($zoneId, $weightGrams, $orderTotal = 0)
    {
        return $this->rates()
            ->where('zone_id', $zoneId)
            ->where('is_active', true)
            ->where('min_weight_grams', '<=', $weightGrams)
            ->where('max_weight_grams', '>=', $weightGrams)
            ->where(function ($query) use ($orderTotal) {
                $query->where('min_amount_cents', '<=', $orderTotal)
                      ->where(function ($q) use ($orderTotal) {
                          $q->where('max_amount_cents', '>=', $orderTotal)
                            ->orWhere('max_amount_cents', 0);
                      });
            })
            ->first();
    }
}
