<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'logo',
        'website',
        'product_count',
        'is_active',
    ];

    protected $casts = [
        'product_count' => 'integer',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function activeProducts()
    {
        return $this->hasMany(Product::class)->where('status', 'active');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeWithProducts($query)
    {
        return $query->where('product_count', '>', 0);
    }

    public function scopePopular($query, $limit = 10)
    {
        return $query->where('is_active', true)
                    ->orderBy('product_count', 'desc')
                    ->limit($limit);
    }

    // Helper methods
    public function hasProducts()
    {
        return $this->product_count > 0;
    }

    public function updateProductCount()
    {
        $this->product_count = $this->activeProducts()->count();
        $this->save();
    }

    public function getLogoUrlAttribute()
    {
        if ($this->logo) {
            if (Str::startsWith($this->logo, ['http://', 'https://'])) {
                return $this->logo;
            }
            return asset('storage/' . $this->logo);
        }

        return asset('images/brands/default.png');
    }

    public function hasWebsite()
    {
        return !empty($this->website);
    }

    public function getWebsiteUrlAttribute()
    {
        if (empty($this->website)) {
            return null;
        }

        if (!Str::startsWith($this->website, ['http://', 'https://'])) {
            return 'https://' . $this->website;
        }

        return $this->website;
    }

    // Events
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($brand) {
            if (empty($brand->slug)) {
                $brand->slug = Str::slug($brand->name);
            }
        });

        static::updating(function ($brand) {
            if ($brand->isDirty('name') && empty($brand->slug)) {
                $brand->slug = Str::slug($brand->name);
            }
        });

        static::deleted(function ($brand) {
            // Update products to remove brand reference
            $brand->products()->update(['brand_id' => null]);
        });
    }
}
