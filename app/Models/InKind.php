<?php

namespace App\Models;

use App\Models\Campaigns\Campaign;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class InKind extends Model
{
    protected $fillable = [
        'user_id',
        'address_en',
        'address_ar',
        'phone'

    ];
    public function beneficiaries()
    {
        return $this->belongsToMany(Beneficiary::class, 'in_kind_beneficiary');
    }
    public function user() {
        return $this->belongsTo(User::class);
    }
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->setTimezone('Asia/Damascus')->toDateTimeString();
    }

    // تحويل updated_at لتوقيت دمشق
    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->setTimezone('Asia/Damascus')->toDateTimeString();
    }
    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

}
