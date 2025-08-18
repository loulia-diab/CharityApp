<?php

namespace App\Models;

use App\Models\Campaigns\Campaign;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Sponsorship extends Model
{
    protected $fillable = [
        'campaign_id',
        'beneficiary_id',
        'is_permanent'
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
    // Accessors to format date on Damascus time
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->setTimezone('Asia/Damascus')->toDateTimeString();
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->setTimezone('Asia/Damascus')->toDateTimeString();
    }
    public function getCancelledAtAttribute($value)
    {
        return $value
            ? Carbon::parse($value)->setTimezone('Asia/Damascus')->toDateTimeString()
            : null;
    }
    public function reports()
    {
        return $this->hasMany(Report::class);
    }

}
