<?php

namespace App\Models;

use App\Enums\RecurrenceType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'user_id',
        'sponsorship_id',
        'amount',
        'is_activated',
        'start_date',
        'end_date',
        'recurrence',
    ];
    protected $casts = [
        'recurrence' => RecurrenceType::class,
        'is_activated' => 'boolean',
    ];

    protected $appends = ['recurrence_label'];

    public function getRecurrenceLabelAttribute()
    {
        return $this->recurrence?->label(app()->getLocale());
    }
    public function sponsorship()
    {
        return $this->belongsTo(Sponsorship::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
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

    public function getLastDonationAtAttribute($value)
    {
        return $value ? Carbon::parse($value)->setTimezone('Asia/Damascus')->toDateTimeString() : null;
    }

}
