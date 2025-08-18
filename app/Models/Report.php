<?php

namespace App\Models;

use App\Models\Campaigns\Campaign;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'sponsorship_id',
        'file_url',
    ];

    // علاقة التقرير بالحملة
    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    // علاقة التقرير بالكفالة
    public function sponsorship()
    {
        return $this->belongsTo(Sponsorship::class);
    }
}

