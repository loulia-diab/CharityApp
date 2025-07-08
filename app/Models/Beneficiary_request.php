<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Beneficiary_request extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
