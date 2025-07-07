<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Coupon;
use Carbon\Carbon;

class CouponsSeeder extends Seeder
{
    public function run()
    {
        $coupons = [
            [
                'code' => 'WELCOME10',
                'name' => 'Welcome Discount 10%',
                'description' => '10% discount for new customers',
                'type' => 'percentage',
                'value_cents' => 1000, // 10%
                'minimum_order_cents' => 100000, // Rp 100,000
                'maximum_discount_cents' => 50000, // Rp 50,000
                'usage_limit' => 1000,
                'usage_limit_per_customer' => 1,
                'is_active' => true,
                'is_public' => true,
                'applicable_to' => 'all',
                'starts_at' => Carbon::now()->subDays(30),
                'expires_at' => Carbon::now()->addDays(30)
            ],
            [
                'code' => 'FREESHIP',
                'name' => 'Free Shipping',
                'description' => 'Free shipping for all orders',
                'type' => 'free_shipping',
                'value_cents' => 0,
                'minimum_order_cents' => 200000, // Rp 200,000
                'maximum_discount_cents' => null,
                'usage_limit' => 500,
                'usage_limit_per_customer' => 2,
                'is_active' => true,
                'is_public' => true,
                'applicable_to' => 'all',
                'starts_at' => Carbon::now()->subDays(15),
                'expires_at' => Carbon::now()->addDays(15)
            ],
            [
                'code' => 'FLASH50',
                'name' => 'Flash Sale 50K',
                'description' => 'Rp 50,000 discount for all orders',
                'type' => 'fixed',
                'value_cents' => 50000, // Rp 50,000
                'minimum_order_cents' => 250000, // Rp 250,000
                'maximum_discount_cents' => 50000,
                'usage_limit' => 200,
                'usage_limit_per_customer' => 1,
                'is_active' => true,
                'is_public' => true,
                'applicable_to' => 'all',
                'starts_at' => Carbon::now(),
                'expires_at' => Carbon::now()->addDays(3)
            ],
            [
                'code' => 'VIP20',
                'name' => 'VIP Discount 20%',
                'description' => '20% discount for VIP customers',
                'type' => 'percentage',
                'value_cents' => 2000, // 20%
                'minimum_order_cents' => 0,
                'maximum_discount_cents' => 100000, // Rp 100,000
                'usage_limit' => null,
                'usage_limit_per_customer' => null,
                'is_active' => true,
                'is_public' => false,
                'applicable_to' => 'user',
                'applicable_ids' => json_encode([1, 2, 3, 4]), // Admin users
                'starts_at' => Carbon::now()->subDays(10),
                'expires_at' => Carbon::now()->addDays(365)
            ],
            [
                'code' => 'ELECTRONIC15',
                'name' => 'Electronics 15% Off',
                'description' => '15% discount for electronics category',
                'type' => 'percentage',
                'value_cents' => 1500, // 15%
                'minimum_order_cents' => 0,
                'maximum_discount_cents' => 75000, // Rp 75,000
                'usage_limit' => 300,
                'usage_limit_per_customer' => 3,
                'is_active' => true,
                'is_public' => true,
                'applicable_to' => 'category',
                'applicable_ids' => json_encode([1]), // Electronics category
                'starts_at' => Carbon::now()->subDays(5),
                'expires_at' => Carbon::now()->addDays(10)
            ]
        ];

        foreach ($coupons as $coupon) {
            Coupon::create($coupon);
        }
    }
}
