<?php

namespace Database\Seeders;

use App\Models\Box;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BoxSeeder extends Seeder
{
    public function run(): void
    {
        // الصندوق الرئيسي
        $parent1Box = Box::create([
            'box_id' => null,
            'name_ar' => 'الكفارات',
            'name_en' => 'kafarat',
            'description_ar' => null,
            'description_en' => null,
            'image' => null,
            'price' => null,

        ]);

        // صندوق فرعي مرتبط بالصندوق الرئيسي
        Box::create([
            'box_id' => $parent1Box->id,
            'name_ar' => 'إطعام مسكين',
            'name_en' => 'Feeding A Poor',
            'description_ar' => null,
            'description_en' => null,
            'image' => 'boxes/Feeding_A_Poor.jpg',
            'price' => '3.00',

        ]);

        Box::create([
            'box_id' => $parent1Box->id,
            'name_ar' => 'كسوة مسكين',
            'name_en' => 'Clothing A Poor',
            'description_ar' => null,
            'description_en' => null,
            'image' => 'boxes/Clothing_A_Poor.jpg',
            'price' => '10.00',

        ]);

        Box::create([
            'box_id' => $parent1Box->id,
            'name_ar' => 'كفارة يمين',
            'name_en' => 'Expiation for Breaking an Oath',
            'description_ar' => null,
            'description_en' => null,
            'image' => 'boxes/Expiation_for_Breaking_an_Oath.jpg',
            'price' => '15.00',

        ]);

        Box::create([
            'box_id' => $parent1Box->id,
            'name_ar' => 'وفاء نذر',
            'name_en' => 'Fulfillment of a Vow',
            'description_ar' => null,
            'description_en' => null,
            'image' => 'boxes/Fulfillment_of_a_Vow.jpg',
            'price' => '20.00',

        ]);

        Box::create([
            'box_id' => $parent1Box->id,
            'name_ar' => 'أضحية',
            'name_en' => 'Sacrifice',
            'description_ar' => null,
            'description_en' => null,
            'image' => 'boxes/Sacrifice.jpg',
            'price' => '160.00',

        ]);

        Box::create([
            'box_id' => $parent1Box->id,
            'name_ar' => 'عقيقة',
            'name_en' => 'Aqiqah',
            'description_ar' => null,
            'description_en' => null,
            'image' => 'boxes/Aqiqah.jpg',
            'price' => '130.00',

        ]);

        $parent2Box = Box::create([
            'box_id' => null,
            'name_ar' => 'تبرع عام',
            'name_en' => 'General Donation',
            'description_ar' => null,
            'description_en' => null,
            'image' => null,
            'price' => null,

        ]);

        Box::create([
            'box_id' => $parent2Box->id,
            'name_ar' => 'دعم الفريق',
            'name_en' => 'Support the Team',
            'description_ar' => null,
            'description_en' => null,
            'image' => null,
            'price' => null,

        ]);

        Box::create([
            'box_id' => $parent2Box->id,
            'name_ar' => 'دعم قسم الأيتام',
            'name_en' => 'Support the Orphans Section',
            'description_ar' => null,
            'description_en' => null,
            'image' => null,
            'price' => null,

        ]);

        Box::create([
            'box_id' => $parent2Box->id,
            'name_ar' => 'دعم القسم الطبي',
            'name_en' => 'Support the Medical Section',
            'description_ar' => null,
            'description_en' => null,
            'image' => null,
            'price' => null,

        ]);

        // صندوق آخر بدون علاقة
        Box::create([
            'box_id' => null,
            'name_ar' => 'الصدقة',
            'name_en' => 'Sadaqah',
            'description_ar' => 'الصدقة ليست فقط مالًا يُعطى، بل رحمةٌ تُهدى. حين تمنح، فإنك تروي قلبًا عطشانًا، وتُنير دربًا مظلمًا، وتقول لأخيك الإنسان: "أنا معك." الصدقة لا تُنقص مالك، بل تزيدك بركة وسكينة وسرورًا لا يُشترى. امنح مما تحب، فإن ما تعطيه يعود إليك أضعافًا بوجهٍ آخر.',
            'description_en' => 'Sadaqah is not just giving — it’s offering hope, silently and sincerely. When you give, you water a thirsty soul, brighten a dark path, and whisper to someone in pain: You are not alone. Sadaqah doesn’t diminish wealth — it multiplies peace, joy, and unseen blessings. Give from the heart, and watch the world bloom.',
            'image' => 'boxes/sadaqah.jpg',
            'price' => null,

        ]);

        Box::create([
            'box_id' => null,
            'name_ar' => 'الزكاة',
            'name_en' => 'Zakat',
            'description_ar' => 'الزكاة عهد بينك وبين الله، تطهّر به مالك، وتزكي به روحك. هي ليست عبئًا، بل رسالة سامية تقول فيها: "مالي ليس لي وحدي". بالزكاة، يشبع جائع، ويُداوى مريض، ويشعر الفقير أنه مرئي ومحبوب.زكاتك نبض رحمة في جسد المجتمع، فلا تؤخرها.',
            'description_en' => 'Zakat is a sacred covenant between you and God — it purifies your wealth and elevates your soul. It’s not a burden, but a message: My wealth belongs not only to me. Through Zakat, the hungry are fed, the sick are healed, and the forgotten feel seen and loved. Your Zakat is the heartbeat of mercy in a wounded world. Give it now.',
            'image' => 'boxes/zakah.jpg',
            'price' => null,
        ]);

    }
}
