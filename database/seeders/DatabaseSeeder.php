<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Primary Owner Admin Account (admin12345@gmail.com / admin12345)
        User::updateOrCreate(
            ['email' => 'admin12345@gmail.com'],
            [
                'name' => 'Owner Admin',
                'username' => 'admin',
                'phone' => '+91 9876543210',
                'password' => Hash::make('admin12345'),
                'earning_balance' => 0.00,
                'total_earnings' => 0.00,
                'is_admin' => true,
            ]
        );

        // Hostinger Webmaster Admin Account (admin@rajdootnivedan.com / admin123)
        User::updateOrCreate(
            ['email' => 'admin@rajdootnivedan.com'],
            [
                'name' => 'Admin Manager',
                'username' => 'adminmanager',
                'phone' => '+91 9876543211',
                'password' => Hash::make('admin123'),
                'earning_balance' => 0.00,
                'total_earnings' => 0.00,
                'is_admin' => true,
            ]
        );

        // Run full demo seeder for customers, verification KYC, earnings, and claim links
        $this->call(AdminDashboardDemoSeeder::class);
    }
}
