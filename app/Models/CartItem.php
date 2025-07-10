<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

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
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
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
        if ($this->variant && $this->variant->image) {
            return $this->variant->image;
        }

        return $this->product->images->first()->image_url ?? asset('images/placeholder.jpg');
    }
}
