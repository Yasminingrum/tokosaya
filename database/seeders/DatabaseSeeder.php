<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            RolesTableSeeder::class,
            UsersTableSeeder::class,
            CustomerAddressesTableSeeder::class,
            CategoriesTableSeeder::class,
            BrandsTableSeeder::class,
            ProductAttributesTableSeeder::class,
            ProductsTableSeeder::class,
            ProductImagesTableSeeder::class,
            ProductAttributeValuesTableSeeder::class,
            PaymentMethodsTableSeeder::class,
            ShippingMethodsTableSeeder::class,
            ShippingZonesTableSeeder::class,
            ShippingRatesTableSeeder::class,
            CouponsTableSeeder::class,
            OrdersTableSeeder::class,
            ProductReviewsTableSeeder::class,
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
