<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'key_name',
        'value',
        'description',
        'type',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Scopes
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeByKey($query, $key)
    {
        return $query->where('key_name', $key);
    }

    // Helper methods
    public function getTypedValueAttribute()
    {
        return match($this->type) {
            'boolean' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'number' => is_numeric($this->value) ? (float) $this->value : 0,
            'json' => json_decode($this->value, true),
            default => $this->value,
        };
    }

    public function isPublic()
    {
        return $this->is_public;
    }

    // Static methods
    public static function get($key, $default = null, $category = 'general')
    {
        $cacheKey = "settings.{$category}.{$key}";

        return Cache::remember($cacheKey, 3600, function () use ($key, $default, $category) {
            $setting = static::where('category', $category)
                             ->where('key_name', $key)
                             ->first();

            return $setting ? $setting->typed_value : $default;
        });
    }

    public static function set($key, $value, $category = 'general', $type = 'string', $description = null, $isPublic = false)
    {
        $setting = static::updateOrCreate(
            ['category' => $category, 'key_name' => $key],
            [
                'value' => is_array($value) ? json_encode($value) : $value,
                'type' => $type,
                'description' => $description,
                'is_public' => $isPublic,
            ]
        );

        // Clear cache
        Cache::forget("settings.{$category}.{$key}");

        return $setting;
    }

    public static function getByCategory($category)
    {
        $cacheKey = "settings.category.{$category}";

        return Cache::remember($cacheKey, 3600, function () use ($category) {
            return static::where('category', $category)
                         ->pluck('value', 'key_name')
                         ->toArray();
        });
    }

    public static function getPublicSettings()
    {
        $cacheKey = 'settings.public';

        return Cache::remember($cacheKey, 3600, function () {
            return static::where('is_public', true)
                         ->get()
                         ->groupBy('category')
                         ->map(function ($settings) {
                             return $settings->pluck('typed_value', 'key_name');
                         });
        });
    }

    public static function clearCache($category = null, $key = null)
    {
        if ($category && $key) {
            Cache::forget("settings.{$category}.{$key}");
        } elseif ($category) {
            Cache::forget("settings.category.{$category}");
        } else {
            Cache::forget('settings.public');
            // Clear all settings cache - in production you might want to be more specific
            Cache::tags(['settings'])->flush();
        }
    }

    // Events
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($setting) {
            static::clearCache($setting->category, $setting->key_name);
            static::clearCache('public');
        });

        static::deleted(function ($setting) {
            static::clearCache($setting->category, $setting->key_name);
            static::clearCache('public');
        });
    }
}
