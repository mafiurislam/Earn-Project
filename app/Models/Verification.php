<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Verification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'full_name',
        'pan_number',
        'pan_card_photo',
        'signature_photo',
        'bank_account',
        'ifsc_code',
        'phone',
        'email',
        'status',
        'rejection_reason',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the accessible public URL for the PAN Card Photo.
     */
    public function getPanCardPhotoUrlAttribute(): ?string
    {
        return $this->resolveMediaUrl($this->pan_card_photo, 'verifications/dummy_pan.jpg');
    }

    /**
     * Get the accessible public URL for the Signature Photo.
     */
    public function getSignaturePhotoUrlAttribute(): ?string
    {
        return $this->resolveMediaUrl($this->signature_photo, 'verifications/dummy_sig.jpg');
    }

    /**
     * Resolve media path to a valid public URL with fallback support.
     */
    protected function resolveMediaUrl(?string $path, ?string $defaultFallback = null): ?string
    {
        if (! empty($path)) {
            $clean = ltrim($path, '/');

            // If already a full URL
            if (str_starts_with($clean, 'http://') || str_starts_with($clean, 'https://')) {
                return $clean;
            }

            if (str_starts_with($clean, 'storage/')) {
                $clean = substr($clean, 8);
            }

            // Check if file exists in Storage or on disk
            if (Storage::disk('public')->exists($clean) || file_exists(storage_path('app/public/'.$clean)) || file_exists(public_path('storage/'.$clean))) {
                return asset('storage/'.$clean);
            }
        }

        // Check fallback if primary file is missing
        if (! empty($defaultFallback)) {
            $fallbackClean = ltrim($defaultFallback, '/');
            if (Storage::disk('public')->exists($fallbackClean) || file_exists(storage_path('app/public/'.$fallbackClean)) || file_exists(public_path('storage/'.$fallbackClean))) {
                return asset('storage/'.$fallbackClean);
            }

            $basename = basename($fallbackClean);
            if (file_exists(public_path('images/'.$basename))) {
                return asset('images/'.$basename);
            }
        }

        return ! empty($path) ? asset('storage/'.ltrim($path, '/')) : null;
    }
}
