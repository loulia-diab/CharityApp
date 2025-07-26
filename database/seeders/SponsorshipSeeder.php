<?php

namespace Database\Seeders;

use App\Models\Beneficiary;
use App\Models\Beneficiary_request;
use App\Models\Campaigns\Campaign;
use App\Models\Category;
use App\Models\Sponsorship;
use Illuminate\Database\Seeder;

class SponsorshipSeeder extends Seeder
{
    public function run()
    {
        $createCategoryWithCases = function ($mainCategory, $categoryNameEn, $categoryNameAr, $cases) {
            // إنشاء أو تحديث التصنيف
            $category = Category::updateOrCreate(
                ['main_category' => $mainCategory, 'name_category_en' => $categoryNameEn],
                ['name_category_ar' => $categoryNameAr]
            );

            foreach ($cases as $case) {
                // إنشاء طلب المستفيد (Beneficiary_request)
                $request = Beneficiary_request::create([
                    'user_id' => null,
                    'admin_id' => null,
                    'name_ar' => explode(' ', $case['campaign']['title_ar'])[0],
                    'name_en' => explode(' ', $case['campaign']['title_en'])[0],
                    'gender_ar' => $case['beneficiary']['gender_ar'] ?? '',
                    'gender_en' => $case['beneficiary']['gender_en'] ?? '',
                    'birth_date' => $case['beneficiary']['birth_date'] ?? null,

                    'marital_status_ar' => 'أعزب',
                    'marital_status_en' => 'single',
                    'num_of_members' => 1,
                    'study_ar' => 'لا يوجد',
                    'study_en' => 'none',
                    'has_job' => false,
                    'job_ar' => '',
                    'job_en' => '',
                    'housing_type_ar' => 'مؤقت',
                    'housing_type_en' => 'temporary',
                    'has_fixed_income' => false,
                    'fixed_income' => 0,
                    'address_ar' => '',
                    'address_en' => '',
                    'phone' => '',
                    'main_category_ar' => 'كفالة',
                    'main_category_en' => 'Sponsorship',
                    'sub_category_ar' => '',
                    'sub_category_en' => '',
                    'notes_ar' => '',
                    'notes_en' => '',
                    'status_ar' => 'مقبول',
                    'status_en' => 'accepted',
                    'reason_of_rejection_ar' => null,
                    'reason_of_rejection_en' => null,
                    'priority_ar' => 'عالية',
                    'priority_en' => 'high',
                    'is_sorted' => false,
                    'is_read_by_admin' => true,
                ]);

                // إنشاء المستفيد المرتبط بطلب المستفيد
                $beneficiary = Beneficiary::create([
                    'beneficiary_request_id' => $request->id,
                    'priority_ar' => 'عالية',
                    'priority_en' => 'high',
                    'is_sorted' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // إنشاء أو تحديث الحملة
                $campaign = Campaign::updateOrCreate(
                    [
                        'title_en' => $case['campaign']['title_en'],
                        'category_id' => $category->id,
                    ],
                    [
                        'title_en' => $case['campaign']['title_en'],
                        'title_ar' => $case['campaign']['title_ar'],
                        'description_en' => $case['campaign']['description_en'],
                        'description_ar' => $case['campaign']['description_ar'],
                        'goal_amount' => $case['campaign']['goal_amount'],
                        'collected_amount' => $case['campaign']['collected_amount'] ?? 0,
                        'status' => $case['campaign']['status'] ?? \App\Enums\CampaignStatus::Active->value,
                        'category_id' => $category->id,
                        'image' => $case['campaign']['image'] ?? null,
                    ]
                );

                // إنشاء أو تحديث الكفالة وربطها بالحملة والمستفيد
                Sponsorship::updateOrCreate(
                    [
                        'campaign_id' => $campaign->id,
                        'beneficiary_id' => $beneficiary->id,
                        'is_permanent' => false,
                    ]
                );
            }
        };

        // بيانات الأيتام كمثال مع فصل البيانات كما طلبت
        $orphans = [
            [
                'campaign' => [
                    'title_en' => 'Mahmoud, an orphan child, needs our support.',
                    'title_ar' => 'صغيرنا محمود اليتيم يحتاج دعمنا',
                    'description_en' => 'Mahmoud was born in the city of Bazza’a on 08/28/2020. He is a very active and naughty child. He loves to play like other children. Mahmoud did not live with his father along. His father was died in 2020 as a result of a traffic accident. The poor child lives with his brothers and their mother in their grandfather’s house without any kind of income. With your support and generous sponsorship, you will give Mahmoud a chance of a decent life after he was deprived of it, so help him.',
                    'description_ar' => 'ولد صغيرنا محمود بمدينة بزاعة عام 2020/08/28, يتسم بأنه كثير الحركة ,مشاغب, يهوى اللعب كباقي الاطفال.. لم يرتوي محمود من أباه ولا من حنانه ,حيث اختطف الموت أباه بتاريخ 2020 اثر حادث سير, يعيش صغيرنا واخوته ووالدته بمنزل جده بلا مدخول شهري او معيل يلبي احتياجاتهم. بدعمك وكفالتك السخية ستمنح محمود حياة كريمة بعدما حُرم منها فكن له عوناً.',
                    'goal_amount' => 50,
                    'status' => \App\Enums\CampaignStatus::Active->value,
                    'image' => 'sponsorship_images/Mahmoud.jpg',
                ],
                'beneficiary' => [
                    'gender_en' => 'male',
                    'gender_ar' => 'ذكر',
                    'birth_date' => '2020-08-28',
                ],
            ],
            [
                'campaign' => [
                    'title_en' => 'Saddam is an orphan child who needs your support.',
                    'title_ar' => 'صدام طفل يتيم ينتظر دعمكم',
                    'description_en' => 'Saddam was born in the countryside of Aleppo. He was born on 04/14/2016. He is a cheerful child and has a social character. Our child lives with his mother and three siblings in an apartment in the village of Mulham after his father’s death in 2022. They have no breadwinner. Saddam is completing his education and he dreams of becoming an engineer. Your sponsorship of him will be the first step on the path to his dreams. Note: $25 of this sponsorship is for the child’s education and its value will go as operating fees to the school to continue his education.',
                    'description_ar' => 'صدام طفلٌ من مواليد ريف حلب 14/04/2016، ذو شخصيةٍ اجتماعيةٍ مرحة ونشاطٍ كبير. يعيش طفلنا برفقة والدته وإخوته الثلاثة في شقةٍ في قريةِ مُلهم بعد وفاة الأب عام 2022 وبقائهم بلا مُعيل. يتابع صدام تعليمه ويحلم أن يصبح مهندساً في المستقبل. كفالتك له خطوةٌ أولى في طريقِ أحلامه. ملاحظة: 25$ من هذه الكفالة لتعليم الطفل وقيمتها ستذهب كأجور تشغيلية للمدرسة لمتابعة تعليمه.',
                    'goal_amount' => 50,
                    'status' => \App\Enums\CampaignStatus::Active->value,
                    'image' => 'sponsorship_images/Saddam.jpg',
                ],
                'beneficiary' => [
                    'gender_en' => 'male',
                    'gender_ar' => 'ذكر',
                    'birth_date' => '2016-04-14',
                ],
            ],
            [
                'campaign' => [
                    'title_en' => 'Mona is an orphan who needs your support.',
                    'title_ar' => 'منى طفلة يتيمة بحاجة تبرعاتكم',
                    'description_en' => 'Mona, who was born on January 1, 2015, is a quiet child. The bombing took away her father\'s love while she was still in her first year of life. In this life, she became a lonely orphan, facing life\'s challenges alone. Our child lives with her mother in a rented home that lacks basic necessities. They live in deplorable conditions, which has an impact on the child\'s psychological state. Mona requires sponsorship to go through childhood like the rest of her peers. Your contributions will make a difference and give the girl new hope in life, so please stand by her side.',
                    'description_ar' => 'منى طفلة هادئة ولِدت بتاريخ 01/01/2015، حرمها القصف من حنان والدها وهي ما زالت في السنة الأولى من عمرها، لتغدو يتيمة وحيدة في هذه الحياة تواجه قساوتها بأناملها الرقيقة وملامحها البريئة. تستقر طفلتنا مع والدتها في منزل مستأجر يفتقر إلى أدنى مقومات الحياة، وتحيط بهما ظروف معيشية سيئة مما يؤثر على حالة الطفلة النفسية. منى بحاجة إلى كفالة تساعدها على السير في ركب الطفولة كبقية أقرانها. تبرعاتكم ستصنع فرقا، وستمنح الطفلة أملا جديدا في الحياة، فكونوا بجانبها.',
                    'goal_amount' => 50,
                    'status' => \App\Enums\CampaignStatus::Active->value,
                    'image' => 'sponsorship_images/Mona.jpg',
                ],
                'beneficiary' => [
                    'gender_en' => 'female',
                    'gender_ar' => 'أنثى',
                    'birth_date' => '2015-01-01',
                ],
            ],
        ];

        // استدعاء إنشاء التصنيف والحالات
        $createCategoryWithCases('Sponsorship', 'Orphan', 'يتيم', $orphans);

        // ======== أسر فقيرة (Poor families) ========
        // ======== أسر فقيرة (Poor families) ========
        $poorFamilies = [
            [
                'campaign' => [
                    'title_en' => 'Disabled family members living tragic human conditions',
                    'title_ar' => 'عائلة يعاني كل أفرادها من اعاقة',
                    'description_en' => 'Suleiman and his family were displaced to Turkey after the violent incidents occurred in Syria. They live in a small house with no provider and they face challenging conditions. Suleiman is suffering from tongue tie and difficulty in speaking, while his children are suffering from mental disability which made them helpless regarding working and supporting their selves. This family needs your help to secure their essential needs.',
                    'description_ar' => 'كحال الكثير من الأسر اللاجئة تقاسي عائلة العم سليمان ظروفاً معيشية سيئة للغاية ولاسيما أن العائلة بلا معيل ولا يوجد مصدر للرزق يكفي أفرادها الحاجة والسؤال... يعاني العم سليمان و أولاده جميعاً من إعاقة عقلية مما يحول دون قدرتهم على العمل وتدبر شؤونهم وإعالة أنفسهم.. وتحتاج الأسرة التي جارت الحياة كثيراً عليها وحرمتها أدنى مقومات العيش الكريم للسند والعون والذي يعينهم على تأمين مايحتاجونه من مصروف معيشي وسواه... عونكم قد يجبر خواطر أفراد هذه الأسرة ويبعث في نفوسهم شيئاً من الراحة والطمأنينة التي لم يعرفوا لها طعماً منذ زمن...',
                    'goal_amount' => 150,
                    'status' => \App\Enums\CampaignStatus::Active->value,
                    'image' => 'sponsorship_images/Disabled.jpg',
                ],
                'beneficiary' => [
                    'gender_en' => null,
                    'gender_ar' => null,
                    'birth_date' => null,
                ],
            ],
            [
                'campaign' => [
                    'title_en' => 'A distressed family needs your support',
                    'title_ar' => 'همومٌ كبيرة وقعت على عاتق الخالة غصون وعائلتها',
                    'description_en' => 'This family of five is facing tragic living conditions. The father has a mental illness and he can\'t work. Also, the family has no source of income. Your support can change the family\'s life.',
                    'description_ar' => 'في ظل ظروف معيشية قاسية وحياة مليئة بالمتاعب تعيش عائلة الخالة غصون في منزل إيجار بسيط يفتقر لأبسط مقومات العيش .. وقعت على عاتقهم الكثير من الهموم والايام الصعبة ولا يوجد لديهم مصدر دخل ثابت يستندون عليه وبالكاد قادرين على تأمين قوت يومهم، الزوج مريض وغير قادر على العمل ليساند عائلته في كربتهم، وهم بحاجة لحنانكم ليخفف عنهم جزء من حِملهم الثقيل.. كونوا عوّنهم من بعد الله.',
                    'goal_amount' => 100,
                    'status' => \App\Enums\CampaignStatus::Active->value,
                    'image' => 'sponsorship_images/distressed.jpg',
                ],
                'beneficiary' => [
                    'gender_en' => null,
                    'gender_ar' => null,
                    'birth_date' => null,
                ],
            ],
            [
                'campaign' => [
                    'title_en' => 'A family struggling to make a living',
                    'title_ar' => 'قصفٌ على المنزل دمّر حياتهم',
                    'description_en' => 'Jamal\'s family lives in dire poverty and they don\'t have a source of income. Jamal was injured by the bombing, leaving him paralyzed and bedridden. He needs medical follow-up and medications. He also has four children with severe hearing deficiency. Jamal\'s family lives in a small room and desperately needs your assistance.',
                    'description_ar' => 'تعرض منزل العم جمال للقصف الحربي وحلت الفاجعة على أفراد الأسرة جميعها... فاستشهد الابن الأكبر وأصيب الأب إصابات بليغة أدت لشلل رباعي وعجز تام عن الحركة...كما أصيب من تبقى من الأبناء بفقدان السمع بنسبة كبيرة... وتعيش الأسرة ظروفاً مأساوية للغاية في ظل عدم وجود معيل للأسرة وعدم وجود أي مصدر للدخل يكفيهم الحاجة والسؤال.. تحتاج الاسرة للعون والسند والذي قد يخفف الكثير من الأعباء الملقاة على عاتقهم ويجبر خواطرهم المكسورة...',
                    'goal_amount' => 50,
                    'status' => \App\Enums\CampaignStatus::Active->value,
                    'image' => 'sponsorship_images/struggling.jpg',
                ],
                'beneficiary' => [
                    'gender_en' => null,
                    'gender_ar' => null,
                    'birth_date' => null,
                ],
            ],
        ];

// استدعاء إنشاء التصنيف والحالات
        $createCategoryWithCases('Sponsorship', 'Poor Families', 'أسر فقيرة', $poorFamilies);

// ======== طلاب (Students) ========
        $students = [
            [
                'campaign' => [
                    'title_en' => 'Amr, a university student in need of your support!',
                    'title_ar' => 'عمرو طالب جامعي بحاجة لعونكم !',
                    'description_en' => 'Amr, a young man displaced from Homs, moved with his family from the Khaldiya neighborhood to the Waer neighborhood, and later to Al-Bab in 2017, where he lives in a small house within the Youth Housing Camp. Despite the displacement and harsh circumstances, he has continued his education and is now in his second year at the Faculty of Economics at Gaziantep University. His only support comes from his father, who suffers from a herniated disc but continues to work to secure treatment for his wife, who has a disability, and to cover the family’s basic needs. With your support, Amr can continue his journey toward a better future.',
                    'description_ar' => 'عمرو، شاب مهجّر من حمص، انتقل مع عائلته من الخالدية إلى الوعر، ثم إلى مدينة الباب عام 2017 حيث يقيم في منزل صغير داخل مخيم السكن الشبابي. رغم التهجير والظروف القاسية، واصل تعليمه حتى وصل للسنة الثانية في كلية الاقتصاد بجامعة غازي عنتاب. لا معيل له سوى والده المريض بالدسك، الذي يعمل رغم ألمه لتأمين علاج والدته المعاقة ومصاريف العائلة. بدعمكم، يمكن لعمرو أن يكمل طريقه نحو مستقبل أفضل.',
                    'goal_amount' => 100,
                    'status' => \App\Enums\CampaignStatus::Active->value,
                    'image' => 'sponsorship_images/Amr.jpg',
                ],
                'beneficiary' => [
                    'gender_en' => 'male',
                    'gender_ar' => 'ذكر',
                    'birth_date' => '2006-01-01',
                ],
            ],
            [
                'campaign' => [
                    'title_en' => 'Rawan is a university student who needs your support!',
                    'title_ar' => 'روان طالبة جامعية بحاجة لعونكم',
                    'description_en' => 'Rawan is an ambitious young woman who dreams of becoming a primary school teacher so she can help her father pay for her mother’s cancer treatment — an illness the family has been battling for four years. Her family sold everything they owned to afford the medical procedures and chemotherapy sessions. Sadly, Rawan had to pause her studies because they could no longer afford her tuition fees. This year, she returned to pursue her dream, but her grades have been withheld because she hasn’t been able to pay for two semesters. Rawan needs your help to continue her education and save her future — and her family’s future.',
                    'description_ar' => 'روان، طالبة طموحة تحلم بأن تصبح معلمة صف لتساعد والدها في علاج والدتها المصابة بالسرطان منذ 4 سنوات. باعت أسرتها كل ما تملك لتأمين الجرعات والعمليات، واضطرت روان لإيقاف دراستها بسبب العجز عن دفع الأقساط. عادت هذا العام لتكمل حلمها، لكن علاماتها حُجبت لعدم دفع قسطَي الفصلين. روان بحاجة لمساعدة لتتابع طريقها وتنقذ مستقبلها ومستقبل عائلتها.',
                    'goal_amount' => 150,
                    'status' => \App\Enums\CampaignStatus::Active->value,
                    'image' => 'sponsorship_images/Rawan.jpg',
                ],
                'beneficiary' => [
                    'gender_en' => 'female',
                    'gender_ar' => 'أنثى',
                    'birth_date' => '2003-01-11',
                ],
            ],
            [
                'campaign' => [
                    'title_en' => 'A Programming Student with Big Dreams',
                    'title_ar' => 'طالب برمجة بطموحات كبيرة بحاجة الى عونكم !',
                    'description_en' => 'Tariq is a Syrian student studying Software Engineering — the field through which he dreams of coding a better world. Since he was a child, Tariq has dreamed of becoming a professional programmer and building a brighter future for himself and his family. But due to difficult circumstances, he’s struggling to cover his educational expenses. Every bit of support from you can make a real difference and bring him one step closer to achieving his dream.',
                    'description_ar' => 'طارق طالب سوري يدرس هندسة البرمجيات الاختصاص الذي يحلم ان يبرمج عالمه فيه للأفضل, طارق منذ صغره حلمه يصير مبرمج محترف ويصنع المستقبل اله ولاهله بس بسبب الظروف الصعبة عم يواجه صعوبة بتامين المصاريف الدراسية وكل دعم منكم ممكن يشكل فرق ويقربه من حلمه خطوة بخطوة',
                    'goal_amount' => 200,
                    'status' => \App\Enums\CampaignStatus::Active->value,
                    'image' => 'sponsorship_images/Programming.jpg',
                ],
                'beneficiary' => [
                    'gender_en' => 'male',
                    'gender_ar' => 'ذكر',
                    'birth_date' => '2004-10-30',
                ],
            ],
        ];

        $createCategoryWithCases('Sponsorship', 'Student', 'طالب علم', $students);

    }
}

