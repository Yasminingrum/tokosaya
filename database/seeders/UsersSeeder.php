<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Pastikan role sudah ada atau buat default jika belum
        $superAdminRole = Role::where('name', 'superadmin')->first();
        $adminRole = Role::where('name', 'admin')->first();
        $customerRole = Role::where('name', 'customer')->first();

        if (!$superAdminRole || !$adminRole || !$customerRole) {
            $this->command->error('Roles not found! Please run RolesSeeder first.');
            return;
        }

        // Create Super Admin
        User::firstOrCreate(
            ['email' => 'superadmin@tokosaya.id'],
            [
                'username' => 'superadmin',
                'role_id' => $superAdminRole->id,
                'password_hash' => Hash::make('password123'),
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'phone' => '+6281234567890',
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
                'is_active' => true,
            ]
        );

        // Create Admin User
        User::firstOrCreate(
            ['email' => 'admin@tokosaya.id'],
            [
                'username' => 'admin',
                'role_id' => $adminRole->id,
                'password_hash' => Hash::make('password123'),
                'first_name' => 'Admin',
                'last_name' => 'TokoSaya',
                'phone' => '+6281234567891',
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
                'is_active' => true,
            ]
        );

        // Create Test Customer
        User::firstOrCreate(
            ['email' => 'customer@example.com'],
            [
                'username' => 'customer',
                'role_id' => $customerRole->id,
                'password_hash' => Hash::make('password123'),
                'first_name' => 'John',
                'last_name' => 'Doe',
                'phone' => '+6281234567892',
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
                'is_active' => true,
            ]
        );

        // Create 20 additional customer accounts
        for ($i = 1; $i <= 20; $i++) {
            $firstName = $faker->firstName;
            $lastName = $faker->lastName;
            $username = strtolower($firstName . $i);
            $email = strtolower($firstName . '.' . $lastName . $i . '@example.com');

            User::firstOrCreate(
                ['email' => $email],
                [
                    'username' => $username,
                    'role_id' => $customerRole->id,
                    'password_hash' => Hash::make('password123'),
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'phone' => '+628' . $faker->numerify('##########'),
                    'email_verified_at' => now(),
                    'phone_verified_at' => now(),
                    'is_active' => true,
                ]
            );
        }

        $this->command->info('Users seeded successfully! Total: ' . User::count() . ' users created.');
    }
}
