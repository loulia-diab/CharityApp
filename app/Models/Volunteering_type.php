<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Volunteering_type extends Model
{
    public function volunteers()
    {
        return $this->belongsToMany(Volunteer_request::class, 'preferred_volunteering_type');
    }
}
