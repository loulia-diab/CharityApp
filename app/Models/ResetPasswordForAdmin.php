<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResetPasswordForAdmin extends Model
{

    protected $fillable = [
        'email',
        'code',
    ];
}
