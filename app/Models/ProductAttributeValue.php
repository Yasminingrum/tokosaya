<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAttributeValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'attribute_id',
        'value_text',
        'value_number',
        'value_boolean',
    ];

    protected $casts = [
        'value_number' => 'decimal:4',
        'value_boolean' => 'boolean',
        'created_at' => 'datetime',
    ];

    // Relationships
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function attribute()
    {
        return $this->belongsTo(ProductAttribute::class, 'attribute_id');
    }

    // Scopes
    public function scopeByProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeByAttribute($query, $attributeId)
    {
        return $query->where('attribute_id', $attributeId);
    }

    public function scopeTextValue($query, $value)
    {
        return $query->where('value_text', $value);
    }

    public function scopeNumberValue($query, $value)
    {
        return $query->where('value_number', $value);
    }

    public function scopeBooleanValue($query, $value)
    {
        return $query->where('value_boolean', $value);
    }

    // Helper methods
    public function getValueAttribute()
    {
        if (!$this->attribute) {
            return null;
        }

        return match($this->attribute->type) {
            'number' => $this->value_number,
            'boolean' => $this->value_boolean,
            default => $this->value_text,
        };
    }

    public function getFormattedValueAttribute()
    {
        if (!$this->attribute) {
            return $this->value;
        }

        $value = $this->value;

        return match($this->attribute->type) {
            'boolean' => $value ? 'Yes' : 'No',
            'number' => number_format($value, 2),
            'color' => '<span class="color-swatch" style="background-color: ' . $value . '"></span> ' . $value,
            default => $value,
        };
    }

    public function setValue($value)
    {
        if (!$this->attribute) {
            throw new \Exception('Attribute relationship must be loaded');
        }

        // Reset all value fields
        $this->value_text = null;
        $this->value_number = null;
        $this->value_boolean = null;

        // Set the appropriate field based on attribute type
        switch ($this->attribute->type) {
            case 'number':
                $this->value_number = (float) $value;
                break;
            case 'boolean':
                $this->value_boolean = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                break;
            default:
                $this->value_text = (string) $value;
                break;
        }
    }

    public function isSearchable()
    {
        return $this->attribute && $this->attribute->is_searchable;
    }

    public function isFilterable()
    {
        return $this->attribute && $this->attribute->is_filterable;
    }

    // Static methods
    public static function setForProduct($productId, $attributeId, $value)
    {
        $attributeValue = static::updateOrCreate(
            ['product_id' => $productId, 'attribute_id' => $attributeId],
            []
        );

        $attributeValue->load('attribute');
        $attributeValue->setValue($value);
        $attributeValue->save();

        return $attributeValue;
    }

    // Events
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($attributeValue) {
            // Validate value before saving
            if ($attributeValue->attribute && !$attributeValue->attribute->validateValue($attributeValue->value)) {
                throw new \Exception('Invalid value for attribute: ' . $attributeValue->attribute->name);
            }
        });
    }
}
