<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Verification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CustomerDemoSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'anowora@example.com'],
            [
                'name' => 'anowora',
                'username' => 'anowora',
                'password' => Hash::make('password123'),
                'earning_balance' => 0.00,
                'total_earnings' => 0.00,
                'is_admin' => false,
            ]
        );

        Verification::updateOrCreate(
            ['user_id' => $user->id],
            [
                'full_name' => 'anowora',
                'pan_number' => 'ABCDE1234F',
                'bank_account' => '987654321098',
                'ifsc_code' => 'SBIN0001234',
                'phone' => '+91 9988776655',
                'email' => 'anowora@example.com',
                'pan_card_photo' => 'verifications/dummy_pan.jpg',
                'signature_photo' => 'verifications/dummy_sig.jpg',
                'status' => 'pending',
            ]
        );
    }
}
