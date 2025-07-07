<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Product;
use App\Models\PaymentMethod;
use App\Models\ShippingMethod;
use Carbon\Carbon;

class OrdersSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Starting OrdersSeeder with Indonesian phone format...');

        // 1. Check users dengan debug info
        $this->command->info('Checking users...');
        $customerRole = DB::table('roles')->where('name', 'customer')->first();

        if (!$customerRole) {
            $this->command->error('❌ Customer role not found!');
            // Ambil semua users sebagai fallback
            $users = User::where('is_active', true)->get();
        } else {
            $users = User::where('role_id', $customerRole->id)->where('is_active', true)->get();
        }

        $this->command->info("Found {$users->count()} users");

        if ($users->count() === 0) {
            $this->command->error('❌ No users found! Cannot create orders.');
            return;
        }

        // 2. Check products dengan debug info
        $this->command->info('Checking products...');
        $products = Product::where('status', 'active')->get();
        $this->command->info("Found {$products->count()} active products");

        if ($products->count() === 0) {
            $this->command->error('❌ No active products found! Cannot create orders.');
            return;
        }

        // 3. Check payment methods (create if not exists)
        $this->command->info('Checking payment methods...');
        $paymentMethods = PaymentMethod::where('is_active', true)->get();

        if ($paymentMethods->count() === 0) {
            $this->command->warn('⚠️ No payment methods found. Creating default payment methods...');
            $this->createDefaultPaymentMethods();
            $paymentMethods = PaymentMethod::where('is_active', true)->get();
        }

        // 4. Check shipping methods (create if not exists)
        $this->command->info('Checking shipping methods...');
        $shippingMethods = ShippingMethod::where('is_active', true)->get();

        if ($shippingMethods->count() === 0) {
            $this->command->warn('⚠️ No shipping methods found. Creating default shipping methods...');
            $this->createDefaultShippingMethods();
            $shippingMethods = ShippingMethod::where('is_active', true)->get();
        }

        $this->command->info("✅ All dependencies checked. Creating orders...");

        // 5. Create orders with Indonesian phone format
        $orders = [];
        $orderStatuses = ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'];
        $paymentStatuses = ['pending', 'paid', 'failed', 'refunded'];

        for ($i = 0; $i < 50; $i++) { // Reduced number for testing
            try {
                $user = $users->random();
                $orderNumber = 'ORD-' . str_pad(1001 + $i, 6, '0', STR_PAD_LEFT);
                $status = fake()->randomElement($orderStatuses);
                $paymentStatus = $status === 'delivered' ? 'paid' : fake()->randomElement($paymentStatuses);

                // Generate realistic totals
                $subtotal = fake()->numberBetween(50000, 500000); // 50k - 500k IDR
                $tax = (int)($subtotal * 0.11); // 11% PPN
                $shipping = fake()->numberBetween(15000, 35000);
                $discount = fake()->optional(0.3)->numberBetween(5000, 50000) ?? 0;
                $total = $subtotal + $tax + $shipping - $discount;

                $createdAt = fake()->dateTimeBetween('-3 months', 'now');

                // Generate Indonesian phone numbers (max 15 characters)
                $shippingPhone = $this->generateIndonesianPhone();
                $billingPhone = $this->generateIndonesianPhone();

                $orders[] = [
                    'user_id' => $user->id,
                    'order_number' => $orderNumber,
                    'status' => $status,
                    'payment_status' => $paymentStatus,
                    'subtotal_cents' => $subtotal,
                    'tax_cents' => $tax,
                    'shipping_cents' => $shipping,
                    'discount_cents' => $discount,
                    'total_cents' => $total,

                    // Shipping information with Indonesian format
                    'shipping_name' => $user->first_name . ' ' . $user->last_name,
                    'shipping_phone' => $shippingPhone,
                    'shipping_address' => $this->generateIndonesianAddress(),
                    'shipping_city' => $this->getIndonesianCity(),
                    'shipping_state' => $this->getIndonesianProvince(),
                    'shipping_postal_code' => fake()->numerify('#####'),
                    'shipping_country' => 'ID',

                    // Billing information with Indonesian format
                    'billing_name' => $user->first_name . ' ' . $user->last_name,
                    'billing_phone' => $billingPhone,
                    'billing_address' => $this->generateIndonesianAddress(),
                    'billing_city' => $this->getIndonesianCity(),
                    'billing_state' => $this->getIndonesianProvince(),
                    'billing_postal_code' => fake()->numerify('#####'),
                    'billing_country' => 'ID',

                    'notes' => fake()->optional(0.3)->sentence(),
                    'coupon_code' => fake()->optional(0.2)->word(),
                    'tracking_number' => in_array($status, ['shipped', 'delivered']) ? 'TRK' . fake()->numerify('############') : null,
                    'shipping_method_id' => $shippingMethods->random()->id,
                    'payment_method_id' => $paymentMethods->random()->id,
                    'confirmed_at' => in_array($status, ['confirmed', 'processing', 'shipped', 'delivered']) ? $createdAt : null,
                    'shipped_at' => in_array($status, ['shipped', 'delivered']) ? Carbon::parse($createdAt)->addDays(1) : null,
                    'delivered_at' => $status === 'delivered' ? Carbon::parse($createdAt)->addDays(3) : null,
                    'cancelled_at' => $status === 'cancelled' ? Carbon::parse($createdAt)->addHours(2) : null,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ];

            } catch (\Exception $e) {
                $this->command->error("Error creating order {$i}: " . $e->getMessage());
                continue;
            }

            // Insert in smaller batches
            if (count($orders) >= 10) {
                try {
                    DB::table('orders')->insert($orders);
                    $this->command->info('✅ Inserted batch of ' . count($orders) . ' orders');
                    $orders = [];
                } catch (\Exception $e) {
                    $this->command->error('❌ Error inserting orders: ' . $e->getMessage());
                    break;
                }
            }
        }

        // Insert remaining orders
        if (!empty($orders)) {
            try {
                DB::table('orders')->insert($orders);
                $this->command->info('✅ Inserted final batch of ' . count($orders) . ' orders');
            } catch (\Exception $e) {
                $this->command->error('❌ Error inserting final batch: ' . $e->getMessage());
            }
        }

        $this->command->info('🎉 Orders seeding completed!');
    }

    /**
     * Generate Indonesian phone number (max 15 characters)
     * Format: +628xxxxxxxxx or 08xxxxxxxxx
     */
    private function generateIndonesianPhone(): string
    {
        $formats = [
            // Mobile numbers (08xx-xxxx-xxxx) - 13-14 chars max
            '08' . fake()->randomElement(['11', '12', '13', '21', '22', '51', '52', '53', '55', '56', '57', '58', '59', '77', '78', '81', '82', '83', '85', '86', '87', '88', '89']) . fake()->numerify('########'),

            // International format (+628xx-xxxx-xxxx) - 14-15 chars max
            '+628' . fake()->randomElement(['1', '2', '5', '7', '8']) . fake()->numerify('#######'),

            // Home numbers (021-xxxx-xxxx) - Jakarta
            '021' . fake()->numerify('########'),

            // Other major cities
            '022' . fake()->numerify('########'), // Bandung
            '031' . fake()->numerify('########'), // Surabaya
            '061' . fake()->numerify('########'), // Medan
        ];

        $phone = fake()->randomElement($formats);

        // Ensure it's not longer than 15 characters
        return substr($phone, 0, 15);
    }

    /**
     * Generate Indonesian address
     */
    private function generateIndonesianAddress(): string
    {
        $streetTypes = ['Jl.', 'Jalan', 'Gang', 'Komplek'];
        $streetNames = [
            'Sudirman', 'Thamrin', 'Gatot Subroto', 'Ahmad Yani', 'Diponegoro',
            'Pahlawan', 'Merdeka', 'Kebon Jeruk', 'Kebayoran', 'Senopati',
            'Wijaya', 'Raya', 'Utama', 'Indah', 'Asri'
        ];

        $streetType = fake()->randomElement($streetTypes);
        $streetName = fake()->randomElement($streetNames);
        $number = fake()->numberBetween(1, 999);
        $rt = fake()->numberBetween(1, 20);
        $rw = fake()->numberBetween(1, 10);

        return "{$streetType} {$streetName} No. {$number}, RT.{$rt}/RW.{$rw}";
    }

    /**
     * Get Indonesian city names
     */
    private function getIndonesianCity(): string
    {
        $cities = [
            'Jakarta', 'Surabaya', 'Bandung', 'Medan', 'Semarang',
            'Makassar', 'Palembang', 'Tangerang', 'Depok', 'Bekasi',
            'Yogyakarta', 'Solo', 'Malang', 'Bogor', 'Batam',
            'Denpasar', 'Balikpapan', 'Banjarmasin', 'Pontianak', 'Manado'
        ];

        return fake()->randomElement($cities);
    }

    /**
     * Get Indonesian province names
     */
    private function getIndonesianProvince(): string
    {
        $provinces = [
            'DKI Jakarta', 'Jawa Barat', 'Jawa Tengah', 'Jawa Timur',
            'Sumatera Utara', 'Sumatera Selatan', 'Sulawesi Selatan',
            'Kalimantan Timur', 'Bali', 'Yogyakarta', 'Banten',
            'Kepulauan Riau', 'Kalimantan Selatan', 'Sulawesi Utara',
            'Lampung', 'Riau', 'Sumatera Barat', 'Aceh'
        ];

        return fake()->randomElement($provinces);
    }

    /**
     * Create default payment methods if none exist
     */
    private function createDefaultPaymentMethods(): void
    {
        $paymentMethods = [
            [
                'name' => 'Transfer Bank BCA',
                'code' => 'bca_transfer',
                'description' => 'Transfer ke rekening Bank BCA',
                'is_active' => true,
                'sort_order' => 1,
                'fee_type' => 'fixed',
                'fee_amount_cents' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Transfer Bank Mandiri',
                'code' => 'mandiri_transfer',
                'description' => 'Transfer ke rekening Bank Mandiri',
                'is_active' => true,
                'sort_order' => 2,
                'fee_type' => 'fixed',
                'fee_amount_cents' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'OVO',
                'code' => 'ovo',
                'description' => 'Pembayaran melalui OVO',
                'is_active' => true,
                'sort_order' => 3,
                'fee_type' => 'fixed',
                'fee_amount_cents' => 2500,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'GoPay',
                'code' => 'gopay',
                'description' => 'Pembayaran melalui GoPay',
                'is_active' => true,
                'sort_order' => 4,
                'fee_type' => 'fixed',
                'fee_amount_cents' => 2500,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'COD (Cash on Delivery)',
                'code' => 'cod',
                'description' => 'Bayar di tempat saat barang diterima',
                'is_active' => true,
                'sort_order' => 5,
                'fee_type' => 'fixed',
                'fee_amount_cents' => 5000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('payment_methods')->insert($paymentMethods);
        $this->command->info('✅ Created default payment methods');
    }

    /**
     * Create default shipping methods if none exist
     */
    private function createDefaultShippingMethods(): void
    {
        $shippingMethods = [
            [
                'name' => 'JNE Regular',
                'code' => 'jne_reg',
                'description' => 'JNE Reguler 2-3 hari',
                'is_active' => true,
                'sort_order' => 1,
                'estimated_min_days' => 2,
                'estimated_max_days' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'JNE YES',
                'code' => 'jne_yes',
                'description' => 'JNE YES 1-2 hari',
                'is_active' => true,
                'sort_order' => 2,
                'estimated_min_days' => 1,
                'estimated_max_days' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'J&T Express',
                'code' => 'jnt_express',
                'description' => 'J&T Express 2-4 hari',
                'is_active' => true,
                'sort_order' => 3,
                'estimated_min_days' => 2,
                'estimated_max_days' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'SiCepat Halu',
                'code' => 'sicepat_halu',
                'description' => 'SiCepat Halu 1-2 hari',
                'is_active' => true,
                'sort_order' => 4,
                'estimated_min_days' => 1,
                'estimated_max_days' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Pos Indonesia',
                'code' => 'pos_indonesia',
                'description' => 'Pos Indonesia 3-5 hari',
                'is_active' => true,
                'sort_order' => 5,
                'estimated_min_days' => 3,
                'estimated_max_days' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('shipping_methods')->insert($shippingMethods);
        $this->command->info('✅ Created default shipping methods');
    }
}
