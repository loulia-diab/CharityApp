<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Box extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_ar',
        'name_en',
        'description_ar',
        'description_en',
        'image',
        'price',
        'balance',
        'box_id',
    ];

    // علاقة: هذا الصندوق ينتمي إلى صندوق أب
    public function parent()
    {
        return $this->belongsTo(Box::class, 'box_id');
    }

    // علاقة: هذا الصندوق لديه صناديق أبناء
    public function children()
    {
        return $this->hasMany(Box::class, 'box_id');
    }
}
