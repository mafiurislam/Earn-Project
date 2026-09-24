<?php

namespace App\Models;

use Database\Factories\CustomerProfileFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerProfile extends Model
{
    /** @use HasFactory<CustomerProfileFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'owner_name',
        'channel_name',
        'youtube_link',
        'label_name',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
