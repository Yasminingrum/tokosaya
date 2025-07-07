<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CustomerAddress;
use App\Models\User;

class CustomerAddressesSeeder extends Seeder
{
    public function run()
    {
        $users = User::where('role_id', 4)->get(); // Get all customers

        foreach ($users as $user) {
            // Create 1-3 addresses per customer
            $addressCount = rand(1, 3);

            for ($i = 0; $i < $addressCount; $i++) {
                CustomerAddress::create([
                    'user_id' => $user->id,
                    'label' => $i === 0 ? 'Rumah' : ($i === 1 ? 'Kantor' : 'Alamat Lain'),
                    'recipient_name' => $user->first_name . ' ' . $user->last_name,
                    'phone' => $user->phone,
                    'address_line1' => 'Jl. ' . fake()->streetName() . ' No. ' . rand(1, 100),
                    'address_line2' => 'RT ' . rand(1, 10) . '/RW ' . rand(1, 10),
                    'city' => fake()->randomElement(['Jakarta', 'Bandung', 'Surabaya', 'Medan', 'Makassar']),
                    'state' => fake()->state(),
                    'postal_code' => rand(10000, 99999),
                    'country' => 'ID',
                    'latitude' => fake()->latitude(-6, -2),
                    'longitude' => fake()->longitude(95, 141),
                    'is_default' => $i === 0
                ]);
            }
        }
    }
}
