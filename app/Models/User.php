<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable ,HasApiTokens;

    protected $fillable = [
        'name',
        'phone',
        'password',
        'profile_image',
        'preferred_language',
        'google_id',
        'email',
        'balance',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

     protected $casts = [
        'balance' => 'float',
        
    ];

    public function volunteer_requests()
    {
        return $this->hasMany(Volunteer_request::class);
    }

    public function beneficiary_requests()
    {
        return $this->hasMany(Beneficiary_request::class);
    }

    public function otps()
    {
        return $this->hasMany(PhoneOtp::class, 'phone', 'phone');
    }


    protected function casts(): array
    {
        return [
            'phone_verified_at' => 'datetime',
           // 'password' => 'hashed',
        ];
    }

    public function plans()
    {
        return $this->hasMany(Plan::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function donations()
    {
        return $this->hasMany(Transaction::class)->where('type', 'donation');
    }

    public function recharges()
    {
        return $this->hasMany(Transaction::class)->where('type', 'recharge');
    }

    public function gifts()
    {
        return $this->hasMany(Gift::class);
    }
    public function inKinds() {
        return $this->hasMany(InKind::class);
    }
// User.php
    public function notifications() { return $this->hasMany(Notification::class); }
    public function devices() { return $this->hasMany(UserDevice::class); }


}
