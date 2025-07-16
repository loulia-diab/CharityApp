<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HumanCaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $humanCases =[

        ];
        foreach ($humanCases as $humanCase) {
            DB::table('human_cases')->updateOrInsert(
               [

               ]
            );
        }
    }
}
