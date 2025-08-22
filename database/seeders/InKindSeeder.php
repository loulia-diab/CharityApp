<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Campaigns\Campaign;
use App\Models\InKind;
use App\Models\Beneficiary;
use App\Models\Category;

class InKindSeeder extends Seeder
{
    public function run()
    {
        $locale = app()->getLocale();

        // 1️⃣ إنشاء مستخدم متبرع
        $user = User::firstOrCreate(
            ['email' => 'donor@example.com'],
            [
                'name' => 'متبرع تجريبي',
                'password' => bcrypt('password'),
            ]
        );


        // 3️⃣ جلب كل فئات InKind
        $categories = Category::where('main_category', 'InKind')->get();

        foreach ($categories as $category) {
            // 4️⃣ إنشاء حملة لكل فئة
            $campaign = Campaign::create([
                'title_en' => $category->name_category_en . ' Donation',
                'title_ar' => 'تبرع ' . $category->name_category_ar,
                'description_en' => 'Donation campaign for ' . $category->name_category_en,
                'description_ar' => 'حملة تبرع لـ' . $category->name_category_ar,
                'goal_amount' => 0,
                'collected_amount' => 0,
                'status' => 'pending',
                'category_id' => $category->id,
            ]);

            // 5️⃣ إنشاء تبرع عيني مرتبط بالمستخدم والحملة
            $inKind = InKind::create([
                'user_id' => $user->id,
                'campaign_id' => $campaign->id,
                'address_en' => 'Damascus - Kafar Soussa',
                'address_ar' => 'دمشق - كفرسوسة',
                'phone' => '0987654321',
            ]);


        }
    }
}
