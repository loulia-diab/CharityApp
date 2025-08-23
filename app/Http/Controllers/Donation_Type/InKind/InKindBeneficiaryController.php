<?php

namespace App\Http\Controllers\Donation_Type\InKind;

use App\Http\Controllers\Controller;
use App\Models\Beneficiary;
use App\Models\InKind;
use Illuminate\Http\Request;

class InKindBeneficiaryController extends Controller
{
    // اضافة مستفيدين
    /*
    public function addBeneficiariesToInKind(Request $request, $inKindId) {
        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json([
                'message' => 'Unauthorized - Admin access only',
            ], 401);
        }

        $request->validate([
            'beneficiary_ids' => 'required|array|min:1',
            'beneficiary_ids.*' => 'exists:beneficiaries,id',
        ]);

        $inKind = InKind::find($inKindId);

        if (!$inKind) {
            return response()->json([
                'message' => 'In-kind donation not found',
            ], 404);
        }

        $inKind->beneficiaries()->syncWithoutDetaching($request->beneficiary_ids);

// تحديث is_stored لكل المستفيدين المضافين
        Beneficiary::whereIn('id', $request->beneficiary_ids)
            ->update(['is_sorted' => true]);

// جلب المستفيدين بعد التحديث
        $inKind->load('beneficiaries');

        return response()->json([
            'message' => 'Beneficiaries added to in-kind donation successfully',
            'data' => $inKind->beneficiaries,
        ]);

    }
*/
    public function addBeneficiariesToInKind(Request $request, $inKindId)
    {
        $locale = app()->getLocale();

        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json([
                'message' => $locale === 'ar' ? 'غير مصرح' : 'Unauthorized - Admin access only',
            ], 401);
        }

        $request->validate([
            'beneficiary_ids' => 'required|array|min:1',
            'beneficiary_ids.*' => 'exists:beneficiaries,id',
        ]);

        $inKind = InKind::find($inKindId);
        if (!$inKind) {
            return response()->json([
                'message' => $locale === 'ar' ? 'التبرع العيني غير موجود' : 'In-kind donation not found',
            ], 404);
        }

        try {
            DB::transaction(function () use ($request, $inKind, $admin) {
                // إضافة المستفيدين دون حذف الموجودين
                $inKind->beneficiaries()->syncWithoutDetaching($request->beneficiary_ids);

                // تحديث is_sorted للمستفيدين الجدد
                Beneficiary::whereIn('id', $request->beneficiary_ids)
                    ->update(['is_sorted' => true]);

                // إرسال إشعار لكل مستفيد جديد
                $notificationService = app()->make(\App\Services\NotificationService::class);

                foreach ($request->beneficiary_ids as $beneficiaryId) {
                    $beneficiary = Beneficiary::find($beneficiaryId);
                    if ($beneficiary && $beneficiary->user_id) {
                        try {
                            $title = [
                                'en' => "New In-Kind Donation Assigned",
                                'ar' => "تم تخصيص تبرع عيني جديد لك",
                            ];

                            $body = [
                                'en' => "You have been added as a beneficiary to an in-kind donation.",
                                'ar' => "تمت إضافتك كمستفيد من تبرع عيني.",
                            ];

                            $notificationService->sendFcmNotification(new \Illuminate\Http\Request([
                                'user_id'  => $beneficiary->user_id,
                                'title_en' => $title['en'],
                                'title_ar' => $title['ar'],
                                'body_en'  => $body['en'],
                                'body_ar'  => $body['ar'],
                                'type'     => 'in_kind',
                                'item_id'  => $inKind->id,
                            ]));
                        } catch (\Exception $e) {
                            \Log::error("Failed to send notification to user #{$beneficiary->user_id}: " . $e->getMessage());
                        }
                    }
                }
            });

            // إعادة تحميل المستفيدين بعد الإضافة
            $inKind->load('beneficiaries');

            return response()->json([
                'message' => $locale === 'ar' ? 'تمت إضافة المستفيدين بنجاح للتبرع العيني' : 'Beneficiaries added to in-kind donation successfully',
                'data' => $inKind->beneficiaries,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $locale === 'ar' ? 'حدث خطأ أثناء إضافة المستفيدين' : 'Error adding beneficiaries to in-kind donation',
                'error' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }


    // جلب مستفيدين تبرع عيني لحالو
    public function getInKindBeneficiaries($inKindId) {
        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json([
                'message' => 'Unauthorized - Admin access only',
            ], 401);
        }

        $locale = app()->getLocale(); // الحصول على لغة التطبيق

        $inKind = InKind::with('beneficiaries.beneficiary_request')->find($inKindId);

        if (!$inKind) {
            return response()->json([
                'message' => 'In-kind donation not found',
            ], 404);
        }

        return response()->json([
            'message' => 'Beneficiaries retrieved successfully',
            'data' => $inKind->beneficiaries->map(function ($beneficiary) use ($locale) {
                return [
                    'id' => $beneficiary->id,
                    'name' => $beneficiary->beneficiary_request?->{"name_{$locale}"} ?? null,
                    // إذا بدك تضيف العمر أو الجنس أو أي حقل آخر من المستفيد:
                    // 'age' => $beneficiary->age,
                    // 'gender' => $beneficiary->gender,
                ];
            }),
        ]);
    }



    // غير مستخدمة
    // جلب مستفيدين تبرعات عينية حسب التصنيف المعين
    public function getBeneficiariesByCategory($categoryId) {
        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $locale = app()->getLocale();

        // جلب التبرعات العينية اللي حملاتها مرتبطة بالتصنيف المعطى
        $inKinds = InKind::whereHas('campaign.categories', function($q) use ($categoryId) {
            $q->where('id', $categoryId);
        })->with(['beneficiaries'])->get();

        // جمع كل المستفيدين من التبرعات العينية
        $beneficiaries = $inKinds->flatMap(function($inKind) {
            return $inKind->beneficiaries;
        })->unique('id')->values();

        // ترجع بيانات المستفيدين مع التنسيق
        $result = $beneficiaries->map(function($beneficiary) use ($locale) {
            return [
                'id' => $beneficiary->id,
                'name' => $beneficiary->name,  // إذا في أسماء بلغتين عدل هنا
                // ممكن تضيف حقول أخرى حسب الحاجة
            ];
        });

        return response()->json([
            'message' => 'Beneficiaries filtered by category retrieved successfully',
            'data' => $result,
        ]);
    }


}
