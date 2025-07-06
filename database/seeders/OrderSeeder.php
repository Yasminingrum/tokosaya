<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\User;
use App\Models\CustomerAddress;
use App\Models\PaymentMethod;
use App\Models\ShippingMethod;
use Carbon\Carbon;

class OrdersTableSeeder extends Seeder
{
    public function run()
    {
        $customers = User::where('role_id', 4)->get(); // Get all customers
        $paymentMethods = PaymentMethod::all();
        $shippingMethods = ShippingMethod::all();

        // Create 200 orders
        for ($i = 0; $i < 200; $i++) {
            $customer = $customers->random();
            $address = $customer->addresses->random();
            $paymentMethod = $paymentMethods->random();
            $shippingMethod = $shippingMethods->random();

            $orderDate = Carbon::now()->subDays(rand(0, 90));
            $status = $this->getRandomOrderStatus($orderDate);

            $subtotal = rand(100000, 5000000); // Rp 100,000 - 5,000,000
            $tax = (int)($subtotal * 0.11); // 11% PPN
            $shippingCost = rand(10000, 50000); // Rp 10,000 - 50,000
            $discount = rand(1, 100) <= 30 ? rand(10000, 100000) : 0; // 30% chance of discount
            $total = $subtotal + $tax + $shippingCost - $discount;

            $order = Order::create([
                'user_id' => $customer->id,
                'order_number' => 'ORD-' . Carbon::now()->format('Ymd') . str_pad($i + 1, 5, '0', STR_PAD_LEFT),
                'status' => $status,
                'payment_status' => $this->getPaymentStatus($status),
                'subtotal_cents' => $subtotal,
                'tax_cents' => $tax,
                'shipping_cents' => $shippingCost,
                'discount_cents' => $discount,
                'total_cents' => $total,
                'shipping_name' => $address->recipient_name,
                'shipping_phone' => $address->phone,
                'shipping_address' => $address->address_line1 . ', ' . $address->city . ', ' . $address->state,
                'shipping_city' => $address->city,
                'shipping_state' => $address->state,
                'shipping_postal_code' => $address->postal_code,
                'shipping_country' => $address->country,
                'billing_name' => $address->recipient_name,
                'billing_phone' => $address->phone,
                'billing_address' => $address->address_line1 . ', ' . $address->city . ', ' . $address->state,
                'billing_city' => $address->city,
                'billing_state' => $address->state,
                'billing_postal_code' => $address->postal_code,
                'billing_country' => $address->country,
                'payment_method_id' => $paymentMethod->id,
                'shipping_method_id' => $shippingMethod->id,
                'confirmed_at' => $status !== 'pending' ? $orderDate->addMinutes(rand(5, 60)) : null,
                'shipped_at' => in_array($status, ['shipped', 'delivered']) ? $orderDate->addDays(rand(1, 3)) : null,
                'delivered_at' => $status === 'delivered' ? $orderDate->addDays(rand(2, 7)) : null,
                'created_at' => $orderDate,
                'updated_at' => $orderDate->addDays(rand(0, 7))
            ]);

            // Create order items
            $this->createOrderItems($order);

            // Create payment if not pending
            if ($order->payment_status !== 'pending') {
                $this->createPayment($order);
            }
        }
    }

    protected function getRandomOrderStatus($orderDate)
    {
        $daysAgo = Carbon::now()->diffInDays($orderDate);
        $random = rand(1, 100);

        if ($daysAgo > 30) {
            // Older orders are more likely to be completed
            if ($random <= 70) return 'delivered';
            if ($random <= 85) return 'cancelled';
            if ($random <= 95) return 'shipped';
            return 'processing';
        } else {
            // Recent orders have more varied statuses
            if ($random <= 40) return 'delivered';
            if ($random <= 60) return 'shipped';
            if ($random <= 75) return 'processing';
            if ($random <= 90) return 'confirmed';
            return 'pending';
        }
    }

    protected function getPaymentStatus($orderStatus)
    {
        switch ($orderStatus) {
            case 'delivered':
                return rand(1, 100) <= 90 ? 'paid' : 'partial';
            case 'shipped':
            case 'processing':
                return rand(1, 100) <= 80 ? 'paid' : 'pending';
            case 'confirmed':
                return rand(1, 100) <= 50 ? 'paid' : 'pending';
            case 'cancelled':
                return rand(1, 100) <= 30 ? 'refunded' : 'failed';
            default: // pending
                return 'pending';
        }
    }

    protected function createOrderItems($order)
    {
        $products = \App\Models\Product::inRandomOrder()->limit(rand(1, 5))->get();

        foreach ($products as $product) {
            $quantity = rand(1, 3);
            $unitPrice = $product->price_cents;
            $totalPrice = $unitPrice * $quantity;

            $order->items()->create([
                'product_id' => $product->id,
                'variant_id' => $product->variants->count() > 0 ? $product->variants->random()->id : null,
                'product_name' => $product->name,
                'product_sku' => $product->sku,
                'variant_name' => $product->variants->count() > 0 ? $product->variants->random()->variant_value : null,
                'quantity' => $quantity,
                'unit_price_cents' => $unitPrice,
                'total_price_cents' => $totalPrice,
                'cost_price_cents' => $product->cost_price_cents,
                'created_at' => $order->created_at
            ]);

            // Update product sales data if order is delivered
            if ($order->status === 'delivered') {
                $product->increment('sale_count', $quantity);
                $product->increment('revenue_cents', $totalPrice);
                $product->last_sold_at = $order->delivered_at;
                $product->save();
            }
        }
    }

    protected function createPayment($order)
    {
        $paymentStatus = $order->payment_status;
        $paidAt = null;
        $expiresAt = null;

        if ($paymentStatus === 'paid') {
            $paidAt = $order->confirmed_at ?? $order->created_at->addMinutes(rand(5, 60));
        } elseif ($paymentStatus === 'pending') {
            $expiresAt = $order->created_at->addHours(24);
        }

        $order->payments()->create([
            'payment_method_id' => $order->payment_method_id,
            'amount_cents' => $order->total_cents,
            'fee_cents' => $this->calculatePaymentFee($order),
            'status' => $paymentStatus,
            'transaction_id' => 'TRX' . strtoupper(Str::random(10)),
            'reference_number' => 'REF' . strtoupper(Str::random(8)),
            'paid_at' => $paidAt,
            'expires_at' => $expiresAt,
            'created_at' => $order->created_at,
            'updated_at' => $paidAt ?? $order->created_at
        ]);
    }

    protected function calculatePaymentFee($order)
    {
        $paymentMethod = $order->paymentMethod;

        if ($paymentMethod->fee_type === 'fixed') {
            return $paymentMethod->fee_amount_cents;
        } elseif ($paymentMethod->fee_type === 'percentage') {
            return (int)($order->total_cents * ($paymentMethod->fee_amount_cents / 10000));
        }

        return 0;
    }
}
