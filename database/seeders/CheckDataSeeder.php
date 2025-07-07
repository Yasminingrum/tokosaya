<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Product;

class CheckDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('=== DATA CHECK REPORT ===');

        // Check roles
        $roles = DB::table('roles')->get();
        $this->command->info("📋 Roles: {$roles->count()}");
        foreach ($roles as $role) {
            $userCount = User::where('role_id', $role->id)->count();
            $this->command->info("  - {$role->name}: {$userCount} users");
        }

        // Check users
        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();
        $this->command->info("👥 Users: {$totalUsers} total, {$activeUsers} active");

        // Check products
        $totalProducts = Product::count();
        $activeProducts = Product::where('status', 'active')->count();
        $this->command->info("📦 Products: {$totalProducts} total, {$activeProducts} active");

        // Check payment methods
        $paymentMethods = DB::table('payment_methods')->where('is_active', true)->count();
        $this->command->info("💳 Payment Methods: {$paymentMethods}");

        // Check shipping methods
        $shippingMethods = DB::table('shipping_methods')->where('is_active', true)->count();
        $this->command->info("🚚 Shipping Methods: {$shippingMethods}");

        // Check existing orders
        $orders = DB::table('orders')->count();
        $this->command->info("📋 Existing Orders: {$orders}");

        // Check existing reviews
        $reviews = DB::table('product_reviews')->count();
        $this->command->info("⭐ Existing Reviews: {$reviews}");

        $this->command->info('=== END REPORT ===');
    }
}
