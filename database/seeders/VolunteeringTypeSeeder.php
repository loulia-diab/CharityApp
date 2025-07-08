<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VolunteeringTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['name_en' => 'Fieldwork',      'name_ar' => 'ميداني'],
            ['name_en' => 'Administrative', 'name_ar' => 'إداري'],
            ['name_en' => 'Awareness',      'name_ar' => 'توعوي'],
            ['name_en' => 'Media',          'name_ar' => 'إعلامي'],
            ['name_en' => 'Design',         'name_ar' => 'تصميم'],
            ['name_en' => 'Technical',      'name_ar' => 'تقني'],
        ];
        // DB::table('days')->insert($days);

        foreach ($types as $type) {
            DB::table('volunteering_types')->updateOrInsert(
                ['name_en' => $type['name_en']], // شرط التطابق
                ['name_ar' => $type['name_ar']]  // القيم المحدّثة أو الجديدة
            );
        }
    }
}
