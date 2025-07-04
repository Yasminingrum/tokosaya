<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'label',
        'recipient_name',
        'phone',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'postal_code',
        'country',
        'latitude',
        'longitude',
        'is_default',
    ];

    protected $casts = [
        'latitude' => 'decimal:6',
        'longitude' => 'decimal:6',
        'is_default' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeByCity($query, $city)
    {
        return $query->where('city', $city);
    }

    public function scopeByState($query, $state)
    {
        return $query->where('state', $state);
    }

    // Helper methods
    public function getFullAddressAttribute()
    {
        $address = $this->address_line1;

        if ($this->address_line2) {
            $address .= ', ' . $this->address_line2;
        }

        $address .= ', ' . $this->city;
        $address .= ', ' . $this->state;
        $address .= ' ' . $this->postal_code;

        if ($this->country !== 'ID') {
            $address .= ', ' . $this->country;
        }

        return $address;
    }

    public function hasCoordinates()
    {
        return !is_null($this->latitude) && !is_null($this->longitude);
    }

    public function distanceTo($latitude, $longitude)
    {
        if (!$this->hasCoordinates()) {
            return null;
        }

        $earthRadius = 6371; // kilometers

        $latDelta = deg2rad($latitude - $this->latitude);
        $lonDelta = deg2rad($longitude - $this->longitude);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($this->latitude)) * cos(deg2rad($latitude)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    // Events
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($address) {
            // If this is set as default, unset other default addresses for this user
            if ($address->is_default) {
                static::where('user_id', $address->user_id)
                    ->where('is_default', true)
                    ->update(['is_default' => false]);
            }

            // If this is the first address, make it default
            $existingCount = static::where('user_id', $address->user_id)->count();
            if ($existingCount === 0) {
                $address->is_default = true;
            }
        });

        static::updating(function ($address) {
            if ($address->is_default && $address->isDirty('is_default')) {
                static::where('user_id', $address->user_id)
                    ->where('id', '!=', $address->id)
                    ->where('is_default', true)
                    ->update(['is_default' => false]);
            }
        });
    }
}
