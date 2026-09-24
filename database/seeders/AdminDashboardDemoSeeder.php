<?php

namespace Database\Seeders;

use App\Models\CopyrightClaimLink;
use App\Models\User;
use App\Models\Verification;
use App\Models\Withdrawal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class AdminDashboardDemoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. simrantaufik (Verified, ₹600 earnings, ₹500 paid out, 4 claim links)
        $simran = User::where('username', 'simrantaufik')->orWhere('email', 'simrantaufik@gmail.com')->first() ?: new User;
        $simran->name = 'simrantaufik';
        $simran->username = 'simrantaufik';
        $simran->email = 'simrantaufik@gmail.com';
        $simran->password = Hash::make('password123');
        $simran->phone = '+91 9876543210';
        $simran->earning_balance = 600.00;
        $simran->total_earnings = 1100.00;
        $simran->is_admin = false;
        $simran->save();

        Verification::updateOrCreate(
            ['user_id' => $simran->id],
            [
                'full_name' => 'Simran Taufik',
                'pan_number' => 'ABCDE5678K',
                'bank_account' => '123456789012',
                'ifsc_code' => 'HDFC0001234',
                'phone' => '+91 9876543210',
                'email' => 'simrantaufik@gmail.com',
                'pan_card_photo' => 'verifications/dummy_pan.jpg',
                'signature_photo' => 'verifications/dummy_sig.jpg',
                'status' => 'approved',
            ]
        );

        Withdrawal::updateOrCreate(
            [
                'user_id' => $simran->id,
                'amount' => 500.00,
            ],
            [
                'status' => 'approved',
                'admin_note' => 'Processed via IMPS to HDFC0001234',
                'created_at' => Carbon::create(2026, 9, 18, 12, 0, 0),
                'updated_at' => Carbon::create(2026, 9, 18, 14, 30, 0),
            ]
        );

        // 4 Claim links for simrantaufik matching Image 5
        CopyrightClaimLink::updateOrCreate(
            ['slot_number' => 1],
            [
                'user_id' => $simran->id,
                'title' => 'Untitled',
                'url' => 'https://app.base44.com/apps/6aaac09b3774d425caaed70c/editor/preview',
            ]
        );

        CopyrightClaimLink::updateOrCreate(
            ['slot_number' => 2],
            [
                'user_id' => $simran->id,
                'title' => 'Untitled',
                'url' => 'https://chatgpt.com/c/6aadecc3-eca0-83e9-8bfb-8b63b2144017',
            ]
        );

        CopyrightClaimLink::updateOrCreate(
            ['slot_number' => 3],
            [
                'user_id' => $simran->id,
                'title' => 'sf',
                'url' => 'https://web.whatsapp.com/',
            ]
        );

        CopyrightClaimLink::updateOrCreate(
            ['slot_number' => 4],
            [
                'user_id' => $simran->id,
                'title' => 'zagedxgffhbn',
                'url' => 'https://chatgpt.com/c/6aadecc3-eca0-83e9-8bfb-8b63b2144017',
            ]
        );

        // 2. anowora (Pending KYC with PAN: kcjlksjk565+, Phone: 9382559266, Bank: 545454444545, IFSC: zsfdv26656)
        $anowora = User::where('username', 'anowora')->orWhere('email', 'mafiurislam366@gmail.com')->first() ?: new User;
        $anowora->name = 'anowora';
        $anowora->username = 'anowora';
        $anowora->email = 'mafiurislam366@gmail.com';
        $anowora->password = Hash::make('password123');
        $anowora->phone = '9382559266';
        $anowora->earning_balance = 0.00;
        $anowora->total_earnings = 0.00;
        $anowora->is_admin = false;
        $anowora->save();

        Verification::updateOrCreate(
            ['user_id' => $anowora->id],
            [
                'full_name' => 'anowora',
                'pan_number' => 'kcjlksjk565+',
                'bank_account' => '545454444545',
                'ifsc_code' => 'zsfdv26656',
                'phone' => '9382559266',
                'email' => 'mafiurislam366@gmail.com',
                'pan_card_photo' => 'verifications/dummy_pan.jpg',
                'signature_photo' => 'verifications/dummy_sig.jpg',
                'status' => 'pending',
            ]
        );

        // 3. Ta (Verified KYC, ₹0 earnings)
        $ta = User::where('username', 'ta')->orWhere('email', 'rajurm063@gmail.com')->first() ?: new User;
        $ta->name = 'Ta';
        $ta->username = 'ta';
        $ta->email = 'rajurm063@gmail.com';
        $ta->password = Hash::make('password123');
        $ta->phone = '+91 9123456780';
        $ta->earning_balance = 0.00;
        $ta->total_earnings = 0.00;
        $ta->is_admin = false;
        $ta->save();

        Verification::updateOrCreate(
            ['user_id' => $ta->id],
            [
                'full_name' => 'Ta Artist',
                'pan_number' => 'TAKPM9921L',
                'bank_account' => '654321987654',
                'ifsc_code' => 'SBIN0004567',
                'phone' => '+91 9123456780',
                'email' => 'rajurm063@gmail.com',
                'pan_card_photo' => 'verifications/dummy_pan.jpg',
                'signature_photo' => 'verifications/dummy_sig.jpg',
                'status' => 'approved',
            ]
        );

        // 4. Mafiur Islam (Unverified KYC, ₹0 earnings)
        $mafiur = User::where('username', 'mafiurislam')->orWhere('email', 'mafiurislam365@gmail.com')->first() ?: new User;
        $mafiur->name = 'Mafiur Islam';
        $mafiur->username = 'mafiurislam';
        $mafiur->email = 'mafiurislam365@gmail.com';
        $mafiur->password = Hash::make('password123');
        $mafiur->phone = '+91 9832145678';
        $mafiur->earning_balance = 0.00;
        $mafiur->total_earnings = 0.00;
        $mafiur->is_admin = false;
        $mafiur->save();
    }
}
