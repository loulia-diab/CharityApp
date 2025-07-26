<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['main_category'=>'Campaign','name_category_en' => 'All',         'name_category_ar' => 'الكل'],
            ['main_category'=>'Campaign','name_category_en' => 'Construction', 'name_category_ar' => 'بناء'],
            ['main_category'=>'Campaign','name_category_en' => 'Health',       'name_category_ar' => 'صحة'],
            ['main_category'=>'Campaign','name_category_en' => 'Education',    'name_category_ar' => 'تعليم'],
            //
            ['main_category'=>'HumanCase','name_category_en' => 'All',         'name_category_ar' => 'الكل'],
            ['main_category'=>'HumanCase','name_category_en' => 'Patients',        'name_category_ar' => 'مرضى'],
            ['main_category'=>'HumanCase','name_category_en' => 'Student',         'name_category_ar' => 'طالب علم'],
            ['main_category'=>'HumanCase','name_category_en' => 'Needy Families',  'name_category_ar' => 'أسر متعففة'],

            //
            ['main_category'=>'Sponsorship','name_category_en' => 'All',         'name_category_ar' => 'الكل'],
            ['main_category'=>'Sponsorship','name_category_en' => 'Orphan',        'name_category_ar' => 'يتيم'],
            ['main_category'=>'Sponsorship','name_category_en' => 'Poor Families', 'name_category_ar' => 'أسر فقيرة'],
            ['main_category'=>'Sponsorship','name_category_en' => 'Student',       'name_category_ar' => 'طالب علم'],

            //

            ['main_category'=>'InKind','name_category_en' => 'Children Toys','name_category_ar' => 'ألعاب أطفال'],
            ['main_category'=>'InKind','name_category_en' => 'Furniture',    'name_category_ar' => 'أثاث'],
            ['main_category'=>'InKind','name_category_en' => 'Electronics',  'name_category_ar' => 'إلكترونيات'],
            ['main_category'=>'InKind','name_category_en' => 'Clothes',      'name_category_ar' => 'ملابس'],
            ['main_category'=>'InKind','name_category_en' => 'Others',       'name_category_ar' => 'أخرى'],
        ];

        foreach ($categories as $category) {
            DB::table('categories')->updateOrInsert(
                ['main_category'=>$category['main_category'],
                    'name_category_en'=>$category['name_category_en'],
                    'name_category_ar'=>$category['name_category_ar']],
            );
        }
    }
}

