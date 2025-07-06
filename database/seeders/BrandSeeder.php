<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Brand;

class BrandsTableSeeder extends Seeder
{
    public function run()
    {
        $brands = [
            [
                'name' => 'Samsung',
                'slug' => 'samsung',
                'description' => 'Produsen elektronik global terkemuka',
                'logo' => 'brands/samsung.png',
                'website' => 'https://www.samsung.com',
                'is_active' => true
            ],
            [
                'name' => 'Apple',
                'slug' => 'apple',
                'description' => 'Perusahaan teknologi inovatif',
                'logo' => 'brands/apple.png',
                'website' => 'https://www.apple.com',
                'is_active' => true
            ],
            [
                'name' => 'Nike',
                'slug' => 'nike',
                'description' => 'Merek olahraga terkemuka dunia',
                'logo' => 'brands/nike.png',
                'website' => 'https://www.nike.com',
                'is_active' => true
            ],
            [
                'name' => 'Uniqlo',
                'slug' => 'uniqlo',
                'description' => 'Merek fashion Jepang yang nyaman',
                'logo' => 'brands/uniqlo.png',
                'website' => 'https://www.uniqlo.com',
                'is_active' => true
            ],
            [
                'name' => 'L\'Oreal',
                'slug' => 'loreal',
                'description' => 'Produk kecantikan dan perawatan kulit',
                'logo' => 'brands/loreal.png',
                'website' => 'https://www.loreal.com',
                'is_active' => true
            ],
            [
                'name' => 'Philips',
                'slug' => 'philips',
                'description' => 'Perangkat elektronik rumah tangga',
                'logo' => 'brands/philips.png',
                'website' => 'https://www.philips.com',
                'is_active' => true
            ],
            [
                'name' => 'Tupperware',
                'slug' => 'tupperware',
                'description' => 'Produk penyimpanan makanan berkualitas',
                'logo' => 'brands/tupperware.png',
                'website' => 'https://www.tupperware.com',
                'is_active' => true
            ],
            [
                'name' => 'Adidas',
                'slug' => 'adidas',
                'description' => 'Merek olahraga dan gaya hidup',
                'logo' => 'brands/adidas.png',
                'website' => 'https://www.adidas.com',
                'is_active' => true
            ],
            [
                'name' => 'Ponds',
                'slug' => 'ponds',
                'description' => 'Produk perawatan kulit wanita',
                'logo' => 'brands/ponds.png',
                'website' => 'https://www.ponds.com',
                'is_active' => true
            ],
            [
                'name' => 'Xiaomi',
                'slug' => 'xiaomi',
                'description' => 'Perangkat elektronik dengan harga terjangkau',
                'logo' => 'brands/xiaomi.png',
                'website' => 'https://www.mi.com',
                'is_active' => true
            ]
        ];

        foreach ($brands as $brand) {
            Brand::create($brand);
        }
    }
}
