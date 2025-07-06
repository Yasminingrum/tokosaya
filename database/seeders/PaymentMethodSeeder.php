<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;

class PaymentMethodsTableSeeder extends Seeder
{
    public function run()
    {
        $methods = [
            [
                'name' => 'Transfer Bank',
                'code' => 'bank_transfer',
                'description' => 'Pembayaran melalui transfer bank',
                'logo' => 'payment/bank_transfer.png',
                'is_active' => true,
                'sort_order' => 1,
                'gateway_config' => json_encode([
                    'banks' => ['BCA', 'Mandiri', 'BNI', 'BRI']
                ]),
                'fee_type' => 'fixed',
                'fee_amount_cents' => 0,
                'min_amount_cents' => 0,
                'max_amount_cents' => 0
            ],
            [
                'name' => 'Kartu Kredit',
                'code' => 'credit_card',
                'description' => 'Pembayaran dengan kartu kredit',
                'logo' => 'payment/credit_card.png',
                'is_active' => true,
                'sort_order' => 2,
                'gateway_config' => json_encode([
                    'cards' => ['VISA', 'MasterCard', 'JCB']
                ]),
                'fee_type' => 'percentage',
                'fee_amount_cents' => 250, // 2.5%
                'min_amount_cents' => 10000,
                'max_amount_cents' => 100000000
            ],
            [
                'name' => 'OVO',
                'code' => 'ovo',
                'description' => 'Pembayaran melalui OVO',
                'logo' => 'payment/ovo.png',
                'is_active' => true,
                'sort_order' => 3,
                'gateway_config' => json_encode([
                    'api_key' => 'demo_ovo_key'
                ]),
                'fee_type' => 'percentage',
                'fee_amount_cents' => 150, // 1.5%
                'min_amount_cents' => 10000,
                'max_amount_cents' => 10000000
            ],
            [
                'name' => 'Gopay',
                'code' => 'gopay',
                'description' => 'Pembayaran melalui Gopay',
                'logo' => 'payment/gopay.png',
                'is_active' => true,
                'sort_order' => 4,
                'gateway_config' => json_encode([
                    'api_key' => 'demo_gopay_key'
                ]),
                'fee_type' => 'percentage',
                'fee_amount_cents' => 150, // 1.5%
                'min_amount_cents' => 10000,
                'max_amount_cents' => 10000000
            ],
            [
                'name' => 'COD (Bayar di Tempat)',
                'code' => 'cod',
                'description' => 'Bayar ketika barang diterima',
                'logo' => 'payment/cod.png',
                'is_active' => true,
                'sort_order' => 5,
                'gateway_config' => json_encode([
                    'max_amount' => 5000000
                ]),
                'fee_type' => 'fixed',
                'fee_amount_cents' => 5000,
                'min_amount_cents' => 0,
                'max_amount_cents' => 5000000
            ]
        ];

        foreach ($methods as $method) {
            PaymentMethod::create($method);
        }
    }
}
