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
    ];

    protected $hidden = [
        'password',
        'remember_token',
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
}
