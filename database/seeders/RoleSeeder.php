<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RolesTableSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            [
                'name' => 'superadmin',
                'display_name' => 'Super Administrator',
                'description' => 'Has full access to all system features',
                'permissions' => json_encode(['*']),
                'is_active' => true
            ],
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'Can manage all content and orders',
                'permissions' => json_encode([
                    'manage_products', 'manage_orders', 'manage_users',
                    'manage_content', 'view_reports'
                ]),
                'is_active' => true
            ],
            [
                'name' => 'manager',
                'display_name' => 'Store Manager',
                'description' => 'Can manage products and orders',
                'permissions' => json_encode([
                    'manage_products', 'manage_orders', 'view_reports'
                ]),
                'is_active' => true
            ],
            [
                'name' => 'customer',
                'display_name' => 'Customer',
                'description' => 'Standard customer account',
                'permissions' => json_encode([
                    'view_products', 'place_orders', 'write_reviews'
                ]),
                'is_active' => true
            ],
            [
                'name' => 'support',
                'display_name' => 'Support Agent',
                'description' => 'Can manage customer support tickets',
                'permissions' => json_encode([
                    'manage_tickets', 'view_orders'
                ]),
                'is_active' => true
            ]
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
