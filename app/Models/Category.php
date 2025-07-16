<?php

namespace App\Models;

use App\Models\Campaigns\Campaign;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'main_category',
        'name_category_en',
        'name_category_ar',
        'image_category',
    ];
    public function campaigns()
    {
        return $this->hasMany(Campaign::class);
    }
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

}
