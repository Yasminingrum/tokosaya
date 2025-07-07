<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ShippingMethod;
use App\Models\ShippingZone;
use App\Models\ShippingRate;

class ShippingRatesSeeder extends Seeder
{
    public function run()
    {
        $methods = ShippingMethod::all();
        $zones = ShippingZone::all();

        foreach ($methods as $method) {
            foreach ($zones as $zone) {
                // Create base rate
                ShippingRate::create([
                    'shipping_method_id' => $method->id,
                    'zone_id' => $zone->id,
                    'min_weight_grams' => 0,
                    'max_weight_grams' => 1000,
                    'rate_cents' => $this->getBaseRate($method->code, $zone->name),
                    'free_shipping_threshold_cents' => $method->code === 'gosend_same_day' ? 0 : 25000000, // Rp 250,000
                    'is_active' => true
                ]);

                // Create additional weight tiers for some methods
                if (in_array($method->code, ['jne_reg', 'jne_oke', 'jt_reg'])) {
                    ShippingRate::create([
                        'shipping_method_id' => $method->id,
                        'zone_id' => $zone->id,
                        'min_weight_grams' => 1001,
                        'max_weight_grams' => 5000,
                        'rate_cents' => $this->getBaseRate($method->code, $zone->name) + 10000,
                        'free_shipping_threshold_cents' => 25000000,
                        'is_active' => true
                    ]);

                    ShippingRate::create([
                        'shipping_method_id' => $method->id,
                        'zone_id' => $zone->id,
                        'min_weight_grams' => 5001,
                        'max_weight_grams' => 10000,
                        'rate_cents' => $this->getBaseRate($method->code, $zone->name) + 20000,
                        'free_shipping_threshold_cents' => 25000000,
                        'is_active' => true
                    ]);
                }
            }
        }
    }

    protected function getBaseRate($methodCode, $zoneName)
    {
        $baseRates = [
            'jne_reg' => ['Jabodetabek' => 15000, 'Jawa' => 20000, 'Sumatera' => 30000, 'Kalimantan' => 35000, 'Indonesia Timur' => 40000],
            'jne_oke' => ['Jabodetabek' => 10000, 'Jawa' => 15000, 'Sumatera' => 25000, 'Kalimantan' => 30000, 'Indonesia Timur' => 35000],
            'jne_yes' => ['Jabodetabek' => 25000, 'Jawa' => 30000, 'Sumatera' => 40000, 'Kalimantan' => 45000, 'Indonesia Timur' => 50000],
            'jt_reg' => ['Jabodetabek' => 14000, 'Jawa' => 18000, 'Sumatera' => 28000, 'Kalimantan' => 33000, 'Indonesia Timur' => 38000],
            'sicepat_halu' => ['Jabodetabek' => 20000, 'Jawa' => 25000, 'Sumatera' => 35000, 'Kalimantan' => 40000, 'Indonesia Timur' => 45000],
            'gosend_same_day' => ['Jabodetabek' => 30000, 'Jawa' => 40000, 'Sumatera' => 0, 'Kalimantan' => 0, 'Indonesia Timur' => 0]
        ];

        return $baseRates[$methodCode][$zoneName] * 100; // Convert to cents
    }
}
