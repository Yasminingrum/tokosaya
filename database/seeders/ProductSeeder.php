<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Support\Str;

class ProductsTableSeeder extends Seeder
{
    public function run()
    {
        $categories = Category::whereNull('parent_id')->get();
        $brands = Brand::all();

        // Create 100 products
        for ($i = 1; $i <= 100; $i++) {
            $category = $categories->random();
            $brand = $brands->random();
            $name = fake()->words(3, true);

            $product = Product::create([
                'category_id' => $category->id,
                'brand_id' => $brand->id,
                'name' => $name,
                'slug' => Str::slug($name) . '-' . Str::random(6),
                'description' => fake()->paragraphs(3, true),
                'short_description' => fake()->sentence(),
                'sku' => 'SKU-' . Str::upper(Str::random(8)),
                'barcode' => 'BC-' . rand(1000000000, 9999999999),
                'price_cents' => rand(50000, 5000000),
                'compare_price_cents' => rand(60000, 5500000),
                'cost_price_cents' => rand(40000, 4000000),
                'stock_quantity' => rand(0, 500),
                'min_stock_level' => 5,
                'max_stock_level' => 1000,
                'weight_grams' => rand(50, 5000),
                'length_mm' => rand(100, 500),
                'width_mm' => rand(100, 500),
                'height_mm' => rand(100, 500),
                'status' => fake()->randomElement(['draft', 'active', 'active', 'active', 'inactive']),
                'featured' => rand(0, 1),
                'digital' => rand(0, 1),
                'track_stock' => true,
                'allow_backorder' => rand(0, 1),
                'meta_title' => $name . ' | TokoSaya',
                'meta_description' => 'Beli ' . $name . ' dengan harga terbaik di TokoSaya',
                'created_by' => rand(1, 4) // Random admin/manager user
            ]);

            // Update category product count
            $category->increment('product_count');

            // Create product variants for 30% of products
            if (rand(1, 100) <= 30) {
                $this->createVariants($product);
            }
        }
    }

    protected function createVariants($product)
    {
        $variantTypes = ['Warna', 'Ukuran', 'Kapasitas'];
        $variantType = $variantTypes[array_rand($variantTypes)];

        $variants = [
            [
                'variant_name' => $variantType,
                'variant_value' => 'Option 1',
                'price_adjustment_cents' => rand(-50000, 50000),
                'stock_quantity' => rand(0, 100),
                'sku' => $product->sku . '-V1',
                'barcode' => $product->barcode . '-V1',
                'is_active' => true
            ],
            [
                'variant_name' => $variantType,
                'variant_value' => 'Option 2',
                'price_adjustment_cents' => rand(-50000, 50000),
                'stock_quantity' => rand(0, 100),
                'sku' => $product->sku . '-V2',
                'barcode' => $product->barcode . '-V2',
                'is_active' => true
            ],
            [
                'variant_name' => $variantType,
                'variant_value' => 'Option 3',
                'price_adjustment_cents' => rand(-50000, 50000),
                'stock_quantity' => rand(0, 100),
                'sku' => $product->sku . '-V3',
                'barcode' => $product->barcode . '-V3',
                'is_active' => true
            ]
        ];

        foreach ($variants as $variant) {
            $product->variants()->create($variant);
        }
    }
}
