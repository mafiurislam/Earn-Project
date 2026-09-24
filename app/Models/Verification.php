<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
