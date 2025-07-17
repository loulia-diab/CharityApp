<?php

namespace Database\Seeders;

use App\Models\HumanCase;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HumanCaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $beneficiaries = \App\Models\Beneficiary::all();

        foreach ($beneficiaries as $beneficiary) {
            HumanCase::create([
                'beneficiary_id' => $beneficiary->id,
                'campaign_id' => null,
                'is_emergency' => false, // مبدئيًا كلها غير طارئة
            ]);
        }
    }
}
