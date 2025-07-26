<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Volunteer_request;
use App\Models\Volunteering_type;
use App\Models\Day;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class VolunteerRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $faker = Faker::create('ar_SA');

        // تأكد من وجود أنواع تطوع وأيام مسبقاً
        $volunteeringTypes = Volunteering_type::all();
        $days = Day::all();

        if ($volunteeringTypes->isEmpty() || $days->isEmpty()) {
            $this->command->warn("⚠️ يرجى ملء جدول Volunteering_types و Days أولاً");
            return;
        }

        for ($i = 0; $i < 20; $i++) {
            $request = Volunteer_request::create([
                'user_id' => null,
                'admin_id' => 1 ,
                'full_name_ar' => $faker->name('male'),
                'full_name_en' => $faker->name('male'),
                'gender_ar' => 'ذكر',
                'gender_en' => 'male',
                'birth_date' => $faker->date('Y-m-d', '-18 years'),
                'address_ar' => $faker->address,
                'address_en' => $faker->address,
                'study_qualification_ar' => 'بكالوريوس',
                'study_qualification_en' => 'Bachelor',
                'job_ar' => 'طالب',
                'job_en' => 'Student',
                'preferred_times_ar' => 'صباحاً',
                'preferred_times_en' => 'Morning',
                'has_previous_volunteer' => true ,
                'previous_volunteer_ar' => 'نعم، في الهلال الأحمر',
                'previous_volunteer_en' => 'Yes, at Red Crescent',
                'phone' => $faker->phoneNumber,
                'notes_ar' => 'لا توجد ملاحظات',
                'notes_en' => 'No notes',
                'status_ar' => 'قيد الانتظار',
                'status_en' => 'pending',
                'is_read_by_admin' => false,
            ]);

            // ربط أيام عشوائية
            $randomDays = $days->random(rand(1, 3))->pluck('id')->toArray();
            $request->days()->attach($randomDays);

            // ربط أنواع تطوع عشوائية
            $randomTypes = $volunteeringTypes->random(rand(1, 2))->pluck('id')->toArray();
            $request->types()->attach($randomTypes);
        }
    }
}
