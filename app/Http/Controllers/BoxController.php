<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Box;

class BoxController extends Controller
{

    public function getBoxByName($name_en)
    {
        $locale = app()->getLocale();

        $box = Box::where('name_en', $name_en)
            ->with('children')
            ->first();

        if (!$box) {
            return response()->json(['message' => 'Box not found'], 404);
        }

        // إعادة البيانات حسب اللغة
        $boxData = [
            'id' => $box->id,
            'name' => $locale === 'ar' ? $box->name_ar : $box->name_en,
            'description' => $locale === 'ar' ? $box->description_ar : $box->description_en,
            'image' => $box->image,
            'price' => $box->price,
            'children' => $box->children->map(function ($child) use ($locale) {
                return [
                    'id' => $child->id,
                    'name' => $locale === 'ar' ? $child->name_ar : $child->name_en,
                    'description' => $locale === 'ar' ? $child->description_ar : $child->description_en,
                    'image' => $child->image,
                    'price' => $child->price,
                ];
            }),
        ];

        return response()->json($boxData);
    }
}
