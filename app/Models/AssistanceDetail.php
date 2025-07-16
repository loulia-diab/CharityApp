<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssistanceDetail extends Model
{
    protected $fillable = [

        'beneficiary_request_id',
        'field_name_ar',
        'field_name_en',
        'field_value_ar',
        'field_value_en',
    ];

    public function request()
    {
        return $this->belongsTo(Beneficiary_request::class, 'beneficiary_request_id');
    }
}
