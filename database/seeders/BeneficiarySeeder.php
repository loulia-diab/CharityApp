<?php

namespace Database\Seeders;

use App\Models\Beneficiary;
use App\Models\Beneficiary_request;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BeneficiarySeeder extends Seeder
{

    public function run()
    {

        $beneficiaryRequests = Beneficiary_request::all();
        $users = User::all();

        if ($beneficiaryRequests->isEmpty()) {
            $this->command->info('No beneficiary requests found, skipping beneficiaries seeding.');
            return;
        }

        foreach ($beneficiaryRequests as $request) {
            Beneficiary::create([
                // user_id ممكن يكون null أو نختار مستخدم عشوائي من جدول المستخدمين
                'user_id' => $users->isNotEmpty() ? $users->random()->id : null,
                'beneficiary_request_id' => $request->id,
                'created_at' => now(),
                'updated_at' => now(),
                'priority_ar'=> 'متوسطة',
                'priority_en'=>'medium',
                'is_sorted'=>false,
            ]);
        }
    }
}
