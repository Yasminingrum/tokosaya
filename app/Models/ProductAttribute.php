<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'options',
        'is_required',
        'is_filterable',
        'is_searchable',
        'sort_order',
    ];

    protected $casts = [
        'options' => 'array',
        'is_required' => 'boolean',
        'is_filterable' => 'boolean',
        'is_searchable' => 'boolean',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function values()
    {
        return $this->hasMany(ProductAttributeValue::class, 'attribute_id');
    }

    // Scopes
    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    public function scopeFilterable($query)
    {
        return $query->where('is_filterable', true);
    }

    public function scopeSearchable($query)
    {
        return $query->where('is_searchable', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // Helper methods
    public function isRequired()
    {
        return $this->is_required;
    }

    public function isFilterable()
    {
        return $this->is_filterable;
    }

    public function isSearchable()
    {
        return $this->is_searchable;
    }

    public function hasOptions()
    {
        return in_array($this->type, ['select', 'multiselect']) && !empty($this->options);
    }

    public function getOptionsListAttribute()
    {
        return $this->hasOptions() ? collect($this->options) : collect();
    }

    public function isText()
    {
        return $this->type === 'text';
    }

    public function isNumber()
    {
        return $this->type === 'number';
    }

    public function isBoolean()
    {
        return $this->type === 'boolean';
    }

    public function isSelect()
    {
        return in_array($this->type, ['select', 'multiselect']);
    }

    public function isColor()
    {
        return $this->type === 'color';
    }

    public function isSize()
    {
        return $this->type === 'size';
    }

    public function getInputTypeAttribute()
    {
        return match($this->type) {
            'number' => 'number',
            'boolean' => 'checkbox',
            'select' => 'select',
            'multiselect' => 'select',
            'color' => 'color',
            default => 'text',
        };
    }

    public function validateValue($value)
    {
        switch ($this->type) {
            case 'number':
                return is_numeric($value);
            case 'boolean':
                return is_bool($value) || in_array($value, [0, 1, '0', '1', 'true', 'false']);
            case 'select':
                return $this->hasOptions() && in_array($value, $this->options);
            case 'multiselect':
                if (!is_array($value)) return false;
                return $this->hasOptions() && empty(array_diff($value, $this->options));
            default:
                return true;
        }
    }

    public function formatValue($value)
    {
        switch ($this->type) {
            case 'number':
                return (float) $value;
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'multiselect':
                return is_array($value) ? $value : [$value];
            default:
                return $value;
        }
    }
}
