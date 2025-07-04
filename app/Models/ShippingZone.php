<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingZone extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'countries',
        'states',
        'cities',
        'postal_codes',
        'is_active',
    ];

    protected $casts = [
        'countries' => 'array',
        'states' => 'array',
        'cities' => 'array',
        'postal_codes' => 'array',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function rates()
    {
        return $this->hasMany(ShippingRate::class, 'zone_id');
    }

    public function activeRates()
    {
        return $this->hasMany(ShippingRate::class, 'zone_id')->where('is_active', true);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForCountry($query, $country)
    {
        return $query->where(function ($q) use ($country) {
            $q->whereJsonContains('countries', $country)
              ->orWhereJsonContains('countries', '*');
        });
    }

    public function scopeForState($query, $state)
    {
        return $query->where(function ($q) use ($state) {
            $q->whereNull('states')
              ->orWhereJsonContains('states', $state)
              ->orWhereJsonContains('states', '*');
        });
    }

    public function scopeForCity($query, $city)
    {
        return $query->where(function ($q) use ($city) {
            $q->whereNull('cities')
              ->orWhereJsonContains('cities', $city)
              ->orWhereJsonContains('cities', '*');
        });
    }

    public function scopeForLocation($query, $country, $state = null, $city = null, $postalCode = null)
    {
        return $query->active()
                    ->forCountry($country)
                    ->forState($state)
                    ->forCity($city)
                    ->when($postalCode, function ($q) use ($postalCode) {
                        $q->forPostalCode($postalCode);
                    });
    }

    public function scopeForPostalCode($query, $postalCode)
    {
        return $query->where(function ($q) use ($postalCode) {
            $q->whereNull('postal_codes')
              ->orWhereJsonContains('postal_codes', $postalCode)
              ->orWhere(function ($subQ) use ($postalCode) {
                  // Check for postal code ranges or patterns
                  $subQ->whereNotNull('postal_codes');
                  // Additional logic for postal code patterns could be added here
              });
        });
    }

    // Helper methods
    public function isActive()
    {
        return $this->is_active;
    }

    public function coversCountry($country)
    {
        return in_array('*', $this->countries ?? []) ||
               in_array($country, $this->countries ?? []);
    }

    public function coversState($state)
    {
        return empty($this->states) ||
               in_array('*', $this->states ?? []) ||
               in_array($state, $this->states ?? []);
    }

    public function coversCity($city)
    {
        return empty($this->cities) ||
               in_array('*', $this->cities ?? []) ||
               in_array($city, $this->cities ?? []);
    }

    public function coversPostalCode($postalCode)
    {
        if (empty($this->postal_codes)) {
            return true;
        }

        return in_array('*', $this->postal_codes ?? []) ||
               in_array($postalCode, $this->postal_codes ?? []);
    }

    public function coversLocation($country, $state = null, $city = null, $postalCode = null)
    {
        return $this->coversCountry($country) &&
               $this->coversState($state) &&
               $this->coversCity($city) &&
               $this->coversPostalCode($postalCode);
    }

    public function getCountriesListAttribute()
    {
        return collect($this->countries ?? [])->filter(function ($country) {
            return $country !== '*';
        });
    }

    public function getStatesListAttribute()
    {
        return collect($this->states ?? [])->filter(function ($state) {
            return $state !== '*';
        });
    }

    public function getCitiesListAttribute()
    {
        return collect($this->cities ?? [])->filter(function ($city) {
            return $city !== '*';
        });
    }

    public function isGlobal()
    {
        return in_array('*', $this->countries ?? []);
    }

    public function isCountrySpecific()
    {
        return !$this->isGlobal() && !empty($this->countries);
    }

    public function hasStateRestrictions()
    {
        return !empty($this->states) && !in_array('*', $this->states ?? []);
    }

    public function hasCityRestrictions()
    {
        return !empty($this->cities) && !in_array('*', $this->cities ?? []);
    }

    public function hasPostalCodeRestrictions()
    {
        return !empty($this->postal_codes) && !in_array('*', $this->postal_codes ?? []);
    }

    public function getCoverageDescriptionAttribute()
    {
        if ($this->isGlobal()) {
            return 'Global coverage';
        }

        $description = [];

        if ($this->isCountrySpecific()) {
            $countries = $this->countries_list;
            if ($countries->count() === 1) {
                $description[] = $countries->first();
            } else {
                $description[] = $countries->count() . ' countries';
            }
        }

        if ($this->hasStateRestrictions()) {
            $states = $this->states_list;
            if ($states->count() === 1) {
                $description[] = $states->first();
            } else {
                $description[] = $states->count() . ' states/provinces';
            }
        }

        if ($this->hasCityRestrictions()) {
            $cities = $this->cities_list;
            if ($cities->count() === 1) {
                $description[] = $cities->first();
            } else {
                $description[] = $cities->count() . ' cities';
            }
        }

        return implode(', ', $description) ?: 'No restrictions';
    }

    public function getRatesCountAttribute()
    {
        return $this->rates()->count();
    }

    public function getActiveRatesCountAttribute()
    {
        return $this->activeRates()->count();
    }

    public function hasRatesForMethod($methodId)
    {
        return $this->activeRates()->where('shipping_method_id', $methodId)->exists();
    }

    public function getRatesForMethod($methodId)
    {
        return $this->activeRates()->where('shipping_method_id', $methodId)->get();
    }

    // Static methods
    public static function findForLocation($country, $state = null, $city = null, $postalCode = null)
    {
        return static::active()
                    ->where(function ($query) use ($country, $state, $city, $postalCode) {
                        $query->whereJsonContains('countries', '*')
                              ->orWhere(function ($q) use ($country) {
                                  $q->whereJsonContains('countries', $country);
                              });
                    })
                    ->where(function ($query) use ($state) {
                        $query->whereNull('states')
                              ->orWhereJsonContains('states', '*')
                              ->orWhere(function ($q) use ($state) {
                                  if ($state) {
                                      $q->whereJsonContains('states', $state);
                                  }
                              });
                    })
                    ->where(function ($query) use ($city) {
                        $query->whereNull('cities')
                              ->orWhereJsonContains('cities', '*')
                              ->orWhere(function ($q) use ($city) {
                                  if ($city) {
                                      $q->whereJsonContains('cities', $city);
                                  }
                              });
                    })
                    ->where(function ($query) use ($postalCode) {
                        $query->whereNull('postal_codes')
                              ->orWhereJsonContains('postal_codes', '*')
                              ->orWhere(function ($q) use ($postalCode) {
                                  if ($postalCode) {
                                      $q->whereJsonContains('postal_codes', $postalCode);
                                  }
                              });
                    })
                    ->orderBy('name')
                    ->get();
    }

    public static function getBestZoneForLocation($country, $state = null, $city = null, $postalCode = null)
    {
        $zones = static::findForLocation($country, $state, $city, $postalCode);

        if ($zones->isEmpty()) {
            return null;
        }

        // Priority: Most specific zone first
        return $zones->sortBy(function ($zone) {
            $specificity = 0;

            if (!$zone->isGlobal()) $specificity += 1000;
            if ($zone->hasStateRestrictions()) $specificity += 100;
            if ($zone->hasCityRestrictions()) $specificity += 10;
            if ($zone->hasPostalCodeRestrictions()) $specificity += 1;

            return -$specificity; // Negative for descending order
        })->first();
    }

    // Events
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($zone) {
            // Ensure at least one location criteria is provided
            if (empty($zone->countries)) {
                $zone->countries = ['*']; // Default to global if no countries specified
            }
        });

        static::deleting(function ($zone) {
            // Check if zone has active rates
            if ($zone->activeRates()->exists()) {
                throw new \Exception('Cannot delete shipping zone with active rates');
            }
        });
    }
}
