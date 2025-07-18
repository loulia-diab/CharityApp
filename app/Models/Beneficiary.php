<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Beneficiary extends Model
{
    protected $fillable = [
        'user_id',
        'beneficiary_request_id',
        'priority_ar', 'priority_en','is_sorted',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function beneficiary_request()
    {
        return $this->belongsTo(Beneficiary_request::class);
    }

    public function campaigns()
    {
        return $this->belongsToMany(Beneficiary::class, 'campaign_beneficiary')
            ->withTimestamps()
            ->withPivot('admin_id');
    }

    public function humanCases()
    {
        return $this->hasMany(HumanCase::class);
    }

    public function sponsorships()
    {
        return $this->hasMany(Sponsorship::class);
    }
}
