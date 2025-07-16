<?php

namespace App\Models;

use App\Enums\RecurrenceType;
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
        'recurrence'
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
}
