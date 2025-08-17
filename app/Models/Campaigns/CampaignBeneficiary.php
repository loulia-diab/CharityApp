<?php

namespace App\Models\Campaigns;

use App\Models\Admin;
use App\Models\Beneficiary;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class CampaignBeneficiary extends Model
{
    protected $table = 'campaign_beneficiary';

    protected $fillable = [
        'campaign_id',
        'beneficiary_id',
        'admin_id',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->setTimezone('Asia/Damascus')->toDateTimeString();
    }

}
