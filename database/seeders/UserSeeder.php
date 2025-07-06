<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        // Super Admin
        User::create([
            'role_id' => 1,
            'username' => 'superadmin',
            'email' => 'superadmin@tokosaya.id',
            'password_hash' => Hash::make('password123'),
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'phone' => '6281234567890',
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
            'is_active' => true
        ]);

        // Admin
        User::create([
            'role_id' => 2,
            'username' => 'admin',
            'email' => 'admin@tokosaya.id',
            'password_hash' => Hash::make('password123'),
            'first_name' => 'System',
            'last_name' => 'Admin',
            'phone' => '6281234567891',
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
            'is_active' => true
        ]);

        // Store Manager
        User::create([
            'role_id' => 3,
            'username' => 'manager',
            'email' => 'manager@tokosaya.id',
            'password_hash' => Hash::make('password123'),
            'first_name' => 'Store',
            'last_name' => 'Manager',
            'phone' => '6281234567892',
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
            'is_active' => true
        ]);

        // Support Agent
        User::create([
            'role_id' => 5,
            'username' => 'support',
            'email' => 'support@tokosaya.id',
            'password_hash' => Hash::make('password123'),
            'first_name' => 'Customer',
            'last_name' => 'Support',
            'phone' => '6281234567893',
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
            'is_active' => true
        ]);

        // Create 50 customers
        \App\Models\User::factory()->count(50)->create([
            'role_id' => 4, // Customer role
            'password_hash' => Hash::make('password123'),
            'email_verified_at' => now(),
            'is_active' => true
        ]);
    }
}
