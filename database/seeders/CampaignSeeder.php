<?php

namespace Database\Seeders;

use App\Enums\CampaignStatus;
use App\Models\Campaigns\Campaign;
use Carbon\Carbon;
use Illuminate\Database\Seeder;



class CampaignSeeder extends Seeder
{
    public function run()
    {
        Campaign::insert([
           /* [
                'title_en' => 'Ramadan Food Baskets',
                'title_ar' => 'سلال غذائية لرمضان',
                'description_en' => 'Distribute essential food items to families during Ramadan.',
                'description_ar' => 'توزيع مواد غذائية أساسية للعائلات خلال شهر رمضان.',
                'category_id' => 1,
                'goal_amount' => 5000,
                'collected_amount' => 500,
                'start_date' => Carbon::now()->subDays(10),
                'end_date' => Carbon::now()->addDays(20),
                'status' => CampaignStatus::Active->value,
                'created_at' => Carbon::now(),
                'image'=>"campaign_images/food1.jpg"
            ],
            [
                'title_en' => 'Urgent Medicine for Cancer Patients',
                'title_ar' => 'دواء عاجل لمرضى السرطان',
                'description_en' => 'Providing vital medications for low-income cancer patients.',
                'description_ar' => 'توفير أدوية حيوية لمرضى السرطان من ذوي الدخل المحدود.',
                'category_id' => 2,
                'goal_amount' => 12000,
                'collected_amount' => 0,
                'start_date' => Carbon::now()->subDays(5),
                'end_date' => Carbon::now()->addDays(25),
                'status' => CampaignStatus::Pending->value,
                'created_at' => Carbon::now(),

            ],*/

            [
                'title_en' => 'Rebuild Destroyed Homes',
                'title_ar' => 'إعادة بناء المنازل المدمرة',
                'description_en' => 'Help families return to safe housing by rebuilding their destroyed homes.',
                'description_ar' => 'ساعد العائلات على العودة إلى منازل آمنة من خلال إعادة بنائها.',
                'category_id' => 2,
                'goal_amount' => 50000,
                'collected_amount' => 0,
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addDays(60),
                'status' => CampaignStatus::Active->value,
                'created_at' => Carbon::now(),
               'image'=>"campaign_images/construction1.jpg"
            ],
        /*    [
                'title_en' => 'Monthly Support for Orphans',
                'title_ar' => 'كفالة أيتام شهرية',
                'description_en' => 'Sponsor orphan children and provide them with essential care.',
                'description_ar' => 'اكفل الأطفال الأيتام وقدّم لهم الرعاية الأساسية.',
                'category_id' => 4,
                'goal_amount' => 8000,
                'collected_amount' => 0,
                'start_date' => Carbon::now()->subDays(7),
                'end_date' => Carbon::now()->addDays(30),
                'status' => CampaignStatus::Active->value,
                'created_at' => Carbon::now()->addDays(30),
                'image'=>"campaign_images/orphans1.jpg"
            ],
             */
             [
                'title_en' => 'Mobile Clinic for Rural Areas',
                'title_ar' => 'عيادة متنقلة للمناطق الريفية',
                'description_en' => 'Provide medical services in underserved areas.',
                'description_ar' => 'تقديم الخدمات الطبية في المناطق المحرومة.',
                'category_id' => 3,
                'goal_amount' => 15000,
                'collected_amount' => 5000,
                'start_date' => Carbon::now()->addDays(10),
                'end_date' => Carbon::now()->addDays(45),
                'status' => CampaignStatus::Active->value,
                 'created_at' => Carbon::now()->addDays(10),
                 'image'=>"campaign_images/health1.jpg"
            ],
            [
                'title_en' => 'Back to School Kits',
                'title_ar' => 'حقائب العودة إلى المدرسة',
                'description_en' => 'Provide school supplies to students in need.',
                'description_ar' => 'توفير المستلزمات المدرسية للطلاب المحتاجين.',
                'category_id' => 4,
                'goal_amount' => 7000,
                'collected_amount' => 2200,
                'start_date' => Carbon::now()->subDays(2),
                'end_date' => Carbon::now()->addDays(28),
                'status' => CampaignStatus::Archived->value,
                'created_at' => Carbon::now()->subDays(10),
                'image'=>"campaign_images/education1.jpg"
            ],

        ]);
    }
}


