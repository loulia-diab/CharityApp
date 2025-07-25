<?php

namespace App\Http\Controllers\Donation_Type\InKind;

use App\Enums\CampaignStatus;
use App\Http\Controllers\Controller;
use App\Models\Campaigns\Campaign;
use App\Models\Category;
use App\Models\InKind;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InKindController extends Controller
{
    public function addInKind2(Request $request)
    {
        $locale = app()->getLocale();
        $otp = '123456'; // كود التحقق الثابت

        $request->validate([
            'address' => 'required|string|max:255',
            'category_ids' => 'required|array|min:1',
            'category_ids.*' => 'exists:categories,id',
            'phone' => 'required|string|digits:10',
            'otp' => 'required|string',
        ]);

        if ($request->otp !== $otp) {
            return response()->json(['message' => 'رمز التحقق غير صحيح'], 422);
        }

        $user = auth()->user();
        if (!$user) {
            return response()->json([
                'message' => $locale === 'ar' ? 'غير مصرح' : 'Unauthorized'
            ], 401);
        }

        // تأكيد أن كل التصنيفات من نوع InKind
        $valid = Category::whereIn('id', $request->category_ids)
            ->where('main_category', 'InKind')
            ->count();

        if ($valid !== count($request->category_ids)) {
            return response()->json([
                'message' => $locale === 'ar' ? 'بعض التصنيفات غير صالحة' : 'Some categories are invalid'
            ], 422);
        }

        try {
            DB::beginTransaction();

            // إنشاء حملة فارغة فقط لربطها بالتصنيفات والتبرع
            $campaign = Campaign::create([
                'title_en' =>  'in Kind',
                'title_ar' =>  'تبرع عيني',
                'description_en' => '',
                'description_ar' => '',
                'status' => CampaignStatus::Pending->value,
                'goal_amount' => 0,
                'collected_amount' => 0,
                'category_id' => $request->category_ids[0],
            ]);
            $campaign->category()->sync($request->category_ids);

            // إنشاء التبرع العيني وربطه بالحملة والتصنيفات
            $inKind = InKind::create([
                'user_id' => $user->id,
                'campaign_id' => $campaign->id,
                'address_en' => $locale === 'en' ? $request->address : null,
                'address_ar' => $locale === 'ar' ? $request->address : null,
                'phone' => $request->phone,
            ]);

            $inKind->categories()->sync($request->category_ids);

            DB::commit();

            return response()->json([
                'message' => $locale === 'ar' ? 'تم إنشاء التبرع العيني بنجاح' : 'In-kind donation created successfully',
                'data' => $inKind->load('categories', 'campaign'),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $locale === 'ar' ? 'حدث خطأ أثناء الحفظ' : 'Error while saving',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function addInKind(Request $request)
    {
        $locale = app()->getLocale();
        $otp = '123456';

        $request->validate([
            'address' => 'required|string|max:255',
            'category_ids' => 'required|array|min:1',
            'category_ids.*' => 'exists:categories,id',
            'phone' => 'required|string|digits:10',
            'otp' => 'required|string',
        ]);

        if ($request->otp !== $otp) {
            return response()->json(['message' => 'رمز التحقق غير صحيح'], 422);
        }

        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => $locale === 'ar' ? 'غير مصرح' : 'Unauthorized'], 401);
        }

        // تحقق أن كل التصنيفات تابعة للتبرعات العينية
        $valid = Category::whereIn('id', $request->category_ids)
            ->where('main_category', 'InKind')
            ->count();

        if ($valid !== count($request->category_ids)) {
            return response()->json([
                'message' => $locale === 'ar' ? 'بعض التصنيفات غير صالحة' : 'Some categories are invalid'
            ], 422);
        }

        try {
            DB::beginTransaction();

            $created = [];

            foreach ($request->category_ids as $categoryId) {
                // أنشئ الحملة المرتبطة بالصنف
                $campaign = Campaign::create([
                    'title_en' => 'In Kind',
                    'title_ar' => 'تبرع عيني',
                    'description_en' => '',
                    'description_ar' => '',
                    'status' => CampaignStatus::Pending->value,
                    'goal_amount' => 0,
                    'collected_amount' => 0,
                    'category_id' => $categoryId,
                ]);
                if (!$campaign || !$campaign->id) {
                    DB::rollBack();
                    return response()->json([
                        'message' => $locale === 'ar' ? 'فشل في إنشاء الحملة' : 'Failed to create campaign',
                    ], 500);
                }
                // أنشئ التبرع العيني ويرتبط بالحملة فقط
                $inKind = InKind::create([
                    'user_id' => $user->id,
                    'campaign_id' => $campaign->id,
                    'address_en' => $locale === 'en' ? $request->address : null,
                    'address_ar' => $locale === 'ar' ? $request->address : null,
                    'phone' => $request->phone,
                ]);

                $created[] = [
                    'in_kind' => $inKind,
                    'campaign' => $campaign,
                    'category' => Category::find($categoryId),
                ];

                $inKind->campaign_id = $campaign->id;
                $inKind->save();
            }

            DB::commit();
            $data = collect($created)->map(function ($item) {
                $inKind = $item['in_kind'];
                $category = $item['category']; // استخدم الكاتيجوري يلي جهزته فوق

                return [
                    'in_kind' => [
                        'id' => $inKind->id,
                        'user_id' => $inKind->user_id,
                        'campaign_id' => $inKind->campaign_id,
                        'category_id' => $category?->id,
                        'name_category_en' => $category?->name_category_en,
                        'name_category_ar' => $category?->name_category_ar,
                        'address_en' => $inKind->address_en,
                        'address_ar' => $inKind->address_ar,
                        'phone' => $inKind->phone,
                        'created_at' => $inKind->created_at,
                        'updated_at' => $inKind->updated_at,
                    ]
                ];
            });



            return response()->json([
                'message' => $locale === 'ar' ? 'تم إنشاء التبرعات العينية بنجاح' : 'In-kind donations created successfully',
                'data' => $data,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $locale === 'ar' ? 'حدث خطأ أثناء الحفظ' : 'Error while saving',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    public function getAllUserInKinds()
    {
        $locale = app()->getLocale();
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'message' => $locale === 'ar' ? 'غير مصرح' : 'Unauthorized',
            ], 401);
        }

        $inKinds = InKind::with([
            'campaign.category' => function ($query) {
                $query->select('id', 'name_category_en', 'name_category_ar');
            }
        ])
            ->where('user_id', $user->id)
            ->get();

        $results = $inKinds->map(function ($inKind) use ($locale) {
            return [
                'id' => $inKind->id,
                'address' => $locale === 'ar' ? $inKind->address_ar : $inKind->address_en,
                'phone' => $inKind->phone,
                'campaign' => [
                    'id' => $inKind->campaign->id,
                    'title' => $locale === 'ar' ? $inKind->campaign->title_ar : $inKind->campaign->title_en,
                    'status' => $inKind->campaign->status,
                ],
                'category' => [
                    'id' => optional($inKind->campaign->category)->id,
                    'name' => $locale === 'ar'
                        ? optional($inKind->campaign->category)->name_category_ar
                        : optional($inKind->campaign->category)->name_category_en,
                ],
            ];
        });

        return response()->json([
            'data' => $results
        ]);
    }




    // ADMIN
    public function acceptInKind(Request $request)
    {
        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json([
                'message' => 'Unauthorized - Admin access only',
            ], 401);
        }

        $request->validate([
            'in_kind_id' => 'required|exists:in_kinds,id',
        ]);

        $locale = app()->getLocale();

        DB::beginTransaction();

        try {
            $inKind = InKind::with('campaign')->lockForUpdate()->find($request->in_kind_id);

            if (!$inKind) {
                DB::rollBack();
                return response()->json([
                    'message' => $locale === 'ar' ? 'التبرع العيني غير موجود' : 'In-kind donation not found',
                ], 404);
            }

            // تحقق إذا التبرع العيني لم يقبل أو يرفض مسبقاً (اختياري)
            if ($inKind->status === 'active') {
                DB::rollBack();
                return response()->json([
                    'message' => $locale === 'ar' ? 'تم قبول التبرع مسبقاً' : 'In-kind donation already accepted',
                ], 422);
            }

            $campaign = $inKind->campaign;

            if ($campaign && $campaign->status !== CampaignStatus::Active->value) {
                $campaign->status = CampaignStatus::Active->value;
                $campaign->save();
            }

            $inKind->status = 'active';
            $inKind->save();

            Transaction::create([
                'user_id'     => $inKind->user_id,
                'admin_id'    => $admin->id,
                'campaign_id' => $inKind->campaign_id,
                'box_id'      => null,
                'type'        => 'donation',
                'direction'   => 'in',
            ]);

            DB::commit();

            return response()->json([
                'message' => $locale === 'ar'
                    ? 'تم قبول التبرع العيني وتحديث حالة الحملة'
                    : 'In-kind donation accepted and campaign status updated',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $locale === 'ar' ? 'حدث خطأ أثناء العملية' : 'Error during operation',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function getAllInKinds() {
        $locale = app()->getLocale();
        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json([
                'message' => 'Unauthorized - Admin access only',
            ], 401);
        }
        $addressField = "address_{$locale}";

        $query = InKind::query();

        $query->whereHas('campaign.categories', function ($q) {
            $q->where('main_category', 'InKind');
        });

        $query->whereHas('campaign', function($q) {
            $q->where('status', CampaignStatus::Pending->value);
        });

        $inKinds = $query->with('campaign.categories')->get()->map(function ($inKind) use ($addressField, $locale) {
            return [
                'id' => $inKind->id,
                'address' => $inKind->{$addressField} ?? null,
                'phone' => $inKind->phone ?? null,  // إضافة رقم الهاتف لو موجود
                'created_at' => $inKind->created_at->toDateTimeString(),
                'categories' => $inKind->campaign->categories->map(function ($cat) use ($locale) {
                    return [
                        'id' => $cat->id,
                        'name' => $locale === 'ar' ? $cat->name_ar : $cat->name_en,
                    ];
                })->values(),
            ];
        });

        return response()->json([
            'message' => 'In-kind donations retrieved successfully',
            'data' => $inKinds,
        ]);
    }

    public function getInKindsByCategory($categoryId) {
        $locale = app()->getLocale();
        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json([
                'message' => 'Unauthorized - Admin access only',
            ], 401);
        }
        $addressField = "address_{$locale}";

        $query = InKind::query();

        $query->whereHas('campaign.categories', function ($q) use ($categoryId) {
            $q->where('id', $categoryId);
        });

        $query->whereHas('campaign', function($q) {
            $q->where('status', CampaignStatus::Pending->value);
        });

        $inKinds = $query->with('campaign.categories')->get()->map(function ($inKind) use ($addressField, $locale) {
            return [
                'id' => $inKind->id,
                'address' => $inKind->{$addressField} ?? null,
                'phone' => $inKind->phone ?? null,
                'created_at' => $inKind->created_at->toDateTimeString(),
                'categories' => $inKind->campaign->categories->map(function ($cat) use ($locale) {
                    return [
                        'id' => $cat->id,
                        'name' => $locale === 'ar' ? $cat->name_ar : $cat->name_en,
                    ];
                })->values(),
            ];
        });

        return response()->json([
            'message' => 'In-kind donations filtered by category retrieved successfully',
            'data' => $inKinds,
        ]);
    }

    // ما الها داعي
    public function getInKindDetails($inKindId) {
        $locale = app()->getLocale();
        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json([
                'message' => 'Unauthorized - Admin access only',
            ], 401);
        }
        $addressField = "address_{$locale}";

        $inKind = InKind::with('campaign.categories', 'user')->find($inKindId);

        if (!$inKind) {
            return response()->json([
                'message' => $locale === 'ar' ? 'التبرع العيني غير موجود' : 'In-kind donation not found',
            ], 404);
        }

        $data = [
            'id' => $inKind->id,
            'address' => $inKind->{$addressField} ?? null,
            'phone' => $inKind->phone ?? null,
            'created_at' => $inKind->created_at->toDateTimeString(),
            'categories' => $inKind->campaign->categories->map(function ($cat) use ($locale) {
                return [
                    'id' => $cat->id,
                    'name' => $locale === 'ar' ? $cat->name_ar : $cat->name_en,
                ];
            })->values(),
            'user' => [
                'id' => $inKind->user->id,
                'name' => $inKind->user->name,
                'email' => $inKind->user->email,
            ],
        ];

        return response()->json([
            'message' => $locale === 'ar' ? 'تفاصيل التبرع العيني' : 'In-kind donation details',
            'data' => $data,
        ]);
    }





}
