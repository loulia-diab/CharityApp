<?php

namespace App\Models;

use App\Models\Campaigns\Campaign;
use Illuminate\Database\Eloquent\Model;


class HumanCase extends Model
{
    protected $fillable = [
        'campaign_id',
        'beneficiary_id',
        'is_emergency',
    ];

    // العلاقة مع الحملة (اختياري إذا حبيت تربطهم)
    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    // العلاقة مع المستفيد
    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class);
    }
}
