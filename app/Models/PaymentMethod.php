<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'logo',
        'is_active',
        'sort_order',
        'gateway_config',
        'fee_type',
        'fee_amount_cents',
        'min_amount_cents',
        'max_amount_cents',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'gateway_config' => 'array',
        'fee_amount_cents' => 'integer',
        'min_amount_cents' => 'integer',
        'max_amount_cents' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
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

    // Helper methods
    public function getFeeAmountAttribute()
    {
        return $this->fee_amount_cents / 100;
    }

    public function getMinAmountAttribute()
    {
        return $this->min_amount_cents / 100;
    }

    public function getMaxAmountAttribute()
    {
        return $this->max_amount_cents > 0 ? $this->max_amount_cents / 100 : null;
    }

    public function getLogoUrlAttribute()
    {
        if ($this->logo) {
            if (str_starts_with($this->logo, 'http://') || str_starts_with($this->logo, 'https://')) {
                return $this->logo;
            }
            return asset('storage/' . $this->logo);
        }

        return asset('images/payment-methods/' . $this->code . '.png');
    }

    public function calculateFee($amountCents)
    {
        if ($this->fee_type === 'percentage') {
            return ($amountCents * $this->fee_amount_cents) / 10000; // fee_amount_cents is in basis points
        }

        return $this->fee_amount_cents; // fixed fee
    }

    public function getFormattedFeeAttribute()
    {
        if ($this->fee_type === 'percentage') {
            return ($this->fee_amount_cents / 100) . '%';
        }

        return 'Rp ' . number_format($this->fee_amount, 0, ',', '.');
    }

    public function isAvailableForAmount($amountCents)
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->min_amount_cents > 0 && $amountCents < $this->min_amount_cents) {
            return false;
        }

        if ($this->max_amount_cents > 0 && $amountCents > $this->max_amount_cents) {
            return false;
        }

        return true;
    }

    public function hasGatewayConfig()
    {
        return !empty($this->gateway_config);
    }

    public function getGatewayConfig($key = null)
    {
        if ($key) {
            return $this->gateway_config[$key] ?? null;
        }

        return $this->gateway_config;
    }

    public function isManual()
    {
        return in_array($this->code, ['bank_transfer', 'cod']);
    }

    public function isAutomatic()
    {
        return !$this->isManual();
    }

    public function requiresProof()
    {
        return $this->code === 'bank_transfer';
    }

    public function isCashOnDelivery()
    {
        return $this->code === 'cod';
    }

    public function getTotalAmount($orderAmountCents)
    {
        return $orderAmountCents + $this->calculateFee($orderAmountCents);
    }

    public function getFormattedTotalAmount($orderAmountCents)
    {
        $total = $this->getTotalAmount($orderAmountCents) / 100;
        return 'Rp ' . number_format($total, 0, ',', '.');
    }
}
