<?php

namespace App\Models\Campaigns;

use App\Enums\CampaignStatus;
use App\Models\Admin;
use App\Models\Beneficiary;
use App\Models\Category;
use App\Models\HumanCase;
use App\Models\Sponsorship;
use App\Models\Volunteer;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    protected $fillable = [
        'title_en', 'title_ar', 'image',
        'description_en', 'description_ar',
        'status', 'goal_amount', 'collected_amount',
        'start_date', 'end_date', 'completed_at',
    ];
    protected static function booted()
    {
        static::saving(function ($campaign) {
            if (
                $campaign->collected_amount >= $campaign->goal_amount &&
                $campaign->status !== \App\Enums\CampaignStatus::Complete
            ) {
                $campaign->status = \App\Enums\CampaignStatus::Complete;
                $campaign->completed_at = now();
            }
        });
    }

    protected $appends = ['remaining_amount', 'status_label'];

    public function getRemainingAmountAttribute()
    {
        return max(0, $this->goal_amount - $this->collected_amount);
    }

    public function getStatusLabelAttribute()
    {
        $locale = app()->getLocale();
        return CampaignStatus::from($this->status)->label($locale);
    }

    protected $casts = [
        'status' => CampaignStatus::class,
    ];
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
    public function beneficiaries()
    {
        return $this->belongsToMany(Beneficiary::class, 'campaign_beneficiary')
            ->withPivot('admin_id')
            ->withTimestamps();
    }
    public function volunteers()
    {
        return $this->belongsToMany(Volunteer::class, 'campaign_volunteer')
            ->withPivot('admin_id')
            ->withTimestamps();
    }
    public function humanCase()
    {
        return $this->hasOne(HumanCase::class);
    }
    public function sponsorship()
    {
        return $this->hasOne(Sponsorship::class);
    }
}
