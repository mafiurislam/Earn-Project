<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function getCoverImageUrlAttribute(): string
    {
        if ($this->cover_image && file_exists(public_path('storage/'.$this->cover_image))) {
            return asset('storage/'.$this->cover_image);
        }

        return asset('images/default-cover.svg');
    }

    public function getAudioFileUrlAttribute(): string
    {
        return asset('storage/'.$this->audio_file);
    }
}
