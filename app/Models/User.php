<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'password',
        'profile_photo',
        'earning_balance',
        'total_earnings',
        'is_admin',
        'autocart_generator_enabled',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'autocart_generator_enabled' => 'boolean',
            'earning_balance' => 'decimal:2',
            'total_earnings' => 'decimal:2',
        ];
    }

    public function verification()
    {
        return $this->hasOne(Verification::class);
    }

    public function profileInfo()
    {
        return $this->hasOne(CustomerProfile::class);
    }

    public function customerProfile()
    {
        return $this->hasOne(CustomerProfile::class);
    }

    public function isProfileComplete(): bool
    {
        $profile = $this->profileInfo;
        if (! $profile) {
            return false;
        }

        return ! empty(trim($profile->owner_name ?? ''))
            && ! empty(trim($profile->channel_name ?? ''))
            && ! empty(trim($profile->youtube_link ?? ''))
            && ! empty(trim($profile->label_name ?? ''));
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class)->latest();
    }

    public function earningTransactions()
    {
        return $this->hasMany(EarningTransaction::class)->latest();
    }

    public function copyrightClaimLinks()
    {
        return $this->hasMany(CopyrightClaimLink::class)->orderBy('slot_number', 'asc');
    }

    public function songs()
    {
        return $this->hasMany(Song::class)->latest();
    }

    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo) {
            return asset('storage/'.$this->profile_photo);
        }

        return asset('images/default-avatar.png');
    }

    public function getWithdrawnAmountAttribute(): float
    {
        return (float) $this->withdrawals()->where('status', 'approved')->sum('amount');
    }

    public function isVerified()
    {
        return $this->verification && $this->verification->status === 'approved';
    }

    public function increaseEarnings(float $amount, string $note, ?int $adminId = null): EarningTransaction
    {
        $before = (float) $this->earning_balance;
        $after = $before + $amount;

        $this->earning_balance = $after;
        $this->total_earnings = (float) $this->total_earnings + $amount;
        $this->save();

        return $this->earningTransactions()->create([
            'admin_id' => $adminId,
            'type' => 'credit',
            'amount' => $amount,
            'balance_before' => $before,
            'balance_after' => $after,
            'note' => $note,
        ]);
    }

    public function decreaseEarnings(float $amount, string $note, ?int $adminId = null): EarningTransaction
    {
        $before = (float) $this->earning_balance;
        $after = max(0.00, $before - $amount);

        $this->earning_balance = $after;
        $this->total_earnings = max(0.00, (float) $this->total_earnings - $amount);
        $this->save();

        return $this->earningTransactions()->create([
            'admin_id' => $adminId,
            'type' => 'debit',
            'amount' => $amount,
            'balance_before' => $before,
            'balance_after' => $after,
            'note' => $note,
        ]);
    }

    public function updateEarningsDirect(float $newBalance, ?float $newTotal, string $note, ?int $adminId = null): EarningTransaction
    {
        $before = (float) $this->earning_balance;
        $diff = $newBalance - $before;

        $this->earning_balance = $newBalance;
        if ($newTotal !== null) {
            $this->total_earnings = $newTotal;
        } else {
            // Adjust total earnings proportionally if positive diff
            if ($diff > 0) {
                $this->total_earnings = (float) $this->total_earnings + $diff;
            }
        }
        $this->save();

        return $this->earningTransactions()->create([
            'admin_id' => $adminId,
            'type' => 'manual_adjustment',
            'amount' => abs($diff),
            'balance_before' => $before,
            'balance_after' => $newBalance,
            'note' => $note,
        ]);
    }
}
