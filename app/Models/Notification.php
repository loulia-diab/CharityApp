<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = ['user_id', 'title_en','title_ar', 'body_en','body_ar', 'is_read'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

