<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'brand_id',
        'name',
        'slug',
        'description',
        'short_description',
        'sku',
        'barcode',
        'price_cents',
        'compare_price_cents',
        'cost_price_cents',
        'stock_quantity',
        'reserved_quantity',
        'min_stock_level',
        'max_stock_level',
        'weight_grams',
        'length_mm',
        'width_mm',
        'height_mm',
        'status',
        'featured',
        'digital',
        'track_stock',
        'allow_backorder',
        'rating_average',
        'rating_count',
        'view_count',
        'sale_count',
        'revenue_cents',
        'last_sold_at',
        'meta_title',
        'meta_description',
        'created_by',
    ];

    protected $casts = [
        'price_cents' => 'integer',
        'compare_price_cents' => 'integer',
        'cost_price_cents' => 'integer',
        'stock_quantity' => 'integer',
        'reserved_quantity' => 'integer',
        'min_stock_level' => 'integer',
        'max_stock_level' => 'integer',
        'weight_grams' => 'integer',
        'length_mm' => 'integer',
        'width_mm' => 'integer',
        'height_mm' => 'integer',
        'featured' => 'boolean',
        'digital' => 'boolean',
        'track_stock' => 'boolean',
        'allow_backorder' => 'boolean',
        'rating_average' => 'decimal:2',
        'rating_count' => 'integer',
        'view_count' => 'integer',
        'sale_count' => 'integer',
        'revenue_cents' => 'integer',
        'last_sold_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function activeVariants()
    {
        return $this->hasMany(ProductVariant::class)->where('is_active', true);
    }

    public function attributes()
    {
        return $this->hasMany(ProductAttributeValue::class);
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    public function approvedReviews()
    {
        return $this->hasMany(ProductReview::class)->where('is_approved', true);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock_quantity', '<=', 'min_stock_level');
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('stock_quantity', 0);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeByBrand($query, $brandId)
    {
        return $query->where('brand_id', $brandId);
    }

    public function scopePriceRange($query, $minCents, $maxCents)
    {
        return $query->whereBetween('price_cents', [$minCents, $maxCents]);
    }

    public function scopePopular($query, $limit = 10)
    {
        return $query->where('status', 'active')
                    ->orderBy('sale_count', 'desc')
                    ->limit($limit);
    }

    public function scopeTopRated($query, $limit = 10)
    {
        return $query->where('status', 'active')
                    ->where('rating_count', '>', 0)
                    ->orderBy('rating_average', 'desc')
                    ->limit($limit);
    }

    public function scopeRecent($query, $limit = 10)
    {
        return $query->where('status', 'active')
                    ->orderBy('created_at', 'desc')
                    ->limit($limit);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('short_description', 'like', "%{$search}%")
              ->orWhere('sku', 'like', "%{$search}%");
        });
    }

    // Price helpers
    public function getPriceAttribute()
    {
        return $this->price_cents / 100;
    }

    public function getComparePriceAttribute()
    {
        return $this->compare_price_cents ? $this->compare_price_cents / 100 : null;
    }

    public function getCostPriceAttribute()
    {
        return $this->cost_price_cents ? $this->cost_price_cents / 100 : null;
    }

    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    public function getFormattedComparePriceAttribute()
    {
        return $this->compare_price ? 'Rp ' . number_format($this->compare_price, 0, ',', '.') : null;
    }

    // Stock helpers
    public function getAvailableStockAttribute()
    {
        return max(0, $this->stock_quantity - $this->reserved_quantity);
    }

    public function isInStock()
    {
        return $this->available_stock > 0 || $this->allow_backorder;
    }

    public function isLowStock()
    {
        return $this->track_stock && $this->stock_quantity <= $this->min_stock_level;
    }

    public function isOutOfStock()
    {
        return $this->track_stock && $this->stock_quantity <= 0;
    }

    // Other helpers
    public function hasDiscount()
    {
        return $this->compare_price_cents && $this->compare_price_cents > $this->price_cents;
    }

    public function getDiscountPercentageAttribute()
    {
        if (!$this->hasDiscount()) {
            return 0;
        }

        return round((($this->compare_price_cents - $this->price_cents) / $this->compare_price_cents) * 100);
    }

    public function hasVariants()
    {
        return $this->variants()->count() > 0;
    }

    public function isDigital()
    {
        return $this->digital;
    }

    public function isFeatured()
    {
        return $this->featured;
    }

    public function hasReviews()
    {
        return $this->rating_count > 0;
    }

    public function getWeightInKgAttribute()
    {
        return $this->weight_grams / 1000;
    }

    public function getDimensionsAttribute()
    {
        if (!$this->length_mm || !$this->width_mm || !$this->height_mm) {
            return null;
        }

        return [
            'length' => $this->length_mm / 10, // cm
            'width' => $this->width_mm / 10,   // cm
            'height' => $this->height_mm / 10,  // cm
        ];
    }

    public function incrementViewCount()
    {
        $this->increment('view_count');
    }

    public function getImageUrlAttribute()
    {
        return $this->primaryImage?->image_url ?? asset('images/products/placeholder.png');
    }

    // Events
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }

            if (empty($product->sku)) {
                $product->sku = 'PRD-' . strtoupper(Str::random(8));
            }
        });

        static::updating(function ($product) {
            if ($product->isDirty('name') && empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });

        static::saved(function ($product) {
            // Update category product count
            if ($product->category) {
                $product->category->updateProductCount();
            }

            // Update brand product count
            if ($product->brand) {
                $product->brand->updateProductCount();
            }
        });

        static::deleted(function ($product) {
            // Update category product count
            if ($product->category) {
                $product->category->updateProductCount();
            }

            // Update brand product count
            if ($product->brand) {
                $product->brand->updateProductCount();
            }
        });
    }
}
