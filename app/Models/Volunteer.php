<?php

namespace App\Models;

use App\Models\Campaigns\Campaign;
use Illuminate\Database\Eloquent\Model;

class Volunteer extends Model
{
    protected $fillable = [
        'user_id',
        'volunteer_request_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function volunteer_request()
    {
        return $this->belongsTo(Volunteer_request::class);
    }
    public function campaigns()
    {
        return $this->belongsToMany(Campaign::class, 'campaign_volunteer')
            ->withPivot('admin_id')
            ->withTimestamps();
    }
}
