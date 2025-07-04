<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'featured_image',
        'status',
        'meta_title',
        'meta_description',
        'sort_order',
        'show_in_menu',
        'view_count',
        'created_by',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'show_in_menu' => 'boolean',
        'view_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopePrivate($query)
    {
        return $query->where('status', 'private');
    }

    public function scopeInMenu($query)
    {
        return $query->where('show_in_menu', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('title');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('content', 'like', "%{$search}%")
              ->orWhere('excerpt', 'like', "%{$search}%");
        });
    }

    // Helper methods
    public function isPublished()
    {
        return $this->status === 'published';
    }

    public function isDraft()
    {
        return $this->status === 'draft';
    }

    public function isPrivate()
    {
        return $this->status === 'private';
    }

    public function isInMenu()
    {
        return $this->show_in_menu;
    }

    public function hasFeaturedImage()
    {
        return !empty($this->featured_image);
    }

    public function getFeaturedImageUrlAttribute()
    {
        if (!$this->featured_image) {
            return null;
        }

        if (Str::startsWith($this->featured_image, ['http://', 'https://'])) {
            return $this->featured_image;
        }

        return asset('storage/' . $this->featured_image);
    }

    public function getExcerptOrContentAttribute($length = 160)
    {
        if ($this->excerpt) {
            return $this->excerpt;
        }

        if ($this->content) {
            $plainText = strip_tags($this->content);
            return Str::limit($plainText, $length);
        }

        return '';
    }

    public function getReadingTimeAttribute()
    {
        if (!$this->content) {
            return 0;
        }

        $wordCount = str_word_count(strip_tags($this->content));
        $averageWordsPerMinute = 200;

        return max(1, ceil($wordCount / $averageWordsPerMinute));
    }

    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'published' => 'badge-success',
            'draft' => 'badge-warning',
            'private' => 'badge-secondary',
            default => 'badge-light',
        };
    }

    public function getMetaTitleOrTitleAttribute()
    {
        return $this->meta_title ?: $this->title;
    }

    public function getMetaDescriptionOrExcerptAttribute()
    {
        return $this->meta_description ?: $this->excerpt_or_content;
    }

    public function incrementViewCount()
    {
        $this->increment('view_count');
    }

    public function getUrlAttribute()
    {
        return route('pages.show', $this->slug);
    }

    // Static methods
    public static function findBySlug($slug)
    {
        return static::where('slug', $slug)->first();
    }

    public static function getMenuPages()
    {
        return static::published()
                    ->inMenu()
                    ->ordered()
                    ->get();
    }

    // Events
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($page) {
            if (empty($page->slug)) {
                $page->slug = Str::slug($page->title);
            }
        });

        static::updating(function ($page) {
            if ($page->isDirty('title') && empty($page->slug)) {
                $page->slug = Str::slug($page->title);
            }
        });
    }
}
