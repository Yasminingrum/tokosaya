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
        'approved_at',
    ];

    protected $casts = [
        'rating' => 'integer',
        'images' => 'array',
        'helpful_count' => 'integer',
        'is_verified' => 'boolean',
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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
        return $this->belongsTo(OrderItem::class, 'order_item_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
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

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeByRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }

    public function scopeByProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeHelpful($query, $minCount = 1)
    {
        return $query->where('helpful_count', '>=', $minCount);
    }

    public function scopeWithImages($query)
    {
        return $query->whereNotNull('images')->where('images', '!=', '[]');
    }

    // Helper methods
    public function isApproved()
    {
        return $this->is_approved;
    }

    public function isPending()
    {
        return !$this->is_approved;
    }

    public function isVerified()
    {
        return $this->is_verified;
    }

    public function hasImages()
    {
        return !empty($this->images);
    }

    public function getImageUrls()
    {
        if (!$this->hasImages()) {
            return [];
        }

        return collect($this->images)->map(function ($image) {
            if (str_starts_with($image, ['http://', 'https://'])) {
                return $image;
            }
            return asset('storage/' . $image);
        })->toArray();
    }

    public function getStarsAttribute()
    {
        return str_repeat('★', $this->rating) . str_repeat('☆', 5 - $this->rating);
    }

    public function getRatingClassAttribute()
    {
        return match($this->rating) {
            5 => 'text-success',
            4 => 'text-info',
            3 => 'text-warning',
            2 => 'text-warning',
            1 => 'text-danger',
            default => 'text-muted',
        };
    }

    public function getShortReviewAttribute($length = 100)
    {
        if (!$this->review) {
            return '';
        }

        return strlen($this->review) > $length
            ? substr($this->review, 0, $length) . '...'
            : $this->review;
    }

    public function getAuthorNameAttribute()
    {
        // Hide partial name for privacy
        $firstName = $this->user->first_name;
        $lastName = $this->user->last_name;

        if (strlen($lastName) > 1) {
            $lastName = substr($lastName, 0, 1) . str_repeat('*', strlen($lastName) - 1);
        }

        return $firstName . ' ' . $lastName;
    }

    public function getDaysAgoAttribute()
    {
        return $this->created_at->diffInDays(now());
    }

    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    public function canBeApproved()
    {
        return !$this->is_approved;
    }

    public function canBeRejected()
    {
        return !$this->is_approved;
    }

    public function isHelpful($minCount = 5)
    {
        return $this->helpful_count >= $minCount;
    }

    // Actions
    public function approve($approvedBy = null)
    {
        $this->update([
            'is_approved' => true,
            'approved_by' => $approvedBy,
            'approved_at' => now(),
        ]);

        // Update product rating
        $this->updateProductRating();
    }

    public function reject()
    {
        $this->update(['is_approved' => false]);

        // Update product rating
        $this->updateProductRating();
    }

    public function incrementHelpful()
    {
        $this->increment('helpful_count');
    }

    public function markAsVerified()
    {
        $this->update(['is_verified' => true]);
    }

    protected function updateProductRating()
    {
        $approvedReviews = $this->product->approvedReviews();

        $avgRating = $approvedReviews->avg('rating') ?: 0;
        $reviewCount = $approvedReviews->count();

        $this->product->update([
            'rating_average' => round($avgRating, 2),
            'rating_count' => $reviewCount,
        ]);
    }

    // Events
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($review) {
            // Mark as verified if user purchased the product
            if ($review->order_item_id) {
                $review->is_verified = true;
            }
        });

        static::saved(function ($review) {
            // Update product rating when review is approved/rejected
            if ($review->isDirty('is_approved')) {
                $review->updateProductRating();
            }
        });

        static::deleted(function ($review) {
            // Update product rating when review is deleted
            $review->updateProductRating();
        });
    }
}
