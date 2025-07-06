<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ShippingMethod;

class ShippingMethodsTableSeeder extends Seeder
{
    public function run()
    {
        $methods = [
            [
                'name' => 'JNE Reguler',
                'code' => 'jne_reg',
                'description' => 'Pengiriman reguler melalui JNE',
                'logo' => 'shipping/jne.png',
                'is_active' => true,
                'sort_order' => 1,
                'estimated_min_days' => 2,
                'estimated_max_days' => 5
            ],
            [
                'name' => 'JNE OKE',
                'code' => 'jne_oke',
                'description' => 'Pengiriman ekonomis melalui JNE',
                'logo' => 'shipping/jne.png',
                'is_active' => true,
                'sort_order' => 2,
                'estimated_min_days' => 3,
                'estimated_max_days' => 7
            ],
            [
                'name' => 'JNE YES',
                'code' => 'jne_yes',
                'description' => 'Pengiriman cepat melalui JNE',
                'logo' => 'shipping/jne.png',
                'is_active' => true,
                'sort_order' => 3,
                'estimated_min_days' => 1,
                'estimated_max_days' => 2
            ],
            [
                'name' => 'J&T Reguler',
                'code' => 'jt_reg',
                'description' => 'Pengiriman reguler melalui J&T',
                'logo' => 'shipping/jt.png',
                'is_active' => true,
                'sort_order' => 4,
                'estimated_min_days' => 2,
                'estimated_max_days' => 5
            ],
            [
                'name' => 'SiCepat Halu',
                'code' => 'sicepat_halu',
                'description' => 'Pengiriman cepat melalui SiCepat',
                'logo' => 'shipping/sicepat.png',
                'is_active' => true,
                'sort_order' => 5,
                'estimated_min_days' => 1,
                'estimated_max_days' => 3
            ],
            [
                'name' => 'GoSend Same Day',
                'code' => 'gosend_same_day',
                'description' => 'Pengiriman hari yang sama melalui GoSend',
                'logo' => 'shipping/gosend.png',
                'is_active' => true,
                'sort_order' => 6,
                'estimated_min_days' => 0,
                'estimated_max_days' => 1
            ]
        ];

        foreach ($methods as $method) {
            ShippingMethod::create($method);
        }
    }
}
