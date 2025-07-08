<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Beneficiary extends Model
{
    protected $fillable = [
        'user_id',
        'beneficiary_request_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function beneficiary_request()
    {
        return $this->belongsTo(Beneficiary_request::class);
    }
}
