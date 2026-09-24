<?php

namespace App\Http\Controllers;

use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function dashboard(): View
    {
        $user = Auth::user();
        $user->load(['verification', 'withdrawals', 'earningTransactions', 'profileInfo']);

        return view('customer.dashboard', compact('user'));
    }

    public function getProfileInfo()
    {
        $user = Auth::user();
        $user->load('profileInfo');

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $user->profileInfo,
                'is_complete' => $user->isProfileComplete(),
            ]);
        }

        return redirect()->route('customer.dashboard');
    }

    public function storeProfileInfo(Request $request)
    {
        $validated = $request->validate([
            'owner_name' => 'required|string|max:255',
            'channel_name' => 'required|string|max:255',
            'youtube_link' => 'required|string|max:500',
            'label_name' => 'required|string|max:255',
        ], [
            'owner_name.required' => 'Owner Name is required.',
            'channel_name.required' => 'YouTube Channel Name is required.',
            'youtube_link.required' => 'YouTube Link is required.',
            'label_name.required' => 'Label Name is required.',
        ]);

        $user = Auth::user();

        $profile = $user->profileInfo()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'owner_name' => trim($validated['owner_name']),
                'channel_name' => trim($validated['channel_name']),
                'youtube_link' => trim($validated['youtube_link']),
                'label_name' => trim($validated['label_name']),
            ]
        );

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Profile information saved successfully!',
                'data' => $profile,
                'is_complete' => true,
            ]);
        }

        return back()->with('success', 'Profile information saved successfully!');
    }

    public function toggleAutocartGenerator(Request $request)
    {
        $user = Auth::user();

        if ($request->has('enabled')) {
            $user->autocart_generator_enabled = filter_var($request->input('enabled'), FILTER_VALIDATE_BOOLEAN);
        } else {
            $user->autocart_generator_enabled = ! $user->autocart_generator_enabled;
        }

        $user->save();

        $statusText = $user->autocart_generator_enabled ? 'ON' : 'OFF';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'enabled' => $user->autocart_generator_enabled,
                'status_text' => $statusText,
                'message' => "Autocart Generator is {$statusText}",
            ]);
        }

        return back()->with('success', "Autocart Generator is {$statusText}");
    }

    public function withdrawals(): View
    {
        $user = Auth::user();
        $user->load(['verification', 'withdrawals' => function ($q) {
            $q->latest();
        }, 'earningTransactions' => function ($q) {
            $q->latest();
        }]);

        return view('customer.withdrawals', compact('user'));
    }

    public function updateProfilePhoto(Request $request)
    {
        $request->validate([
            'profile_photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $user = Auth::user();

        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('avatars', 'public');
            $user->profile_photo = $path;
            $user->save();
        }

        return back()->with('success', 'Profile picture updated successfully!');
    }

    public function requestWithdrawal(Request $request)
    {
        $user = Auth::user();

        // 1. Verify Profile Verification Status
        if (! $user->verification || $user->verification->status !== 'approved') {
            return back()->with('error', 'Profile Verification Required! You must complete and have your profile verified by Admin before submitting a withdrawal request.');
        }

        $request->validate([
            'amount' => 'required|numeric|min:100',
        ]);

        $amount = (float) $request->amount;

        if ($amount > (float) $user->earning_balance) {
            return back()->with('error', 'Insufficient earning balance! Maximum available withdrawal amount: ₹'.number_format($user->earning_balance, 2));
        }

        $balanceBefore = (float) $user->earning_balance;
        $balanceAfter = $balanceBefore - $amount;

        // Deduct from available balance
        $user->earning_balance = $balanceAfter;
        $user->save();

        // Create Withdrawal Request
        $withdrawal = Withdrawal::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'status' => 'pending',
            'admin_note' => 'Requested by customer from dashboard',
        ]);

        // Record transaction debit
        $user->earningTransactions()->create([
            'type' => 'debit',
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'note' => "Withdrawal request #WD-{$withdrawal->id} placed",
        ]);

        return back()->with('success', 'Withdrawal request for ₹'.number_format($amount, 2).' submitted successfully! Admin will process your request shortly.');
    }
}
