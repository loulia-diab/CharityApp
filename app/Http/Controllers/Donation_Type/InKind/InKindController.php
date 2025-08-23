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
    public function addInKind(Request $request)
    {
        $locale = app()->getLocale();
        $otp = '312297'; // يجب استبداله بمنطق تحقق حقيقي

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
        $validCount = Category::whereIn('id', $request->category_ids)
            ->where('main_category', 'InKind')
            ->count();

        if ($validCount !== count($request->category_ids)) {
            return response()->json([
                'message' => $locale === 'ar' ? 'بعض التصنيفات غير صالحة' : 'Some categories are invalid'
            ], 422);
        }

        $categories = Category::whereIn('id', $request->category_ids)->get()->keyBy('id');

        try {
            DB::beginTransaction();

            $created = [];

            foreach ($request->category_ids as $categoryId) {
                $category = $categories[$categoryId] ?? null;

                $campaign = Campaign::create([
                    'title_en' => 'In Kind',
                    'title_ar' => 'تبرع عيني',
                    'description_en' => '',
                    'description_ar' => '',
                    'status' => 'pending',
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

                $inKind = InKind::create([
                    'user_id' => $user->id,
                    'campaign_id' => $campaign->id,
                    'address_en' => $locale === 'en' ? $request->address : null,
                    'address_ar' => $locale === 'ar' ? $request->address : null,
                    'phone' => $request->phone,
                ]);
                $inKind->campaign_id = $campaign->id;
                $inKind->save();

                $created[] = [
                    'in_kind' => $inKind,
                    'campaign' => $campaign,
                    'category' => $category,
                ];
            }

            DB::commit();

            $data = collect($created)->map(function ($item) {
                $inKind = $item['in_kind'];
                $category = $item['category'];

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
                'campaign' => [
                    'id' => $inKind->campaign->id,
                   // 'title' => $locale === 'ar' ? $inKind->campaign->title_ar : $inKind->campaign->title_en,
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
            // جلب التبرع العيني مع الحملة المرتبطة وقفل السجل للتحديث
            $inKind = InKind::with('campaign')->find($request->in_kind_id);

            if (!$inKind) {
                DB::rollBack();
                return response()->json([
                    'message' => $locale === 'ar' ? 'التبرع العيني غير موجود' : 'In-kind donation not found',
                ], 404);
            }

            $campaign = $inKind->campaign;


            if (!$campaign) {
                DB::rollBack();
                return response()->json([
                    'message' => $locale === 'ar' ? 'الحملة المرتبطة غير موجودة' : 'Related campaign not found',
                ], 404);
            }
            if ($campaign->status !== CampaignStatus::Pending) {
                DB::rollBack();
                return response()->json([
                    'message' => $locale === 'ar'
                        ? 'لا يمكن قبول التبرع بحالة الحملة الحالية'
                        : 'Cannot accept donation with the current campaign status',
                ], 422);
            }


            // تحديث حالة الحملة إلى active
            $campaign->status = CampaignStatus::Active;
            $campaign->save();

            // تحديث وقت التعديل في التبرع العيني (اختياري)
            $inKind->updated_at = now();
            $inKind->save();

            // إنشاء عملية transaction لتسجيل القبول
            $transaction = Transaction::create([
                'user_id'     => $inKind->user_id,
                'admin_id'    => $admin->id,
                'campaign_id' => $inKind->campaign_id,
                'box_id'      => null,
                'type'        => 'donation',
                'direction'   => 'in',
                'amount'      => 0,
            ]);

            DB::commit();
            return response()->json([
                'message' => $locale === 'ar'
                    ? 'تم قبول التبرع العيني وتحديث حالة الحملة'
                    : 'In-kind donation accepted and campaign status updated',
                'data' => [
                    'in_kind' => [
                        'id' => $inKind->id,
                        'user_id' => $inKind->user_id,
                        'campaign_id' => $inKind->campaign_id,
                        'address' => $locale === 'ar' ? $inKind->address_ar : $inKind->address_en,
                        'phone' => $inKind->phone,
                        'created_at' => $inKind->created_at,
                        'updated_at' => $inKind->updated_at,
                        'status_label' => $campaign?->status_label ?? null,
                    ],
                    'transaction'=>$transaction
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $locale === 'ar' ? 'حدث خطأ أثناء العملية' : 'Error during operation',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getAllInKinds1()
    {
        $locale = app()->getLocale();
        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json([
                'message' => 'Unauthorized - Admin access only',
            ], 401);
        }

        $addressField = "address_{$locale}";

        $query = InKind::query();

        $query->whereHas('campaign.category', function ($q) {
            $q->where('main_category', 'InKind');
        });

        $query->whereHas('campaign', function ($q) {
            $q->where('status', CampaignStatus::Pending->value);
        });

        $inKinds = $query->with('campaign.category')->get()->map(function ($inKind) use ($addressField, $locale) {
            return [
                'id' => $inKind->id,
                'address' => $inKind->{$addressField} ?? null,
                'phone' => $inKind->phone ?? null,
                'created_at' => $inKind->created_at,
                'category' => [
                    'id' => $inKind->campaign->category->id,
                    'name' => $locale === 'ar'
                        ? $inKind->campaign->category->name_category_ar
                        : $inKind->campaign->category->name_category_en,
                ],
            ];
        });

        return response()->json([
            'message' => 'In-kind donations retrieved successfully',
            'data' => $inKinds,
        ]);
    }
    public function getAllInKinds()
    {
        $locale = app()->getLocale();
        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json([
                'message' => 'Unauthorized - Admin access only',
            ], 401);
        }

        $addressField = "address_{$locale}";

        $inKinds = InKind::with(['campaign.category'])
            ->whereHas('campaign.category', function ($q) {
                $q->where('main_category', 'InKind');
            })
            ->get()
            ->map(function ($inKind) use ($addressField, $locale) {
                return [
                    'id' => $inKind->id,
                    'user_id' => $inKind->user_id,
                    'campaign_id' => $inKind->campaign_id,
                    'address' => $inKind->{$addressField} ?? null,
                    'phone' => $inKind->phone ?? null,
                    'created_at' => $inKind->created_at,
                    'updated_at' => $inKind->updated_at,
                    'campaign' => [
                        'id' => $inKind->campaign->id ?? null,
                        'status' => $inKind->campaign->status ?? null,
                        'title' => $locale === 'ar' ? $inKind->campaign->title_ar : $inKind->campaign->title_en,
                    ],
                    'category' => [
                        'id' => $inKind->campaign->category->id ?? null,
                        'name' => $locale === 'ar'
                            ? $inKind->campaign->category->name_category_ar
                            : $inKind->campaign->category->name_category_en,
                    ],
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
