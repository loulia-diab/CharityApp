<?php

namespace App\Models;

use App\Models\Campaigns\Campaign;
use Illuminate\Database\Eloquent\Model;

class Sponsorship extends Model
{
    protected $fillable = [
        'campaign_id',
        'beneficiary_id',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function plans()
    {
        return $this->hasMany(Plan::class);
    }
}
