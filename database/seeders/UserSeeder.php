<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin
        User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@gmail.com')],
            [
                'name' => 'Admin User',
                'password' => Hash::make(env('ADMIN_PASSWORD', 'password')),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        // Create Manager
        User::updateOrCreate(
            ['email' => env('MANAGER_EMAIL', 'manager@gmail.com')],
            [
                'name' => 'Manager User',
                'password' => Hash::make(env('MANAGER_PASSWORD', 'password')),
                'role' => 'manager',
                'is_active' => true,
            ]
        );
    }
}
