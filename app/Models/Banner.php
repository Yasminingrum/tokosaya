<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'image',
        'mobile_image',
        'link_url',
        'link_text',
        'position',
        'sort_order',
        'click_count',
        'impression_count',
        'is_active',
        'starts_at',
        'expires_at',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'click_count' => 'integer',
        'impression_count' => 'integer',
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByPosition($query, $position)
    {
        return $query->where('position', $position);
    }

    public function scopeValid($query)
    {
        return $query->where('is_active', true)
                    ->where(function ($q) {
                        $q->whereNull('starts_at')
                          ->orWhere('starts_at', '<=', now());
                    })
                    ->where(function ($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>=', now());
                    });
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at', 'desc');
    }

    // Helper methods
    public function isActive()
    {
        return $this->is_active;
    }

    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isNotStarted()
    {
        return $this->starts_at && $this->starts_at->isFuture();
    }

    public function isValid()
    {
        return $this->is_active && !$this->isExpired() && !$this->isNotStarted();
    }

    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return null;
        }

        if (Str::startsWith($this->image, ['http://', 'https://'])) {
            return $this->image;
        }

        return asset('storage/' . $this->image);
    }

    public function getMobileImageUrlAttribute()
    {
        if (!$this->mobile_image) {
            return $this->image_url; // Fallback to desktop image
        }

        if (Str::startsWith($this->mobile_image, ['http://', 'https://'])) {
            return $this->mobile_image;
        }

        return asset('storage/' . $this->mobile_image);
    }

    public function hasLink()
    {
        return !empty($this->link_url);
    }

    public function incrementClick()
    {
        $this->increment('click_count');
    }

    public function incrementImpression()
    {
        $this->increment('impression_count');
    }

    public function getClickThroughRateAttribute()
    {
        if ($this->impression_count == 0) {
            return 0;
        }

        return round(($this->click_count / $this->impression_count) * 100, 2);
    }
}
