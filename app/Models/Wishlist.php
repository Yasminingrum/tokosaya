<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public $timestamps = false;

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Scopes
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // Helper methods
    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    public function getDaysAgoAttribute()
    {
        return $this->created_at->diffInDays(now());
    }

    // Static methods
    public static function toggle($userId, $productId)
    {
        $wishlist = static::where('user_id', $userId)
                          ->where('product_id', $productId)
                          ->first();

        if ($wishlist) {
            $wishlist->delete();
            return false; // Removed from wishlist
        } else {
            static::create([
                'user_id' => $userId,
                'product_id' => $productId,
            ]);
            return true; // Added to wishlist
        }
    }

    public static function isInWishlist($userId, $productId)
    {
        return static::where('user_id', $userId)
                    ->where('product_id', $productId)
                    ->exists();
    }

    public static function getWishlistCount($userId)
    {
        return static::where('user_id', $userId)->count();
    }

    // Events
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($wishlist) {
            $wishlist->created_at = now();
        });
    }
}
