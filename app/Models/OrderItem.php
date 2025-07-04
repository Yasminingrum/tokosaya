<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'variant_id',
        'product_name',
        'product_sku',
        'variant_name',
        'quantity',
        'unit_price_cents',
        'total_price_cents',
        'cost_price_cents',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price_cents' => 'integer',
        'total_price_cents' => 'integer',
        'cost_price_cents' => 'integer',
        'created_at' => 'datetime',
    ];

    // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function review()
    {
        return $this->hasOne(ProductReview::class, 'order_item_id');
    }

    // Price helpers
    public function getUnitPriceAttribute()
    {
        return $this->unit_price_cents / 100;
    }

    public function getTotalPriceAttribute()
    {
        return $this->total_price_cents / 100;
    }

    public function getCostPriceAttribute()
    {
        return $this->cost_price_cents ? $this->cost_price_cents / 100 : null;
    }

    public function getFormattedUnitPriceAttribute()
    {
        return 'Rp ' . number_format($this->unit_price, 0, ',', '.');
    }

    public function getFormattedTotalPriceAttribute()
    {
        return 'Rp ' . number_format($this->total_price, 0, ',', '.');
    }

    public function getFormattedCostPriceAttribute()
    {
        return $this->cost_price ? 'Rp ' . number_format($this->cost_price, 0, ',', '.') : null;
    }

    // Profit calculation
    public function getProfitCentsAttribute()
    {
        if (!$this->cost_price_cents) {
            return null;
        }

        return ($this->unit_price_cents - $this->cost_price_cents) * $this->quantity;
    }

    public function getProfitAttribute()
    {
        return $this->profit_cents ? $this->profit_cents / 100 : null;
    }

    public function getFormattedProfitAttribute()
    {
        return $this->profit ? 'Rp ' . number_format($this->profit, 0, ',', '.') : null;
    }

    public function getProfitMarginAttribute()
    {
        if (!$this->cost_price_cents || $this->unit_price_cents <= 0) {
            return null;
        }

        return (($this->unit_price_cents - $this->cost_price_cents) / $this->unit_price_cents) * 100;
    }

    // Helper methods
    public function hasVariant()
    {
        return !is_null($this->variant_id);
    }

    public function getDisplayNameAttribute()
    {
        $name = $this->product_name;

        if ($this->variant_name) {
            $name .= ' - ' . $this->variant_name;
        }

        return $name;
    }

    public function getImageUrlAttribute()
    {
        if ($this->variant && $this->variant->hasCustomImage()) {
            return $this->variant->image_url;
        }

        return $this->product?->image_url ?? asset('images/products/placeholder.png');
    }

    public function canBeReviewed()
    {
        return $this->order->isDelivered() && !$this->review()->exists();
    }

    public function hasBeenReviewed()
    {
        return $this->review()->exists();
    }

    public function isDigital()
    {
        return $this->product?->digital ?? false;
    }

    public function getWeightGrams()
    {
        if ($this->variant) {
            return $this->variant->final_weight_grams;
        }

        return $this->product?->weight_grams ?? 0;
    }

    public function getTotalWeightGrams()
    {
        return $this->getWeightGrams() * $this->quantity;
    }

    public function getCurrentProductPrice()
    {
        if ($this->hasVariant() && $this->variant) {
            return $this->variant->final_price_cents;
        }

        return $this->product?->price_cents;
    }

    public function isPriceChanged()
    {
        $currentPrice = $this->getCurrentProductPrice();
        return $currentPrice && $currentPrice !== $this->unit_price_cents;
    }

    public function getPriceChangePercentage()
    {
        $currentPrice = $this->getCurrentProductPrice();

        if (!$currentPrice || $this->unit_price_cents <= 0) {
            return null;
        }

        return (($currentPrice - $this->unit_price_cents) / $this->unit_price_cents) * 100;
    }

    // Scopes
    public function scopeByProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeByOrder($query, $orderId)
    {
        return $query->where('order_id', $orderId);
    }

    public function scopeWithProfit($query)
    {
        return $query->whereNotNull('cost_price_cents');
    }

    public function scopeDigital($query)
    {
        return $query->whereHas('product', function ($q) {
            $q->where('digital', true);
        });
    }

    public function scopePhysical($query)
    {
        return $query->whereHas('product', function ($q) {
            $q->where('digital', false);
        });
    }
}
