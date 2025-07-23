<?php
namespace Database\Seeders;

use App\Models\Campaigns\Campaign;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\HumanCase;
use App\Models\Beneficiary;
use Illuminate\Support\Facades\DB;
class HumanCaseSeeder extends Seeder
{
    public function run()
    {
        // 1. تأكد وجود مستفيد واحد على الأقل
        $beneficiary = Beneficiary::first();
        if (!$beneficiary) {
            $beneficiary = Beneficiary::create([
                'name' => 'Test Beneficiary',
                'email' => 'test@example.com',
                // غير الحقول حسب جدولك
            ]);
        }
        // دالة مساعدة لإنشاء الكاتيغوري وحملاته وحالاته
        $createCategoryWithCases = function ($mainCategory, $categoryNameEn, $categoryNameAr, $cases) use ($beneficiary) {
            $category = Category::updateOrCreate(
                ['main_category' => $mainCategory, 'name_category_en' => $categoryNameEn],
                ['name_category_ar' => $categoryNameAr]
            );
            foreach ($cases as $case) {
                $campaign = Campaign::updateOrCreate(
                    [
                        'title_en' => $case['title_en'],
                        'category_id' => $category->id,
                    ],
                    [
                        'title_en' => $case['title_en'],
                        'title_ar' => $case['title_ar'],
                        'description_en' => $case['description_en'],
                        'description_ar' => $case['description_ar'],
                        'goal_amount' => $case['goal_amount'],
                        'collected_amount' => $case['collected_amount'] ?? 0,
                        'status' => $case['status'] ?? \App\Enums\CampaignStatus::Active,
                        'category_id' => $category->id,
                        'image' => $case['image'] ?? null,

                    ]
                );

                HumanCase::updateOrCreate(
                    [
                        'campaign_id' => $campaign->id,
                        'beneficiary_id' => $beneficiary->id,
                    ],
                    [
                        'is_emergency' => $case['is_emergency'] ?? false,
                    ]
                );
            }
        };

        // ====== HumanCase - Patients =======
        $patientsCases = [
            [
                'title_en' => 'A Life at Risk — Help Save Ghazala',
                'description_en' => "Ghazala was rushed to Al-Shifa Hospital in Idlib after suffering a heart attack and now urgently needs a drug-eluting stent—an expense far beyond her family's means. She lives in a simple home damaged by shelling, with a sick husband and an eldest son bedridden due to diabetes. The family also cares for four children with special needs. Their modest income can't cover even the basics, let alone life-saving treatment. Your support can save Ghazala’s life and bring relief to this struggling family.",
                'goal_amount' => 549,
                'title_ar' => 'شبكة قلبية تنقذ غزالة',
                'description_ar' => "نُقلت الخالة غزالة بشكل إسعافي إلى مستشفى الشفاء في إدلب إثر احتشاء عضلة قلبية، وهي الآن بحاجة عاجلة لزراعة شبكة دوائية بتكلفه تفوق قدرة العائلة. تعيش الخالة في منزل بسيط متضرر من القصف، وزوجها مريض، وابنها الأكبر طريح الفراش بسبب السكري، كما لدى العائلة أربعة أبناء من ذوي الاحتياجات الخاصة. دخل الأسرة المتواضع لا يكفي لتأمين العلاج أو احتياجاتهم الأساسية، ونأمل أن تساهموا في إنقاذ حياة الخالة وتخفيف معاناتهم.",
                'collected_amount' => 0,
                'image'=>"human_case_images/Ghazala.jpg",
                'is_emergency'=>true,
                'status'=>\App\Enums\CampaignStatus::Active->value,
            ],
            [
                'title_en' => "Mahmoud's Last Hope",
                'description_en' => "After spending years unjustly imprisoned, Mahmoud emerged with a weary body—but a resilient spirit. He tried to rebuild his life, working hard to move forward. Tragically, a workplace accident left him with a broken leg and fractured clavicle. Now, he urgently needs surgery to insert a fixation plate. Every delay puts his mobility, dignity, and life at risk. But the cost stands in his way. Help Mahmoud before his body gives out—just as years of his life already have. Be his last hope.",
                'goal_amount' => 323,
                'title_ar' => 'لا تتركوا محمود يُكسر مرتين…',
                'description_ar' => "سنوات من الاعتقال قضى فيها عمره خلف القضبان… خرج بجسد مُنهك، لكنه ما استسلم. حاول يبدأ من جديد، يشتغل، يعيش. لكن حادث مأساوي وهو بالعمل، كسر ساقه اليسرى وعظم ترقوته. واليوم، هو بحاجة لعملية عاجلة لتركيب صفيحة تشريحية… وكل تأخير يهدد مشيه، وكرامته، وحياته. لكن العجز المادي واقف بينه وبين غرفة العمليات… ساعدوا محمود، قبل ما ينهار جسده مثل ما انهارت سنين عمره خلف القضبان. كونوا أمله الأخير.",
                'collected_amount' => 323,
                'image'=>"human_case_images/Mahmoud.jpg",
                'status' => \App\Enums\CampaignStatus::Archived->value

            ],
            [
                'title_en' => "A baby was born with a heart defect and needs your support!",
                'description_en' => "After months of anticipation, baby Sham finally arrived. But joy quickly turned to worry, as she was born with a heart defect and developed pneumonia that left her in need of hospital care, far from her mother’s arms and family’s warmth. Her father did everything he could to get her admitted to a charity hospital, and he succeeded. But now, the cost of medications and transportation is too much for a family struggling to afford their daily meals. The family needs your help to help their baby survive. Do not give up on her!",
                'goal_amount' => 186,
                'title_ar' => 'فتحةٌ قلبية ألزمتها المشفى!',
                'description_ar' => "بعد شهور من الانتظار، وُلدت شام لتنير حياة أسرتها، لكنها جاءت بفتحة قلبية وأُصيبت بإنتان رئوي ألزمها المستشفى بعيدًا عن حضن أمها ودفء عائلتها. سعى والد شام جاهدًا لتأمين علاجها في مستشفى مجاني، ونجح في ذلك، لكن تكاليف الأدوية والتنقّل تُرهق أسرة بالكاد تؤمّن قوت يومها، بدخل لا يكفي أبسط احتياجاتها. تحتاج شام اليوم رعايتكم لتعود سالمة إلى حضن عائلتها المنتظرة. فهلّا كنتم السند؟",
                'collected_amount' => 100,
                'image'=>"human_case_images/baby.jpg",
                'status'=>\App\Enums\CampaignStatus::Active->value,
            ],
            [

                'title_en' => "Help Maha Heal—Inside and Out",
                'description_en' => "Maha never imagined that a simple moment of preparing food for her siblings would change the course of her life.A sudden gas cylinder explosion left her with severe burns to her face and chest, plunging her into a painful journey of treatment and recovery. For more than ten days, she remained in the hospital—away from her home, her siblings, and everything familiar—battling pain and the fear that the burns might leave permanent marks on her young features. But the trauma wasn’t Maha’s alone. Her family, already living in hardship, could barely afford their daily meals—let alone the high costs of burn treatment, hospital care, and follow-up medication. The burden is too heavy for them to carry alone. Your support today can help relieve Maha’s pain, restore her health, and protect her future. Be the reason she smiles again.",
                'goal_amount' => 252,
                'title_ar' => "حروقٌ مؤلمة وطفولةٌ مهددة",
                'description_ar' => "لم تكن مها تتخيلُ أنَّ لحظةً إعداد الطعام لإخوتها ستغيّر مجرى حياتها. انفجارٌ مفاجئ في جرة الغاز سبّب لها حروقًا بليغة في وجهها وصدرها، وأدخلها في دوامة من الألم والعلاج لأكثرَ من عشرة أيام قضتها في المشفى بعيدةً عن بيتها وإخوتها، تصارع الألم والخوف من أن تترك هذه الحروق أثرًا دائمًا على ملامحها. الصدمة لم تكن لمها وحدها، بل لعائلةٍ منهكةٍ أصلًا، تعيش في بيتٍ متواضع وتعتمد على دخلٍ بسيط لا  يكفيهم للطعام فكيف بعلاجٍ طويلٍ ومكلف.",
                'collected_amount' => 100,
                'image'=>"human_case_images/Maha.jpg",
                'status'=>\App\Enums\CampaignStatus::Active->value,
            ]

        ];

        $createCategoryWithCases('HumanCase', 'Patients', 'مرضى', $patientsCases);


        // ====== Student =======
      //  $studentCategoryNameEn = 'Student';
      //  $studentCategoryNameAr = 'طالب علم';

        $studentCases = [
            [
                'title_en' => 'Shahd is a university student who needs your help!',
                'title_ar' => 'شهد طالبة جامعية بحاجة لعونكم!',
                'description_en' => "Shahd is in her second year of studying Health Sciences. She lives in a humble room with her younger siblings, with no guardian to support them. Their father was arrested in 2011, and after the prison was liberated, all they found of him were his documents in Saydnaya Prison. Their mother abandoned them and remarried, leaving Shahd and her siblings to face harsh psychological and financial struggles alone. Despite all the pain, Shahd still dreams of finishing her education so she can become a pillar of support for her brothers and sisters. Your donation today could change the future of an entire family.",
                'description_ar' => "شهد، طالبة في السنة الثانية علوم صحية، تعيش مع إخوتها الصغار في غرفة متواضعة بلا معيل. اعتُقل والدهم عام 2011، ولم يجدوا منه سوى أوراقه في سجن صيدنايا بعد التحرير. والدتهم تخلت عنهم وتزوجت، فواجهوا ضغوطاً نفسية وحياتية قاسية. رغم الألم، تحلم شهد بإكمال تعليمها لتصبح سنداً لإخوتها. تبرعك اليوم قد يغيّر مستقبل عائلة كاملة.",
                'goal_amount' => 500,
                'collected_amount' => 500,
                'image'=>"human_case_images/Shahd.jpg",
                'status'=>\App\Enums\CampaignStatus::Archived->value,
            ],
            [
                'title_en' => 'Ahmad, a university student, is in need for your help.',
                'title_ar' => 'أحمد طالب جامعي بحاجة لعونكم',
                'description_en' => "Ahmad, a young man from al-Bab, has been severely injured in 2016, resulting in partial paralysis. In 2017, he lost his father in a heater explosion, followed by the death of his mother a year later in an airstrike. The pain didn’t end there, his sister’s husband was later killed, leaving behind two children, and Ahmad, along with his brother, took on the burden of supporting the grieving family. Despite all the suffering, he insisted on continuing his education and earned his high school diploma in 2023. Today, he struggles to pay his university tuition and is forced to borrow money every year. Support her, please.",
                'description_ar' => "أحمد، شاب من مدينة الباب، أُصيب عام 2016 إصابة خطيرة أدت لشلل نصفي، وفقد والده عام 2017 بانفجار مدفأة، ثم والدته بالقصف بعد عام. لم يتوقف الألم، فاستشهد زوج شقيقته وترك خلفه طفلين، ليحمل أحمد وأخوه عبء عائلة مكلومة. رغم كل المعاناة، أصر على استكمال تعليمه ونال البكالوريا عام 2023. اليوم، يواجه صعوبة في دفع قسطه الجامعي ويضطر للاستدانة كل عام. بدعمكم، يمكن لحلمه أن يستمر",
                'goal_amount' => 333,
                'collected_amount' => 0,
                'image'=>"human_case_images/Ahmad.jpg",
                'is_emergency' => true,
                'status'=>\App\Enums\CampaignStatus::Active->value,
            ],
            [
                'title_en' => 'Walaa, a student affected by the earthquake, needs your support!',
                'title_ar' => 'ولاء طالبة من متضرري الزلزال بحاجة لسندكم !',
                'description_en' => "Walaa comes from a family of six daughters, all students. Their father was injured in the earthquake and suffered damage to his back and legs, making it difficult for him to work. Walaa also faces significant health issues, including a herniated disc in her back, preventing her from working. There is no one else to support the family.",
                'description_ar' => "الطالبة من عائلة مكونة من ٦ بنات كلهن طالبات الوالد متضرر من الزلزال في ظهره واجريه ويصعب عليه العمل والطالبة عندها مشاكل صحية كثيرة تعاني من ديسك في الظهر.ولا يمكنها العمل ولا يوجد اي معيل للعائلة",
                'goal_amount' => 567,
                'collected_amount' => 200,
                'image'=>"human_case_images/Walaa.jpg",
                'status'=>\App\Enums\CampaignStatus::Active->value,
            ],
        ];

        $createCategoryWithCases('HumanCase', 'Student', 'طالب علم', $studentCases);


        // ====== Needy Families =======
        $needyCategoryNameEn = 'Needy Families';
        $needyCategoryNameAr = 'أسر متعففة';

        $needyCases = [
            [
                'title_en' => 'Help Hanan and Her Children Heal',
                'title_ar' => 'أملٌ بالعودةِ ينهار',
                'description_en' => "Hanan, a widow, has been living with her children in a camp since they were displaced. She has no one to support her and struggles to meet her children's needs. Her young daughter suffers from old burn injuries that caused visible disabilities. Hanan cannot afford her daughter’s treatment, and the situation is getting worse. Hanan needs help to heal her daughter and return to their village. Please support them.",
                'description_ar' => "وسط غرفة ضيقة لا تكاد تقي من حرّ الصيف ولا برد الشتاء، تقيم الأخت حنان مع أطفالها في أحد المخيمات شمال سوريا منذ تهجيرهم. حنان أرملة بلا معيل ولا سند، تعيش على مساعدات الآخرين، وتحاول جاهدة تأمين احتياجات صغارها، إلا أن الفقر وقلة الحيلة وقفت في طريقها. طفلتها الصغيرة تعاني من آثار حروق قديمة تركت إعاقات ظاهرة، وحنان عاجزة حتى عن تأمين وسيلة لنقلها إلى قريتهم. الوضع يزداد سوءًا، وحنان تحتاج لقلوبٍ رحيمة تُمكّنها من العودة إلى قريتها ومداواة ألم سنينها. كونوا عوناً لها.",
                'goal_amount' => 369,
                'collected_amount' => 369,
                'image'=>"human_case_images/Hanan.jpg",
                'status'=>\App\Enums\CampaignStatus::Active->value,
            ],
            [
                'title_en' => "They Can’t Leave the Camp",
                'title_ar' => 'يعجزون عن مغادرة المخيم',
                'description_en' => "In the heart of a camp lacking even the most basic necessities, Umm Mohammad lives in a small room with a cloth roof that offers no protection from the blazing summer heat or the bitter winter cold. A rocket shard struck her foot, leaving her unable to work, and her elderly husband is too frail to provide for the family. She has lost two of her sons and now cares for her grandchildren and the wife of one of her late sons. To survive, they’re forced to sell portions of the humanitarian aid they receive just to buy food and medicine. When their village was finally liberated, Umm Mohammad returned, only to find her home reduced to rubble. All she hopes for now is to return and live near her relatives—but she doesn’t even have the means to pay for transportation or the mounting debts she owes. Let’s be a source of strength for Umm Mohammad and her family. With your support, they can overcome this crisis and begin a new chapter in dignity.",
                'description_ar' => "وسط مخيم يفتقر لأبسط مقومات الحياة، تقيم الخالة أم محمد في غرفة سقفها جادر، لا تقي حرّ الصيف ولا برد الشتاء. شظايا صاروخ أصابت قدمها ومنعتها من العمل، وزوجها المُسنّ عاجز عن إعالتهم. فقدت اثنين من أبنائها، وتعتني اليوم بأحفادها وزوجة أحد أبنائها، ويضطرون لبيع ما يصلهم من مساعدات إنسانية لتأمين الطعام والدواء… بعد تحرير قريتهم، عادت الخالة أم محمد إلى بيتها، فوجدته ركاماً. كل ما تريده الآن هو العودة للعيش قرب أقاربها، لكنها لا تملك حتى ثمن المواصلات أو تسديد ديون تراكمت عليها… لنكن عونا للخالة أم محمد وعائلتها ولنساعدهم على تخطي أزمتهم",
                'goal_amount' => 400,
                'collected_amount' => 0,
                'image'=>"human_case_images/Camp.jpg",
                'status'=>\App\Enums\CampaignStatus::Active->value,
            ],
            [
                'title_en' => "Years have weighed him down and robbed him of his strength!",
                'title_ar' => 'أثقلتهُ السنين وسلبتهُ قِواه!',
                'description_en' => "Displacement forced Abu Muhammad to leave his home, finding himself in an old room that barely serves as a kitchen and sleeping space, devoid of essentials and containing only simple bedding that is hardly sufficient. Abu Muhammad, burdened by the years, is no longer able to work or provide for himself, and debts have accumulated upon him. Today, he sits hoping to return to his destroyed home after liberation, but the travel costs alone stand as an obstacle before him. Let us be a support for Abu Muhammad and his family in this difficult crisis!!",
                'description_ar' => "أجبرَ النزوح العم أبو محمد على ترك منزله، ليجد نفسه في غرفة قديمة لا تتعدى كونها مطبخًا ومكانًا للنوم، خالية من الأساسيات، ولا تحتوي إلّا على فرشٍ بسيط بالكاد يكفي… العم أبو محمد، الذي أثقلته السنين، لم يعد قادرًا على العمل أو توفير ما يسد رمقه، وتراكمت عليه الديون. اليوم، يجلس على أمل العودة إلى منزله المدمر بعد التحرير، لكن تكاليف الطريق وحدها تقف عائقًا أمامه… لنكن عونا للعم أبو محمد وعائلته في هذه الأزمة الصعبة!!",
                'goal_amount' => 300,
                'collected_amount' => 120,
                'image'=>"human_case_images/Years.jpg",
                'is_emergency' => true,
                'status'=>\App\Enums\CampaignStatus::Active->value,

            ],
        ];

        $createCategoryWithCases('HumanCase', 'Needy Families', 'أسر متعففة', $needyCases);
    }
}
