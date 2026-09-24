<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CopyrightClaimLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'slot_number',
        'title',
        'url',
    ];

    protected function casts(): array
    {
        return [
            'slot_number' => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getDisplayTitleAttribute(): string
    {
        $trimmed = trim($this->title ?? '');

        return $trimmed !== '' ? $trimmed : 'Untitled';
    }

    /**
     * Get the lowest available slot number between 1 and 10.
     * Returns null if all 10 slots are occupied.
     */
    public static function nextAvailableSlot(?int $ignoreLinkId = null): ?int
    {
        $query = static::query();
        if ($ignoreLinkId !== null) {
            $query->where('id', '!=', $ignoreLinkId);
        }

        $occupiedSlots = $query->pluck('slot_number')->all();

        for ($i = 1; $i <= 10; $i++) {
            if (! in_array($i, $occupiedSlots, true)) {
                return $i;
            }
        }

        return null;
    }
}
