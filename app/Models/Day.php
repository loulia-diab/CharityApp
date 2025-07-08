<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Day extends Model
{
    public function volunteers()
    {
        return $this->belongsToMany(Volunteer_request::class, 'preferred_days_of_volunteer');
    }

}
