<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable ,HasApiTokens ;
    protected $guard = 'admin';


    protected $fillable = [
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function volunteerRequests()
    {
        return $this->hasMany(Volunteer_request::class);
    }

    public function beneficiary_requests()
    {
        return $this->hasMany(Beneficiary_request::class);
    }

    protected function casts(): array
    {
        return [
            //'email_verified_at' => 'datetime',
            // 'password' => 'hashed',
        ];
    }
}
