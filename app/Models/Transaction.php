<?php

namespace App\Models;

use App\Models\Campaigns\Campaign;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'admin_id',
        'campaign_id',
        'box_id',
        'type',
        'direction',
        'amount',
        'pdf_url',
    ];


    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function box()
    {
        return $this->belongsTo(Box::class);
    }

}
