<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'icon',
        'parent_id',
        'path',
        'level',
        'sort_order',
        'product_count',
        'is_active',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'level' => 'integer',
        'sort_order' => 'integer',
        'product_count' => 'integer',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('sort_order');
    }

    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }

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

    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function scopeWithProductCount($query)
    {
        return $query->where('product_count', '>', 0);
    }

    // Helper methods
    public function isRoot()
    {
        return is_null($this->parent_id);
    }

    public function hasChildren()
    {
        return $this->children()->count() > 0;
    }

    public function hasProducts()
    {
        return $this->product_count > 0;
    }

    public function getAncestors()
    {
        $ancestors = collect();
        $current = $this->parent;

        while ($current) {
            $ancestors->prepend($current);
            $current = $current->parent;
        }

        return $ancestors;
    }

    public function getDescendants()
    {
        $descendants = collect();

        foreach ($this->children as $child) {
            $descendants->push($child);
            $descendants = $descendants->merge($child->getDescendants());
        }

        return $descendants;
    }

    public function getBreadcrumb()
    {
        $breadcrumb = $this->getAncestors();
        $breadcrumb->push($this);

        return $breadcrumb;
    }

    public function getPathArray()
    {
        if (empty($this->path)) {
            return [];
        }

        return array_filter(explode('/', trim($this->path, '/')));
    }

    public function updateProductCount()
    {
        $this->product_count = $this->activeProducts()->count();
        $this->save();
    }

    // Events
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }

            // Set path and level based on parent
            if ($category->parent_id) {
                $parent = static::find($category->parent_id);
                if ($parent) {
                    $category->path = rtrim($parent->path, '/') . '/' . $parent->id . '/';
                    $category->level = $parent->level + 1;
                }
            } else {
                $category->path = '/';
                $category->level = 0;
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name') && empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }

            // Update path if parent changed
            if ($category->isDirty('parent_id')) {
                if ($category->parent_id) {
                    $parent = static::find($category->parent_id);
                    if ($parent) {
                        $category->path = rtrim($parent->path, '/') . '/' . $parent->id . '/';
                        $category->level = $parent->level + 1;
                    }
                } else {
                    $category->path = '/';
                    $category->level = 0;
                }
            }
        });

        static::updated(function ($category) {
            // Update children paths if this category's path changed
            if ($category->isDirty('path')) {
                $category->updateChildrenPaths();
            }
        });

        static::deleted(function ($category) {
            // Update parent's product count
            if ($category->parent) {
                $category->parent->updateProductCount();
            }
        });
    }

    private function updateChildrenPaths()
    {
        foreach ($this->children as $child) {
            $child->path = rtrim($this->path, '/') . '/' . $this->id . '/';
            $child->level = $this->level + 1;
            $child->save();
        }
    }
}
