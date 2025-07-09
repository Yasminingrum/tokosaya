<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    // Disable automatic timestamps since we use custom timestamp columns
    public $timestamps = false;

    protected $fillable = [
        'cart_id',
        'product_id',
        'variant_id',
        'quantity',
        'unit_price_cents',
        'total_price_cents',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price_cents' => 'integer',
        'total_price_cents' => 'integer',
        'added_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Define custom timestamp column names
    const CREATED_AT = 'added_at';
    const UPDATED_AT = 'updated_at';

    // Rest of your model methods remain the same...
    public function cart()
    {
        return $this->belongsTo(ShoppingCart::class, 'cart_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    // Helper methods
    public function getUnitPriceAttribute()
    {
        return $this->unit_price_cents / 100;
    }

    public function getTotalPriceAttribute()
    {
        return $this->total_price_cents / 100;
    }

    public function getFormattedUnitPriceAttribute()
    {
        return 'Rp ' . number_format($this->unit_price, 0, ',', '.');
    }

    public function getFormattedTotalPriceAttribute()
    {
        return 'Rp ' . number_format($this->total_price, 0, ',', '.');
    }

    public function getCurrentPriceCents()
    {
        return $this->variant
            ? $this->variant->final_price_cents
            : $this->product->price_cents;
    }

    public function hasVariant()
    {
        return !is_null($this->variant_id);
    }

    public function getDisplayNameAttribute()
    {
        $name = $this->product->name;

        if ($this->variant) {
            $name .= ' - ' . $this->variant->display_name;
        }

        return $name;
    }

    public function getImageUrlAttribute()
    {
        if ($this->variant && $this->variant->hasCustomImage()) {
            return $this->variant->image_url;
        }

        return $this->product->image_url;
    }

    public function getWeightGrams()
    {
        return $this->variant
            ? $this->variant->final_weight_grams
            : $this->product->weight_grams;
    }

    public function getTotalWeightGrams()
    {
        return $this->getWeightGrams() * $this->quantity;
    }

    public function isAvailable()
    {
        if ($this->hasVariant()) {
            return $this->variant->is_active && $this->variant->isInStock();
        }

        return $this->product->status === 'active' && $this->product->isInStock();
    }

    public function getAvailableStock()
    {
        if ($this->hasVariant()) {
            return $this->variant->available_stock;
        }

        return $this->product->available_stock;
    }

    public function canIncrease($amount = 1)
    {
        $newQuantity = $this->quantity + $amount;
        return $newQuantity <= $this->getAvailableStock();
    }

    public function isPriceChanged()
    {
        return $this->unit_price_cents !== $this->getCurrentPriceCents();
    }

    public function updateToCurrentPrice()
    {
        $currentPrice = $this->getCurrentPriceCents();

        $this->update([
            'unit_price_cents' => $currentPrice,
            'total_price_cents' => $currentPrice * $this->quantity,
        ]);
    }

    public function increaseQuantity($amount = 1)
    {
        if (!$this->canIncrease($amount)) {
            throw new \Exception('Not enough stock available');
        }

        $newQuantity = $this->quantity + $amount;

        $this->update([
            'quantity' => $newQuantity,
            'total_price_cents' => $this->unit_price_cents * $newQuantity,
        ]);
    }

    public function decreaseQuantity($amount = 1)
    {
        $newQuantity = $this->quantity - $amount;

        if ($newQuantity <= 0) {
            $this->delete();
            return null;
        }

        $this->update([
            'quantity' => $newQuantity,
            'total_price_cents' => $this->unit_price_cents * $newQuantity,
        ]);

        return $this;
    }

    public function setQuantity($quantity)
    {
        if ($quantity <= 0) {
            $this->delete();
            return null;
        }

        if ($quantity > $this->getAvailableStock()) {
            throw new \Exception('Not enough stock available');
        }

        $this->update([
            'quantity' => $quantity,
            'total_price_cents' => $this->unit_price_cents * $quantity,
        ]);

        return $this;
    }

    // Events
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($cartItem) {
            $cartItem->added_at = now();
            $cartItem->updated_at = now();
        });

        static::updating(function ($cartItem) {
            $cartItem->updated_at = now();
        });

        static::saved(function ($cartItem) {
            $cartItem->cart->refresh();
            $cartItem->cart->update([
                'item_count' => $cartItem->cart->items()->sum('quantity'),
                'total_cents' => $cartItem->cart->items()->sum('total_price_cents'),
                'updated_at' => now(),
            ]);
        });

        static::deleted(function ($cartItem) {
            $cart = $cartItem->cart;
            $cart->update([
                'item_count' => $cart->items()->sum('quantity'),
                'total_cents' => $cart->items()->sum('total_price_cents'),
                'updated_at' => now(),
            ]);
        });
    }
}
