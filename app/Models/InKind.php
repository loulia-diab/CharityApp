<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InKind extends Model
{
    protected $fillable = [
        'user_id',
        'address_en',
        'address_ar',

    ];
    public function beneficiaries()
    {
        return $this->belongsToMany(Beneficiary::class, 'in_kind_beneficiary');
    }

}
