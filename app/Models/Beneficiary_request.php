<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Beneficiary_request extends Model
{
    protected $fillable = [
        'user_id', 'admin_id', 'name_ar', 'name_en',
        'father_name_ar', 'father_name_en',
        'mother_name_ar', 'mother_name_en',
        'gender_ar', 'gender_en', 'birth_date',
        'marital_status_ar', 'marital_status_en',
        'num_of_members', 'study_ar', 'study_en',
        'has_job', 'job_ar', 'job_en',
        'housing_type_ar', 'housing_type_en',
        'has_fixed_income', 'fixed_income',
        'address_ar', 'address_en', 'phone',
        'main_category_ar', 'main_category_en',
        'sub_category_ar', 'sub_category_en',
        'notes_ar', 'notes_en',
        'status_ar', 'status_en',
        'reason_of_rejection_ar', 'reason_of_rejection_en',
        'is_read_by_admin',
    ];

    protected $casts = [
        'is_read_by_admin' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function details()
    {
        return $this->hasMany(AssistanceDetail::class);
    }



}
