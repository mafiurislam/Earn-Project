<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EarningTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'admin_id',
        'type',
        'amount',
        'balance_before',
        'balance_after',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'balance_before' => 'decimal:2',
            'balance_after' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function getTypeBadgeClassAttribute(): string
    {
        return match ($this->type) {
            'credit' => 'success',
            'debit' => 'danger',
            'manual_adjustment' => 'warning',
            default => 'info',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'credit' => 'Credit (+)',
            'debit' => 'Debit (-)',
            'manual_adjustment' => 'Adjustment',
            default => ucfirst($this->type),
        };
    }
}
