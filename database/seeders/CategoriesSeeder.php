<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategoriesSeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'name' => 'Elektronik',
                'slug' => 'elektronik',
                'description' => 'Produk elektronik terbaru dan terbaik',
                'is_active' => true,
                'sort_order' => 1,
                'meta_title' => 'Elektronik Terbaik | TokoSaya',
                'meta_description' => 'Temukan berbagai produk elektronik terbaru dengan harga terbaik'
            ],
            [
                'name' => 'Fashion Pria',
                'slug' => 'fashion-pria',
                'description' => 'Koleksi fashion pria terkini',
                'is_active' => true,
                'sort_order' => 2,
                'meta_title' => 'Fashion Pria | TokoSaya',
                'meta_description' => 'Koleksi fashion pria terlengkap dengan harga terjangkau'
            ],
            [
                'name' => 'Fashion Wanita',
                'slug' => 'fashion-wanita',
                'description' => 'Koleksi fashion wanita terkini',
                'is_active' => true,
                'sort_order' => 3,
                'meta_title' => 'Fashion Wanita | TokoSaya',
                'meta_description' => 'Temukan gaya terkini dengan koleksi fashion wanita kami'
            ],
            [
                'name' => 'Kesehatan & Kecantikan',
                'slug' => 'kesehatan-kecantikan',
                'description' => 'Produk kesehatan dan kecantikan terbaik',
                'is_active' => true,
                'sort_order' => 4,
                'meta_title' => 'Produk Kesehatan & Kecantikan | TokoSaya',
                'meta_description' => 'Produk perawatan tubuh dan kecantikan dengan kualitas terbaik'
            ],
            [
                'name' => 'Rumah Tangga',
                'slug' => 'rumah-tangga',
                'description' => 'Kebutuhan rumah tangga sehari-hari',
                'is_active' => true,
                'sort_order' => 5,
                'meta_title' => 'Produk Rumah Tangga | TokoSaya',
                'meta_description' => 'Lengkapi kebutuhan rumah tangga Anda dengan produk terbaik'
            ]
        ];

        foreach ($categories as $category) {
            $createdCategory = Category::create($category);

            // Create 3-5 subcategories for each main category
            $subcategoryCount = rand(3, 5);
            for ($i = 1; $i <= $subcategoryCount; $i++) {
                $subcategoryName = fake()->unique()->word();
                Category::create([
                    'name' => $subcategoryName,
                    'slug' => \Illuminate\Support\Str::slug($subcategoryName),
                    'description' => fake()->sentence(),
                    'parent_id' => $createdCategory->id,
                    'is_active' => true,
                    'sort_order' => $i,
                    'meta_title' => $subcategoryName . ' | TokoSaya',
                    'meta_description' => 'Koleksi ' . $subcategoryName . ' terbaik'
                ]);
            }
        }
    }
}
