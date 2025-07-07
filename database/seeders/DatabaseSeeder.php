<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            RolesSeeder::class,
            UsersSeeder::class,
            CustomerAddressesSeeder::class,
            CategoriesSeeder::class,
            BrandsSeeder::class,
            ProductAttributesSeeder::class,
            ProductsSeeder::class,
            ProductImagesSeeder::class,
            ProductAttributeValuesSeeder::class,
            PaymentMethodsSeeder::class,
            ShippingMethodsSeeder::class,
            ShippingZonesSeeder::class,
            ShippingRatesSeeder::class,
            CouponsSeeder::class,
            OrdersSeeder::class,
            ProductReviewsSeeder::class,
        ]);

        // Update category product counts
        $this->updateCategoryProductCounts();
    }

    protected function updateCategoryProductCounts()
    {
        $categories = \App\Models\Category::all();

        foreach ($categories as $category) {
            $productCount = \App\Models\Product::where('category_id', $category->id)
                ->where('status', 'active')
                ->count();

            $category->update(['product_count' => $productCount]);
        }
    }
}
