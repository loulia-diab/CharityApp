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
        $otp = '123456'; // كود التحقق الثابت

        $request->validate([
            'address' => 'required|string|max:255',
            'category_ids' => 'required|array|min:1',
            'category_ids.*' => 'exists:categories,id',
            'phone' => 'required|string|digits:10',  // بدون unique عشان نخزن الرقم مع كل تبرع
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
                'title_en' => $locale === 'en' ? $request->title : '',
                'title_ar' => $locale === 'ar' ? $request->title : '',
                'description_en' => '',
                'description_ar' => '',
                'status' => CampaignStatus::Pending->value,
                'goal_amount' => 0,
                'collected_amount' => 0,
            ]);
            $campaign->categories()->sync($request->category_ids);

            // إنشاء التبرع العيني وربطه بالحملة والتصنيفات
            $inKind = InKind::create([
                'user_id' => $user->id,
                'campaign_id' => $campaign->id,
                'address_en' => $locale === 'en' ? $request->address : null,
                'address_ar' => $locale === 'ar' ? $request->address : null,
                'phone' => $request->phone, // تخزين رقم الهاتف مع التبرع
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
    public function getAllUserInKinds()
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }

        $locale = app()->getLocale();

        $inKinds = InKind::with('campaign.categories')
            ->where('user_id', $user->id)
            ->get()
            ->map(function ($inKind) use ($locale) {
                return [
                    'id' => $inKind->id,
                    'address' => $locale === 'ar' ? $inKind->address_ar : $inKind->address_en,
                    'phone' => $inKind->phone,
                    'created_at' => $inKind->created_at->format('Y-m-d H:i'),
                    'categories' => $inKind->campaign->categories->map(function ($cat) use ($locale) {
                        return [
                            'name' => $locale === 'ar' ? $cat->name_ar : $cat->name_en,
                        ];
                    })->values(),
                ];
            });

        return response()->json([
            'message' => 'User in-kind donations retrieved successfully',
            'data' => $inKinds,
        ]);
    }


    // ADMIN

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



}
