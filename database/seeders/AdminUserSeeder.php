<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default admin user
        User::firstOrCreate(
            ['email' => 'admin@shipping.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '0912345678',
                'email_verified_at' => now(),
            ]
        );

        // Create a test merchant user
        User::firstOrCreate(
            ['email' => 'merchant@test.com'],
            [
                'name' => 'Test Merchant',
                'password' => Hash::make('password'),
                'role' => 'merchant',
                'phone' => '0923456789',
                'company_name' => 'Test Trading Company',
                'address' => 'Tripoli, Libya',
                'email_verified_at' => now(),
            ]
        );
    }
}
