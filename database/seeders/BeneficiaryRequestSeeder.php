<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Beneficiary_request;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;


class BeneficiaryRequestSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        $userIds = User::pluck('id')->toArray();
        $adminIds = Admin::pluck('id')->toArray();

        $count = 20;

        for ($i = 0; $i < $count; $i++) {
            Beneficiary_request::create([
                'user_id' => $userIds ? $faker->randomElement($userIds) : null,
                'admin_id' => $adminIds ? $faker->randomElement($adminIds) : null,

                'name_ar' => $faker->firstName . ' بالعربي',
                'name_en' => $faker->firstName,

                'father_name_ar' => $faker->firstNameMale . ' بالعربي',
                'father_name_en' => $faker->firstNameMale,

                'mother_name_ar' => $faker->firstNameFemale . ' بالعربي',
                'mother_name_en' => $faker->firstNameFemale,

                'gender_ar' => $faker->randomElement(['ذكر', 'أنثى']),
                'gender_en' => $faker->randomElement(['male', 'female']),

                'birth_date' => $faker->date('Y-m-d', '2005-01-01'),

                'marital_status_ar' => $faker->randomElement(['أعزب', 'متزوج', 'مطلق', 'أرمل']),
                'marital_status_en' => $faker->randomElement(['single', 'married', 'divorced', 'widowed']),

                'num_of_members' => $faker->numberBetween(1, 10),

                'study_ar' => $faker->randomElement(['ثانوي', 'جامعي', 'ابتدائي', 'لا يوجد']),
                'study_en' => $faker->randomElement(['high school', 'university', 'primary', 'none']),

                'has_job' => $faker->boolean,

                'job_ar' => $faker->jobTitle,
                'job_en' => $faker->jobTitle,

                'housing_type_ar' => $faker->randomElement(['ملك', 'إيجار', 'مؤقت']),
                'housing_type_en' => $faker->randomElement(['owned', 'rented', 'temporary']),

                'has_fixed_income' => $faker->boolean,
                'fixed_income' => $faker->randomFloat(2, 0, 5000),

                'address_ar' => $faker->address,
                'address_en' => $faker->address,

                'phone' => $faker->phoneNumber,

                'main_category_ar' => 'حالة انسانية',
                'main_category_en' => 'HumanCase',

                'sub_category_ar' => $faker->randomElement(['صحة', 'تعليم', 'بناء']),
                'sub_category_en' => $faker->randomElement(['medical', 'education', 'housing']),

                'notes_ar' => $faker->sentence,
                'notes_en' => $faker->sentence,

                'status_ar' => $faker->randomElement(['جديد', 'مقبول', 'مرفوض']),
                'status_en' => $faker->randomElement(['new', 'accepted', 'rejected']),

                'reason_of_rejection_ar' => $faker->boolean(20) ? $faker->sentence : null,
                'reason_of_rejection_en' => $faker->boolean(20) ? $faker->sentence : null,

                'priority_ar' => $faker->randomElement(['عادية', 'متوسطة', 'عالية']),
                'priority_en' => $faker->randomElement(['normal', 'medium', 'high']),

                'is_sorted' => $faker->boolean,
                'is_read_by_admin' => false,
            ]);
        }
    }
}

