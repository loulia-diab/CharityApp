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
                [
                    'title_en' => 'Building Al-Noor Mosque',
                    'title_ar' => 'بناء مسجد النور',
                    'description_en' => 'Mosques have always been sanctuaries of peace and worship, uniting communities in faith and learning. In a poor village in northern Syria, over 500 families have no mosque to gather in. Through Building Al-Noor Mosque, we will establish a place of worship for 700 people, including a Quran learning hall for children and essential facilities. Your support will help build a house of God and bring spiritual light to an entire community.',
                    'description_ar' => 'المساجد كانت ولا تزال بيوت الله التي يجتمع فيها الناس على ذكره وعبادته، وهي الملاذ الروحي لكل محتاج إلى السكينة والطمأنينة. في إحدى القرى الفقيرة شمال سوريا، يعيش أكثر من 500 عائلة بلا مسجد يجمعهم للصلاة والعبادة والتعلم. تبرعك اليوم في حملة بناء مسجد النور سيساهم في إنشاء مسجد يتسع لـ 700 مصلٍّ، مع قاعة صغيرة لتحفيظ القرآن للأطفال ومرافق خدمية. فلنكن معاً سبباً في إعمار بيت من بيوت الله.',
                    'category_id' => 1,
                    'goal_amount' => 80000,
                    'collected_amount' => 1000,
                    'start_date' => '2025-09-01',
                    'end_date' => '2026-03-01',
                    'status' => CampaignStatus::Active->value,
                    'created_at' => Carbon::now(),
                    'image' => "campaign_images/construction1.jpg"
                ],
                [
                    'title_en' => 'Al-Amal (Hope) School',
                    'title_ar' => 'مدرسة الأمل',
                    'description_en' => 'Education is the strongest weapon against ignorance and poverty. In rural Idlib, over 1,200 children are out of school due to the lack of proper facilities. Through Al-Amal School, we aim to construct a modern school with 12 classrooms, a computer lab, and a small library — opening the doors of knowledge and opportunity once more. Your gift today may create tomorrow’s doctor, teacher, or engineer.',
                    'description_ar' => 'التعليم هو السلاح الأقوى لمحاربة الجهل والفقر. في ريف إدلب، أكثر من 1,200 طفل حُرموا من الدراسة لغياب المدارس المجهزة. من خلال حملة مدرسة الأمل نعمل على بناء مدرسة حديثة مكونة من 12 صفاً، مع قاعة حاسوب ومكتبة صغيرة. بذلك نمنح الأطفال فرصة للعودة إلى مقاعد الدراسة، ونفتح أمامهم أبواب المستقبل. تبرعك اليوم قد يصنع طبيباً أو معلماً أو مهندساً في الغد.',
                    'category_id' => 1,
                    'goal_amount' => 120000,
                    'collected_amount' => 5000,
                    'start_date' => '2025-09-15',
                    'end_date' => '2026-06-15',
                    'status' => CampaignStatus::Active->value,
                    'created_at' => Carbon::now(),
                    'image' => "campaign_images/construcrtion2.jpg"
                ],
                [
                    'title_en' => 'House of Mercy for Orphans',
                    'title_ar' => 'بيت الرحمة للأيتام',
                    'description_en' => 'Orphans are a sacred trust, and embracing them with love and care is one of the greatest human investments. In Azaz, hundreds of orphans live in hardship without safe shelter or basic care. House of Mercy for Orphans will build a small residential complex of 20 apartments, with a shared dining hall and dedicated spaces for learning and play. Together, we can give them warmth, security, and a place to smile again.',
                    'description_ar' => 'الأيتام هم أمانة في أعناقنا، واحتضانهم بالحب والرعاية هو أعظم استثمار إنساني. في مدينة أعزاز، يعيش مئات الأطفال الأيتام في ظروف صعبة بلا مأوى آمن أو رعاية أساسية. تهدف حملة بيت الرحمة للأيتام إلى بناء مجمع سكني صغير يضم 20 شقة، مع قاعة طعام مشتركة ومساحات للعب والدراسة، ليكون حضناً آمناً لهؤلاء الأطفال. تبرعك سيكون سبباً في منحهم الدفء والأمان والابتسامة.',
                    'category_id' => 1,
                    'goal_amount' => 95000,
                    'collected_amount' => 9500,
                    'start_date' => '2025-10-01',
                    'end_date' => '2025-10-01',
                    'status' => CampaignStatus::Archived->value,
                    'created_at' => Carbon::now(),
                    'image' => "campaign_images/construcrtion3.jpg"
                ],

                [
                    'title_en' => 'One Pulse!',
                    'title_ar' => 'نبضنا واحد !',
                    'description_en' => 'In the summer of 2025, a large volunteer medical mission will set out, with doctors and surgeons from around the world, to reach thousands of patients in Syria who have been waiting months — even years — for the chance of life-saving treatment or surgery. Organized by the Syrian-German Medical Association (SGMA) in collaboration with Kun Auna team, this mission will cover multiple regions and provinces. Its goals include hundreds of free surgeries — cardiac, neurological, orthopedic, gynecological, maxillofacial, and more — along with specialized consultations, capacity-building for local medical staff, and public health awareness campaigns. This heartbeat will not be complete without your support. While not everyone can be in the operating room, everyone can help open its doors. Join us in One Pulse and help bring life back to those who wait.',
                    'description_ar' => 'في صيف 2025، ينطلق وفد طبي تطوعي كبير، بمشاركة أطباء وجراحين من مختلف أنحاء العالم، ليصل إلى آلاف المرضى في سوريا ممن ينتظرون منذ شهور، وربما سنوات، فرصة علاج أو تدخل جراحي يُنقذ حياتهم. هذا الوفد الإنساني تنظمه الجمعية الطبية السورية الألمانية (SGMA) بالتعاون مع فريق كن عونا، ويستهدف مناطق واسعة ومحافظات عدّة. الهدف هو إجراء مئات العمليات الجراحية مجاناً، منها القلبية والعصبية والعظمية والنسائية والفكية والكثير غيرها، وأيضاً سيتم تقديم استشارات طبية ومعاينة تخصصية كما تهدف الحملة إلى تدريب الكوادر المحلية ورفع كفاءة العمل الطبية وإطلاق حملات توعية صحية. هذا النبض لن يكتمل دون دعمكم، ونحن في فريق كن عونا نفتح حملة "نبضنا واحد" بدافع مسؤوليتنا المشتركة.',
                    'category_id' => 2,
                    'goal_amount' => 100000,
                    'collected_amount' => 0,
                    'start_date' => '2025-09-01',
                    'end_date' => '2025-12-31',
                    'status' => CampaignStatus::Pending->value,
                    'created_at' => Carbon::now(),
                    'image' => "campaign_images/health1.jpg"
                ],
                [
                    'title_en' => 'Support for Medical Facilities in Gaza',
                    'title_ar' => 'دعم المنشآت الطبية في غزة',
                    'description_en' => 'Every day we read about the growing number of “wounded and injured.” But they are not just numbers — they are lives, families, and stories of pain. Hospitals are deliberately targeted, health centers lack critical equipment, and our people in Gaza are left without the means to survive. Children cry in pain, fathers grieve helplessly over their loved ones — this is the reality in Gaza today. Medical centers and field hospitals are overwhelmed and under-equipped in the face of this massive escalation. Your contribution can save a life or protect an entire family. As Allah says: “And whoever saves one life, it is as if he has saved all of humanity.” Be their support. Donate today — even a $100 share can restore hope.',
                    'description_ar' => '"مرضى وجرحى" أعداد تكتب وتنقل كل يوم.. هل أصبح المصابون والجرحى أعداداً نقرأ عنهم ونقف مكتوفي الأيدي عن مساعدتهم، مشافٍ تدمر عمداً، ومراكز صحية تفتقد لتجهيزاتها لمنع أهلنا في غزة من الصمود والعلاج. ليكن لنا دور في الوقوف الى جانب أهلنا هناك، ونسهم في تأمين احتياجاتهم الرئيسية وخاصة الطبية منها. طفل يصرخ متألماً ورجل يبكي قهراً على طفله، هذا هو حالهم في غزة الآن. نقص كبير تعاني منه المراكز الصحية والمشافي الميدانية في هذا التصعيد الكبير الذي تشهده المدينة. مساهمتك في التبرع لهم قد تحيي نفساً أو تنقذ أسرة، قال تعالى: "ومن أحياها فكأنما أحيا الناس جميعا" كن عوناً لهم و سارع بالتبرع لإنقاذ حياتهم.',
                    'category_id' => 2,
                    'goal_amount' => 100000,
                    'collected_amount' => 60000,
                    'start_date' => '2025-08-15',
                    'end_date' => '2026-02-15',
                    'status' => CampaignStatus::Active->value,
                    'created_at' => Carbon::now(),
                    'image' => "campaign_images/health2.jpg"
                ],
                [
                    'title_en' => 'Mother of the Martyr Campaign',
                    'title_ar' => 'حملة أم الشهيد !',
                    'description_en' => 'For a free Syria and in support of its struggling health sector, the Mother of the Martyr campaign brings together doctors from across the globe in cooperation with the Ministry of Health and humanitarian organizations. With over 400 volunteers, including 135 doctors, the campaign will establish mobile clinics, conduct surgeries for patients with special needs, provide dental and reconstructive care for freed detainees, and deliver training workshops for local medical staff. Every donation will go directly to strengthening hospitals in Syria: securing medical supplies, equipment, and covering surgery costs. Stand with us and support this humanitarian journey.',
                    'description_ar' => 'نحو سوريا الحرة وبهدف دعم قطاعها الطبي المتهالك ودعم مئات المرضى، يتوجه اطباء من مختلف الجنسيات بحملة "أم الشهيد" بالتعاون مع وزارة الصحة ومنظمات إنسانية أخرى بخبرات عالية وقلوب معطاءة، سيعمل فريق يتكون من اكثر من 400 متطوع من بينهم 135 طبيبا على تحقيق أهداف الحملة التي من بينها؛ دعم المرضى وتأسيس عيادات متنقلة وعمليات جراحية لذوي الاحتياجات الخاصة وأخرى تجميلية سنية للمعتقلين المحررين في مختلف المحافظات السورية، عدا عن تقديم ورشات عمل للكوادر الطبية. تبرعاتكم بشكل كامل ستذهب لدعم المشافي في سوريا كتأمين المواد الطبية والمستلزمات والمعدات وتكاليف العمليات الجراحية. كونوا معنا وادعموا رحلتنا الانسانية.',
                    'category_id' => 2,
                    'goal_amount' => 100000,
                    'collected_amount' => 100000,
                    'start_date' => '2025-09-01',
                    'end_date' => '2026-04-30',
                    'status' => CampaignStatus::Complete->value,
                    'created_at' => Carbon::now(),
                    'image' => "campaign_images/health3.jpg"
                ],

                [
                    'title_en' => 'My Right to Learn',
                    'title_ar' => 'حقي أتعلم',
                    'description_en' => 'A dream begins with a single step — the first step towards education. Children and young people have been deprived of one of their most basic rights: the right to learn. Whether a child in school or a youth in university, their education is equally vital. Despite the hardships they face, they hold onto the courage to dream and the hope to rise above their struggles. Education is their light forward, and your donation today can be the spark that changes their tomorrow. Be part of their journey — help them reclaim their right to learn.',
                    'description_ar' => 'حلم العمر يبدأ بخطوة تكون أول بدايات السعي للنجاح .. أطفال وشبّان حُرموا من أبسط حقوقهم وأهمّها التعليم ، سواء كان طالب بمرحلة تعليمه المدرسي أو شاب بمرحلة تعليمه الجامعي اللذين لا يقل أحدهما أهمية عن الآخر .. أحلام كبيرة يسعى لتحقيقها كل يوم طالب برغم صعوبة أوضاعهم المعيشية إلا أن في قلوبهم شجاعة التغلب على لحظاتهم القاسية بأحلام بريئة آملين تحقيقها عبر " التعليم" وهو أبسط حقوقهم في الحياة .. تبرعاتكم الحنونة اليوم لحملة حقي أتعلم ستكون بصيص أمل يضيئ عتّمتهم ويعيد لهم آمالهم من جديد، لا تتردد بالتبرع وكُن سببًا بخطوة نجاح لشباب المستقبل.',
                    'category_id' => 3,
                    'goal_amount' => 25000,
                    'collected_amount' => 0,
                    'start_date' => '2025-10-01',
                    'end_date' => '2026-04-01',
                    'status' => CampaignStatus::Active->value,
                    'created_at' => Carbon::now(),
                    'image' => "campaign_images/education1.jpg"
                ],
                [
                    'title_en' => 'Campaign Nine!',
                    'title_ar' => 'حملة تسعة!',
                    'description_en' => 'We are all born of nine months. Yet many have been denied one of their simplest and most crucial rights: education. Whether school or university, both are equally essential today. For the second year, we launch Campaign Nine — inspired by the ninth month, September — a time when children prepare for school and youth eagerly enter university halls. Together, hand in hand, we can stand by them. Small contributions from each of us can create lasting change in the lives of thousands. During September, 9% of all donations will be dedicated to supporting education through the Be a Help team.',
                    'description_ar' => 'لأننا جميعاً أبناءُ تسعة ولأن بعضُنا حُرِم من أبسط حقوقه وأهمها؛ ألا وهيَ التعليم، سواءً المدرسي أو الجامعي اللذَين لايقل أحدُهما أهميةً عن الآخر في عصرنا، أطلقنا حملة "تسعة" في سنتها الثانية التي ترمز للشهر التاسع؛ شهر أيلول المليء باستعدادات الأطفال لعامهم الدراسي الجديد وحماسِ الشُبان والشابات لصفوف الجامعة. بهذهِ الحملة سنكونُ يداً بيد لنُعينهم ونقف بجانبهم، فلا تتردد بالتبرع، فقليلٌ منك وقليلٌ منه سيغير حياة الكثيرين للأفضل دون شك، سيتم تخصيص نسبة 9% من جميع التبرعات الواردة لفريق كن عونا التطوعي خلال شهر أيلول لدعم التعليم',
                    'category_id' => 3,
                    'goal_amount' => 153000,
                    'collected_amount' => 100005,
                    'start_date' => '2025-09-01',
                    'end_date' => '2025-12-31',
                    'status' => CampaignStatus::Active->value,
                    'created_at' => Carbon::now(),
                    'image' => "campaign_images/education2.jpg"
                ],
                [
                    'title_en' => 'A New Year!',
                    'title_ar' => 'سنة جديدة !',
                    'description_en' => 'Begin the new year by changing a child’s life! Fatima, like over a thousand other children in her community, cannot attend school due to the lack of facilities. With just $50, you can help send her back to class. This campaign is dedicated to rebuilding access to education for these children, and also includes providing a bus for an orphan school in Idlib. Every new year is a chance for a new beginning — let’s make theirs brighter.',
                    'description_ar' => 'ابدأ السنة الجديدة بتغيير حياة طفل! فاطمة طفلة لا تذهب للمدرسة، نعمل على بناء مدرسة بالتجمع الذي تسكن فيه مع اكثر من ألف طفل آخر. بتبرّعك ب ٥٠ دولار مع بداية السنة الجديدة، ستتمكن فاطمة من العودة للمدرسة. كل عام وأنتم الخير لهم، كل عام وأنتم وعائلتكم بألف خير. هذه الحملة مخصصة لإعادة الأطفال للمدرسة، بالإضافة لتأمين باص لمدرسة أيتام في ادلب.',
                    'category_id' => 3,
                    'goal_amount' => 10802,
                    'collected_amount' => 0,
                    'start_date' => '2026-01-01',
                    'end_date' => '2026-05-01',
                    'status' => CampaignStatus::Pending->value,
                    'created_at' => Carbon::now(),
                    'image' => "campaign_images/education3.jpg"
                ],




        ]);
    }
}


