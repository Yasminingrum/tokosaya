<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ShippingZone;

class ShippingZonesTableSeeder extends Seeder
{
    public function run()
    {
        $zones = [
            [
                'name' => 'Jabodetabek',
                'countries' => json_encode(['ID']),
                'states' => json_encode(['DKI Jakarta', 'Jawa Barat', 'Banten']),
                'cities' => json_encode(['Jakarta', 'Bogor', 'Depok', 'Tangerang', 'Bekasi']),
                'is_active' => true
            ],
            [
                'name' => 'Jawa',
                'countries' => json_encode(['ID']),
                'states' => json_encode(['Jawa Barat', 'Jawa Tengah', 'Jawa Timur', 'DI Yogyakarta']),
                'cities' => json_encode(['*']),
                'is_active' => true
            ],
            [
                'name' => 'Sumatera',
                'countries' => json_encode(['ID']),
                'states' => json_encode(['Sumatera Utara', 'Sumatera Barat', 'Sumatera Selatan', 'Riau', 'Kepulauan Riau', 'Jambi', 'Bengkulu', 'Lampung', 'Aceh']),
                'cities' => json_encode(['*']),
                'is_active' => true
            ],
            [
                'name' => 'Kalimantan',
                'countries' => json_encode(['ID']),
                'states' => json_encode(['Kalimantan Barat', 'Kalimantan Tengah', 'Kalimantan Selatan', 'Kalimantan Timur', 'Kalimantan Utara']),
                'cities' => json_encode(['*']),
                'is_active' => true
            ],
            [
                'name' => 'Indonesia Timur',
                'countries' => json_encode(['ID']),
                'states' => json_encode(['Bali', 'Nusa Tenggara Barat', 'Nusa Tenggara Timur', 'Sulawesi Utara', 'Sulawesi Tengah', 'Sulawesi Selatan', 'Sulawesi Tenggara', 'Gorontalo', 'Maluku', 'Maluku Utara', 'Papua', 'Papua Barat']),
                'cities' => json_encode(['*']),
                'is_active' => true
            ]
        ];

        foreach ($zones as $zone) {
            ShippingZone::create($zone);
        }
    }
}
