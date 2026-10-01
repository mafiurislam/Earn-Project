<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class Song extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'singer',
        'composer',
        'producer',
        'copyright',
        'cover_image',
        'audio_file',
        'status',
        'admin_notes',
    ];

    protected static function booted(): void
    {
        static::creating(function (Song $song) {
            if (empty($song->copyright)) {
                $song->copyright = '℗ 2026 Rajdoot Nivedan';
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Ensure a storage file is synchronized to public/storage for non-symlink/shared hosting environments.
     */
    public static function syncToPublic(?string $relativePath): void
    {
        if (empty($relativePath)) {
            return;
        }

        try {
            $clean = ltrim(str_replace('storage/', '', $relativePath), '/\\');
            $source = storage_path('app/public/'.$clean);
            $destination = public_path('storage/'.$clean);

            if (file_exists($source) && ! file_exists($destination)) {
                $dir = dirname($destination);
                if (! File::isDirectory($dir)) {
                    File::makeDirectory($dir, 0755, true, true);
                }
                File::copy($source, $destination);
            }
        } catch (\Throwable $e) {
            // Non-critical fallback
        }
    }

    /**
     * Delete file from both storage/app/public and public/storage.
     */
    public static function deleteStorageFile(?string $relativePath): void
    {
        if (empty($relativePath)) {
            return;
        }

        try {
            $clean = ltrim(str_replace('storage/', '', $relativePath), '/\\');

            if (Storage::disk('public')->exists($clean)) {
                Storage::disk('public')->delete($clean);
            }

            $storagePath = storage_path('app/public/'.$clean);
            if (file_exists($storagePath)) {
                @unlink($storagePath);
            }

            $publicPath = public_path('storage/'.$clean);
            if (file_exists($publicPath)) {
                @unlink($publicPath);
            }
        } catch (\Throwable $e) {
            // Silence exceptions on cleanup
        }
    }

    /**
     * Get accessible public URL for the cover image with fallback support.
     */
    public function getCoverImageUrlAttribute(): string
    {
        if (! empty($this->cover_image)) {
            $clean = ltrim(str_replace('storage/', '', $this->cover_image), '/\\');

            if (str_starts_with($clean, 'http://') || str_starts_with($clean, 'https://')) {
                return $clean;
            }

            self::syncToPublic($clean);

            if (file_exists(public_path('storage/'.$clean)) || file_exists(storage_path('app/public/'.$clean)) || Storage::disk('public')->exists($clean)) {
                return asset('storage/'.$clean);
            }
        }

        return asset('images/default-cover.svg');
    }

    /**
     * Get accessible public URL for the audio file.
     */
    public function getAudioFileUrlAttribute(): string
    {
        if (! empty($this->audio_file)) {
            $clean = ltrim(str_replace('storage/', '', $this->audio_file), '/\\');

            if (str_starts_with($clean, 'http://') || str_starts_with($clean, 'https://')) {
                return $clean;
            }

            self::syncToPublic($clean);

            return asset('storage/'.$clean);
        }

        return '';
    }
}
