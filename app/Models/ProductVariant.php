<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'variant_name',
        'variant_value',
        'price_adjustment_cents',
        'stock_quantity',
        'reserved_quantity',
        'sku',
        'barcode',
        'image',
        'weight_adjustment_grams',
        'is_active',
    ];

    protected $casts = [
        'price_adjustment_cents' => 'integer',
        'stock_quantity' => 'integer',
        'reserved_quantity' => 'integer',
        'weight_adjustment_grams' => 'integer',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'variant_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'variant_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    public function scopeByName($query, $name)
    {
        return $query->where('variant_name', $name);
    }

    public function scopeByValue($query, $value)
    {
        return $query->where('variant_value', $value);
    }

    // Price helpers
    public function getPriceAdjustmentAttribute()
    {
        return $this->price_adjustment_cents / 100;
    }

    public function getFinalPriceCentsAttribute()
    {
        return $this->product->price_cents + $this->price_adjustment_cents;
    }

    public function getFinalPriceAttribute()
    {
        return $this->final_price_cents / 100;
    }

    public function getFormattedFinalPriceAttribute()
    {
        return 'Rp ' . number_format($this->final_price, 0, ',', '.');
    }

    // Stock helpers
    public function getAvailableStockAttribute()
    {
        return max(0, $this->stock_quantity - $this->reserved_quantity);
    }

    public function isInStock()
    {
        return $this->available_stock > 0 || $this->product->allow_backorder;
    }

    public function isOutOfStock()
    {
        return $this->available_stock <= 0;
    }

    // Weight helpers
    public function getFinalWeightGramsAttribute()
    {
        return $this->product->weight_grams + $this->weight_adjustment_grams;
    }

    public function getFinalWeightInKgAttribute()
    {
        return $this->final_weight_grams / 1000;
    }

    // Other helpers
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            if (Str::startsWith($this->image, ['http://', 'https://'])) {
                return $this->image;
            }
            return asset('storage/' . $this->image);
        }

        return $this->product->image_url;
    }

    public function getDisplayNameAttribute()
    {
        return "{$this->variant_name}: {$this->variant_value}";
    }

    public function getFullNameAttribute()
    {
        return "{$this->product->name} - {$this->display_name}";
    }

    public function hasPriceAdjustment()
    {
        return $this->price_adjustment_cents != 0;
    }

    public function hasWeightAdjustment()
    {
        return $this->weight_adjustment_grams != 0;
    }

    public function hasCustomImage()
    {
        return !empty($this->image);
    }

    // Events
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($variant) {
            if (empty($variant->sku)) {
                $productSku = $variant->product->sku ?? 'PRD';
                $variant->sku = $productSku . '-' . strtoupper(Str::random(4));
            }
        });

        static::updated(function ($variant) {
            // Update parent product stock if this variant's stock changed
            if ($variant->isDirty('stock_quantity')) {
                $variant->product->updateTotalStock();
            }
        });
    }
}
