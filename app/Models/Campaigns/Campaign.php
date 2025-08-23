<?php

namespace App\Models\Campaigns;

use App\Enums\CampaignStatus;
use App\Models\Admin;
use App\Models\Beneficiary;
use App\Models\Category;
use App\Models\HumanCase;
use App\Models\InKind;
use App\Models\Report;
use App\Models\Sponsorship;
use App\Models\Volunteer;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    protected $fillable = [
        'title_en', 'title_ar', 'image',
        'description_en', 'description_ar',
        'status', 'goal_amount', 'collected_amount',
        'start_date', 'end_date', 'completed_at',
        'category_id'
    ];
    /*
    protected static function booted()
    {
        static::saving(function ($campaign) {
            if (
                $campaign->collected_amount >= $campaign->goal_amount &&
                $campaign->status === CampaignStatus::Active
            ) {
                $campaign->status = CampaignStatus::Complete;
                $campaign->completed_at = now();
            }
        });
    }
*/
    protected static function booted()
    {
        static::saving(function ($campaign) {
            $now = now();

            $shouldComplete = false;

            // الشرط الأول: المبلغ مكتمل
            if ($campaign->collected_amount >= $campaign->goal_amount &&
                $campaign->status === CampaignStatus::Active
            ) {
                $shouldComplete = true;
            }

            // الشرط الثاني: انتهاء الوقت
            if ($campaign->end_date && $campaign->status === CampaignStatus::Active) {
                if ($now->gte($campaign->end_date)) { // إذا اليوم >= نهاية الحملة
                    $shouldComplete = true;
                }
            }

            // إذا تحقق أي شرط
            if ($shouldComplete) {
                $campaign->status = CampaignStatus::Complete;
                if (!$campaign->completed_at) {
                    $campaign->completed_at = $now;
                }
            }
        });
    }

    // Accessor ذكي لتحديث الحالة عند أي fetch
    /*
    public function getStatusAttribute($value)
    {
        $today = now()->toDateString();

        // تحويل القيمة الحالية إلى Enum object
        $currentStatus = CampaignStatus::from($value);

        if ($currentStatus === CampaignStatus::Active &&
            ($this->collected_amount >= $this->goal_amount ||
                ($this->end_date && $this->end_date->toDateString() <= $today))
        ) {
            $this->status = CampaignStatus::Complete->value; // خزن الـ string في DB
            if (!$this->completed_at) {
                $this->completed_at = now();
            }
            $this->saveQuietly(); // تحديث DB بدون loop
            return CampaignStatus::Complete; // رجع Enum object
        }

        return $currentStatus; // رجع Enum object
    }



*/

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->setTimezone('Asia/Damascus')->toDateTimeString();
    }

    // تحويل updated_at لتوقيت دمشق
    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->setTimezone('Asia/Damascus')->toDateTimeString();
    }
    protected $appends = ['remaining_amount', 'status_label'];

    public function getRemainingAmountAttribute()
    {
        return max(0, $this->goal_amount - $this->collected_amount);
    }

    public function getStatusLabelAttribute()
    {
        $locale = app()->getLocale();

        return $this->status->label($locale) ;
    }

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

    public function inKind()
    {
        return $this->hasOne(inKind::class);
    }
    public function reports()
    {
        return $this->hasMany(Report::class);
    }
    public function transactions()
    {
        return $this->hasMany(\App\Models\Transaction::class);
    }

    protected $casts = [
        'goal_amount' => 'float',
        'collected_amount' => 'float',
        'status' => CampaignStatus::class,
    ];



}
