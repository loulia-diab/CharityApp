<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhoneOtp extends Model
{
    protected $fillable = [
        'phone',
        'otp',
        'expires_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'phone', 'phone');
    }

}
