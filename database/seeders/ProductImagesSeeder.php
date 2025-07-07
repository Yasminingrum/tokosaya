<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductImage;

class ProductImagesSeeder extends Seeder
{
    public function run()
    {
        $products = Product::all();

        foreach ($products as $product) {
            // Create 1-5 images per product
            $imageCount = rand(1, 5);

            for ($i = 1; $i <= $imageCount; $i++) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_url' => 'products/' . $product->id . '/image-' . $i . '.jpg',
                    'alt_text' => $product->name . ' - Gambar ' . $i,
                    'sort_order' => $i,
                    'is_primary' => $i === 1,
                    'width' => 800,
                    'height' => 800,
                    'file_size' => rand(500, 2000) * 1024 // 500KB - 2MB
                ]);
            }
        }
    }
}
