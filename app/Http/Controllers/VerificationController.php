<?php

namespace App\Http\Controllers;

use App\Models\Verification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificationController extends Controller
{
    public function submitVerification(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'pan_number' => 'required|string|max:20',
            'pan_card_photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'signature_photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'bank_account' => 'required|string|max:50',
            'ifsc_code' => 'required|string|max:20',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
        ]);

        $user = Auth::user();

        $panPath = $request->file('pan_card_photo')->store('verifications', 'public');
        $sigPath = $request->file('signature_photo')->store('verifications', 'public');

        Verification::updateOrCreate(
            ['user_id' => $user->id],
            [
                'full_name' => $request->full_name,
                'pan_number' => strtoupper($request->pan_number),
                'pan_card_photo' => $panPath,
                'signature_photo' => $sigPath,
                'bank_account' => $request->bank_account,
                'ifsc_code' => strtoupper($request->ifsc_code),
                'phone' => $request->phone,
                'email' => $request->email,
                'status' => 'pending',
                'rejection_reason' => null,
            ]
        );

        return back()->with('success', 'Profile verification details submitted successfully! Your verification is currently pending Admin review.');
    }
}
