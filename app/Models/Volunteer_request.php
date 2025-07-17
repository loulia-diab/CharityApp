<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Volunteer_request extends Model
{
    protected $fillable = [
        'user_id',
        'admin_id',
        'full_name_ar',
        'full_name_en',
        'gender_ar',
        'gender_en',
        'birth_date',
        'address_ar',
        'address_en',
        'study_qualification_ar',
        'study_qualification_en',
        'job_ar',
        'job_en',
        'preferred_times_ar',
        'preferred_times_en',
        'has_previous_volunteer',
        'previous_volunteer_ar',
        'previous_volunteer_en',
        'phone',
        'notes_ar',
        'notes_en',
        'status_ar',
        'status_en',
        'reason_of_rejection_ar',
        'reason_of_rejection_en',
        'is_read_by_admin',
    ];

    public function days()
    {
        return $this->belongsToMany(Day::class, 'preferred_days_of_volunteer');
    }

    public function types()
    {
        return $this->belongsToMany(Volunteering_type::class, 'preferred_volunteering_type');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function volunteer()
    {
        return $this->hasOne(Volunteer::class);
    }

    protected $casts = [
        'is_read_by_admin' => 'boolean',
    ];

}
