<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ShoppingCart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'guest_token',
        'item_count',
        'total_cents',
        'expires_at',
    ];

    protected $casts = [
        'item_count' => 'integer',
        'total_cents' => 'integer',
        'expires_at' => 'datetime',
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
        return $this->hasMany(CartItem::class, 'cart_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForSession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    public function scopeForGuest($query, $guestToken)
    {
        return $query->where('guest_token', $guestToken);
    }

    // Helper methods
    public function getTotalAttribute()
    {
        return $this->total_cents / 100;
    }

    public function getFormattedTotalAttribute()
    {
        return 'Rp ' . number_format($this->total, 0, ',', '.');
    }

    public function isEmpty()
    {
        return $this->item_count == 0;
    }

    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isForGuest()
    {
        return is_null($this->user_id);
    }

    public function isForUser()
    {
        return !is_null($this->user_id);
    }

    public function addItem($product, $quantity = 1, $variantId = null)
    {
        $variant = $variantId ? ProductVariant::find($variantId) : null;
        $unitPrice = $variant ? $variant->final_price_cents : $product->price_cents;

        $existingItem = $this->items()
            ->where('product_id', $product->id)
            ->where('variant_id', $variantId)
            ->first();

        if ($existingItem) {
            $existingItem->update([
                'quantity' => $existingItem->quantity + $quantity,
                'total_price_cents' => ($existingItem->quantity + $quantity) * $unitPrice,
            ]);
            return $existingItem;
        }

        return $this->items()->create([
            'product_id' => $product->id,
            'variant_id' => $variantId,
            'quantity' => $quantity,
            'unit_price_cents' => $unitPrice,
            'total_price_cents' => $quantity * $unitPrice,
        ]);
    }

    public function updateItemQuantity($itemId, $quantity)
    {
        $item = $this->items()->findOrFail($itemId);

        if ($quantity <= 0) {
            return $this->removeItem($itemId);
        }

        $item->update([
            'quantity' => $quantity,
            'total_price_cents' => $quantity * $item->unit_price_cents,
        ]);

        return $item;
    }

    public function removeItem($itemId)
    {
        $item = $this->items()->findOrFail($itemId);
        $item->delete();
        return true;
    }

    public function clear()
    {
        $this->items()->delete();
        $this->update([
            'item_count' => 0,
            'total_cents' => 0,
        ]);
    }

    public function mergeCarts(ShoppingCart $otherCart)
    {
        foreach ($otherCart->items as $item) {
            $this->addItem(
                $item->product,
                $item->quantity,
                $item->variant_id
            );
        }

        $otherCart->delete();
    }

    public function extendExpiry($hours = 24)
    {
        $this->update([
            'expires_at' => now()->addHours($hours),
        ]);
    }

    public function getTotalWeight()
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

    public function getItemByProduct($productId, $variantId = null)
    {
        return $this->items()
            ->where('product_id', $productId)
            ->where('variant_id', $variantId)
            ->first();
    }

    public function hasItem($productId, $variantId = null)
    {
        return $this->getItemByProduct($productId, $variantId) !== null;
    }

    // Static methods
    public static function findOrCreateForUser($userId)
    {
        return static::firstOrCreate(
            ['user_id' => $userId],
            ['expires_at' => now()->addDays(30)]
        );
    }

    public static function findOrCreateForSession($sessionId)
    {
        return static::firstOrCreate(
            ['session_id' => $sessionId],
            ['expires_at' => now()->addHours(24)]
        );
    }

    public static function findOrCreateForGuest($guestToken)
    {
        return static::firstOrCreate(
            ['guest_token' => $guestToken],
            ['expires_at' => now()->addHours(24)]
        );
    }

    // Events
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($cart) {
            if (is_null($cart->expires_at)) {
                $cart->expires_at = $cart->user_id
                    ? now()->addDays(30)
                    : now()->addHours(24);
            }
        });
    }
}
