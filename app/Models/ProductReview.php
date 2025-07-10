<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'order_item_id',
        'rating',
        'title',
        'review',
        'images',
        'helpful_count',
        'is_verified',
        'is_approved',
        'approved_by',
        'approved_at'
    ];

    protected $casts = [
        'images' => 'array',
        'is_verified' => 'boolean',
        'is_approved' => 'boolean',
        'approved_at' => 'datetime'
    ];

    // Relationships
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // FIX: Tambahkan method yang hilang
    public function helpfulUsers()
    {
        return $this->belongsToMany(User::class, 'review_helpful_votes')
                    ->withTimestamps();
    }

    // Helper methods
    public function getStarRatingAttribute()
    {
        return str_repeat('★', $this->rating) . str_repeat('☆', 5 - $this->rating);
    }

    public function getIsHelpfulAttribute()
    {
        return $this->helpful_count > 0;
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopePending($query)
    {
        return $query->where('is_approved', false);
    }

    public function scopeByRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }
}
