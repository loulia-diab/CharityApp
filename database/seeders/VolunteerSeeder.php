<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Volunteer;
use App\Models\Volunteer_request;
use Faker\Factory as Faker;

class VolunteerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // اجلب الطلبات الموجودة (مثلاً 10 فقط)
        $requests = Volunteer_request::inRandomOrder()->take(10)->get();

        foreach ($requests as $request) {
            Volunteer::create([
                'user_id' => $request->user_id, // إن وُجد
                'volunteer_request_id' => $request->id,
            ]);
        }
    }
}
