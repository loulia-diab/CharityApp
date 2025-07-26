<?php

namespace App\Models\Campaigns;

use App\Models\Admin;
use App\Models\Volunteer;
use Illuminate\Database\Eloquent\Model;

class CampaignVolunteer extends Model
{
    protected $table = 'campaign_volunteer';

    protected $fillable = [
        'campaign_id',
        'volunteer_id',
        'admin_id',
    ];
    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
    public function volunteer()
    {
        return $this->belongsTo(Volunteer::class);
    }
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
