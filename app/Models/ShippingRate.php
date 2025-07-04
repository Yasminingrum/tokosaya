<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipping_method_id',
        'zone_id',
        'min_weight_grams',
        'max_weight_grams',
        'min_amount_cents',
        'max_amount_cents',
        'rate_cents',
        'free_shipping_threshold_cents',
        'is_active',
    ];

    protected $casts = [
        'min_weight_grams' => 'integer',
        'max_weight_grams' => 'integer',
        'min_amount_cents' => 'integer',
        'max_amount_cents' => 'integer',
        'rate_cents' => 'integer',
        'free_shipping_threshold_cents' => 'integer',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function shippingMethod()
    {
        return $this->belongsTo(ShippingMethod::class);
    }

    public function zone()
    {
        return $this->belongsTo(ShippingZone::class, 'zone_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForWeight($query, $weightGrams)
    {
        return $query->where('min_weight_grams', '<=', $weightGrams)
                    ->where('max_weight_grams', '>=', $weightGrams);
    }

    public function scopeForAmount($query, $amountCents)
    {
        return $query->where(function ($q) use ($amountCents) {
            $q->where('min_amount_cents', '<=', $amountCents)
              ->where(function ($subQ) use ($amountCents) {
                  $subQ->where('max_amount_cents', '>=', $amountCents)
                       ->orWhere('max_amount_cents', 0);
              });
        });
    }

    public function scopeForMethod($query, $methodId)
    {
        return $query->where('shipping_method_id', $methodId);
    }

    public function scopeForZone($query, $zoneId)
    {
        return $query->where('zone_id', $zoneId);
    }

    public function scopeWithFreeShipping($query)
    {
        return $query->where('free_shipping_threshold_cents', '>', 0);
    }

    // Price helpers
    public function getRateAttribute()
    {
        return $this->rate_cents / 100;
    }

    public function getMinAmountAttribute()
    {
        return $this->min_amount_cents / 100;
    }

    public function getMaxAmountAttribute()
    {
        return $this->max_amount_cents > 0 ? $this->max_amount_cents / 100 : null;
    }

    public function getFreeShippingThresholdAttribute()
    {
        return $this->free_shipping_threshold_cents > 0 ? $this->free_shipping_threshold_cents / 100 : null;
    }

    public function getFormattedRateAttribute()
    {
        return 'Rp ' . number_format($this->rate, 0, ',', '.');
    }

    public function getFormattedMinAmountAttribute()
    {
        return 'Rp ' . number_format($this->min_amount, 0, ',', '.');
    }

    public function getFormattedMaxAmountAttribute()
    {
        return $this->max_amount ? 'Rp ' . number_format($this->max_amount, 0, ',', '.') : 'No limit';
    }

    public function getFormattedFreeShippingThresholdAttribute()
    {
        return $this->free_shipping_threshold
            ? 'Rp ' . number_format($this->free_shipping_threshold, 0, ',', '.')
            : null;
    }

    // Weight helpers
    public function getMinWeightInKgAttribute()
    {
        return $this->min_weight_grams / 1000;
    }

    public function getMaxWeightInKgAttribute()
    {
        return $this->max_weight_grams / 1000;
    }

    public function getWeightRangeAttribute()
    {
        $minKg = $this->min_weight_in_kg;
        $maxKg = $this->max_weight_in_kg;

        if ($minKg == $maxKg) {
            return $minKg . ' kg';
        }

        if ($maxKg >= 65.535) { // max value for SMALLINT UNSIGNED in grams
            return $minKg . ' kg+';
        }

        return $minKg . ' - ' . $maxKg . ' kg';
    }

    // Validation helpers
    public function isValidForWeight($weightGrams)
    {
        return $this->is_active &&
               $weightGrams >= $this->min_weight_grams &&
               $weightGrams <= $this->max_weight_grams;
    }

    public function isValidForAmount($amountCents)
    {
        if (!$this->is_active) {
            return false;
        }

        if ($amountCents < $this->min_amount_cents) {
            return false;
        }

        if ($this->max_amount_cents > 0 && $amountCents > $this->max_amount_cents) {
            return false;
        }

        return true;
    }

    public function isValidForOrder($weightGrams, $amountCents)
    {
        return $this->isValidForWeight($weightGrams) && $this->isValidForAmount($amountCents);
    }

    public function qualifiesForFreeShipping($amountCents)
    {
        return $this->free_shipping_threshold_cents > 0 &&
               $amountCents >= $this->free_shipping_threshold_cents;
    }

    public function calculateShippingCost($amountCents)
    {
        if ($this->qualifiesForFreeShipping($amountCents)) {
            return 0;
        }

        return $this->rate_cents;
    }

    public function getFormattedShippingCost($amountCents)
    {
        $cost = $this->calculateShippingCost($amountCents);

        if ($cost === 0) {
            return 'FREE';
        }

        return 'Rp ' . number_format($cost / 100, 0, ',', '.');
    }

    // Range descriptions
    public function getAmountRangeDescriptionAttribute()
    {
        $min = $this->formatted_min_amount;
        $max = $this->formatted_max_amount;

        if ($this->max_amount_cents === 0) {
            return "Min order: {$min}";
        }

        return "Order range: {$min} - {$max}";
    }

    public function getWeightRangeDescriptionAttribute()
    {
        return "Weight: {$this->weight_range}";
    }

    public function getFullDescriptionAttribute()
    {
        $description = $this->weight_range_description;

        if ($this->min_amount_cents > 0 || $this->max_amount_cents > 0) {
            $description .= ', ' . $this->amount_range_description;
        }

        if ($this->free_shipping_threshold_cents > 0) {
            $description .= ', Free shipping above ' . $this->formatted_free_shipping_threshold;
        }

        return $description;
    }

    // Static methods
    public static function findBestRate($methodId, $zoneId, $weightGrams, $amountCents)
    {
        return static::active()
                    ->forMethod($methodId)
                    ->forZone($zoneId)
                    ->forWeight($weightGrams)
                    ->forAmount($amountCents)
                    ->orderBy('rate_cents')
                    ->first();
    }

    public static function getAvailableRates($methodId, $zoneId, $weightGrams, $amountCents)
    {
        return static::active()
                    ->forMethod($methodId)
                    ->forZone($zoneId)
                    ->forWeight($weightGrams)
                    ->forAmount($amountCents)
                    ->orderBy('rate_cents')
                    ->get();
    }

    public static function calculateShipping($methodId, $zoneId, $weightGrams, $amountCents)
    {
        $rate = static::findBestRate($methodId, $zoneId, $weightGrams, $amountCents);

        if (!$rate) {
            return null;
        }

        return [
            'rate_id' => $rate->id,
            'cost_cents' => $rate->calculateShippingCost($amountCents),
            'cost_formatted' => $rate->getFormattedShippingCost($amountCents),
            'is_free' => $rate->qualifiesForFreeShipping($amountCents),
            'description' => $rate->full_description,
        ];
    }

    // Events
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($shippingRate) {
            // Validate weight range
            if ($shippingRate->min_weight_grams > $shippingRate->max_weight_grams) {
                throw new \Exception('Minimum weight cannot be greater than maximum weight');
            }

            // Validate amount range
            if ($shippingRate->max_amount_cents > 0 &&
                $shippingRate->min_amount_cents > $shippingRate->max_amount_cents) {
                throw new \Exception('Minimum amount cannot be greater than maximum amount');
            }
        });
    }
}
