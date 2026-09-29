<?php

namespace App\Http\Controllers;

use App\Models\Verification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class VerificationController extends Controller
{
    public function submitVerification(Request $request)
    {
        $user = Auth::user();
        $existing = Verification::where('user_id', $user->id)->first();

        $hasExistingPan = $existing && ! empty($existing->pan_card_photo);
        $hasExistingSig = $existing && ! empty($existing->signature_photo);

        $request->validate([
            'full_name' => 'required|string|max:255',
            'pan_number' => 'required|string|max:20',
            'pan_card_photo' => ($hasExistingPan ? 'nullable' : 'required').'|image|mimes:jpg,jpeg,png,webp|max:5120',
            'signature_photo' => ($hasExistingSig ? 'nullable' : 'required').'|image|mimes:jpg,jpeg,png,webp|max:5120',
            'bank_account' => 'required|string|max:50',
            'ifsc_code' => 'required|string|max:20',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
        ]);

        $data = [
            'full_name' => $request->full_name,
            'pan_number' => strtoupper($request->pan_number),
            'bank_account' => $request->bank_account,
            'ifsc_code' => strtoupper($request->ifsc_code),
            'phone' => $request->phone,
            'email' => $request->email,
            'status' => 'pending',
            'rejection_reason' => null,
        ];

        if ($request->hasFile('pan_card_photo')) {
            // Delete previously uploaded custom document if applicable
            if ($existing && $existing->pan_card_photo && ! str_contains($existing->pan_card_photo, 'dummy_') && ! str_contains($existing->pan_card_photo, 'sample_')) {
                Storage::disk('public')->delete($existing->pan_card_photo);
            }

            $panPath = $request->file('pan_card_photo')->store('verifications', 'public');
            $data['pan_card_photo'] = $panPath;
            $this->syncPublicFile($panPath);
        }

        if ($request->hasFile('signature_photo')) {
            // Delete previously uploaded custom signature if applicable
            if ($existing && $existing->signature_photo && ! str_contains($existing->signature_photo, 'dummy_') && ! str_contains($existing->signature_photo, 'sample_')) {
                Storage::disk('public')->delete($existing->signature_photo);
            }

            $sigPath = $request->file('signature_photo')->store('verifications', 'public');
            $data['signature_photo'] = $sigPath;
            $this->syncPublicFile($sigPath);
        }

        Verification::updateOrCreate(
            ['user_id' => $user->id],
            $data
        );

        return back()->with('success', 'Profile verification details submitted successfully! Your verification is currently pending Admin review.');
    }

    /**
     * Ensure uploaded file is also directly accessible in public/storage for non-symlink shared hosting.
     */
    protected function syncPublicFile(string $relativePath): void
    {
        try {
            $source = storage_path('app/public/'.$relativePath);
            $destination = public_path('storage/'.$relativePath);

            if (file_exists($source) && ! file_exists($destination)) {
                $dir = dirname($destination);
                if (! File::isDirectory($dir)) {
                    File::makeDirectory($dir, 0755, true, true);
                }
                File::copy($source, $destination);
            }
        } catch (\Throwable $e) {
            // Non-critical; filesystem serves via symlink or fallback route
        }
    }
}
