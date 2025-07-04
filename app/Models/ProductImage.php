<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'image_url',
        'alt_text',
        'sort_order',
        'is_primary',
        'width',
        'height',
        'file_size',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_primary' => 'boolean',
        'width' => 'integer',
        'height' => 'integer',
        'file_size' => 'integer',
        'created_at' => 'datetime',
    ];

    // Relationships
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Scopes
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    // Helper methods
    public function getFullUrlAttribute()
    {
        if (Str::startsWith($this->image_url, ['http://', 'https://'])) {
            return $this->image_url;
        }

        return asset('storage/' . $this->image_url);
    }

    public function getThumbnailUrlAttribute($size = 300)
    {
        // In a real application, you might use image processing service
        return $this->full_url;
    }

    public function getFileSizeFormattedAttribute()
    {
        if (!$this->file_size) {
            return null;
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->file_size;
        $unit = 0;

        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }

        return round($size, 2) . ' ' . $units[$unit];
    }

    public function getAspectRatioAttribute()
    {
        if (!$this->width || !$this->height) {
            return null;
        }

        return $this->width / $this->height;
    }

    public function isLandscape()
    {
        return $this->aspect_ratio > 1;
    }

    public function isPortrait()
    {
        return $this->aspect_ratio < 1;
    }

    public function isSquare()
    {
        return abs($this->aspect_ratio - 1) < 0.1;
    }

    // Events
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($image) {
            // If this is set as primary, unset other primary images for this product
            if ($image->is_primary) {
                static::where('product_id', $image->product_id)
                    ->where('is_primary', true)
                    ->update(['is_primary' => false]);
            }

            // If this is the first image, make it primary
            $existingCount = static::where('product_id', $image->product_id)->count();
            if ($existingCount === 0) {
                $image->is_primary = true;
                $image->sort_order = 0;
            } else if (is_null($image->sort_order)) {
                $maxOrder = static::where('product_id', $image->product_id)->max('sort_order');
                $image->sort_order = $maxOrder + 1;
            }
        });

        static::updating(function ($image) {
            if ($image->is_primary && $image->isDirty('is_primary')) {
                static::where('product_id', $image->product_id)
                    ->where('id', '!=', $image->id)
                    ->where('is_primary', true)
                    ->update(['is_primary' => false]);
            }
        });

        static::deleted(function ($image) {
            // If primary image was deleted, make the first remaining image primary
            if ($image->is_primary) {
                $firstImage = static::where('product_id', $image->product_id)
                    ->orderBy('sort_order')
                    ->first();

                if ($firstImage) {
                    $firstImage->update(['is_primary' => true]);
                }
            }
        });
    }
}
