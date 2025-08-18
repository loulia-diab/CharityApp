<?php

namespace App\Http\Controllers\Donation_Type\Campaign;

use App\Http\Controllers\Controller;
use App\Models\Beneficiary;
use App\Models\Campaigns\Campaign;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class CampaignBeneficiaryController extends Controller
{
    public function addBeneficiariesToCampaign(Request $request, $campaignId)
    {
        $locale = app()->getLocale();

        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json([
                'message' => $locale === 'ar' ? 'غير مصرح' : 'Unauthorized'
            ], 401);
        }

        $request->validate([
            'beneficiary_ids' => 'required|array',
            'beneficiary_ids.*' => 'exists:beneficiaries,id',
        ]);

        try {
            $campaign = Campaign::with('category', 'beneficiaries')->findOrFail($campaignId);

            if (!$campaign->category || $campaign->category->main_category !== 'Campaign') {
                return response()->json([
                    'message' => $locale === 'ar' ? 'الحملة غير صحيحة أو ليست من نوع حملة' : 'Invalid or non-campaign category',
                ], 404);
            }

            // جلب المستفيدين المضافين مسبقًا
            $existingIds = $campaign->beneficiaries->pluck('id')->toArray();

            // تصفية المستفيدين الجدد فقط
            $newIds = array_diff($request->beneficiary_ids, $existingIds);

            if (empty($newIds)) {
                return response()->json([
                    'message' => $locale === 'ar' ? 'جميع المستفيدين مضافين مسبقًا' : 'All beneficiaries are already added',
                    'status' => 200,
                    'data' => []
                ]);
            }
            // تحضير بيانات الربط
            $syncData = [];
            foreach ($newIds as $id) {
                $syncData[$id] = ['admin_id' => $admin->id];
            }
// إضافة المستفيدين الجدد فقط
            $campaign->beneficiaries()->syncWithoutDetaching($syncData);

// تحديث is_stored لكل المستفيدين الجدد
            Beneficiary::whereIn('id', $newIds)->update(['is_sorted' => true]);
//  إرسال إشعار للمستخدم
            // إرسال إشعار لكل مستفيد جديد
            $notificationService = app()->make(\App\Services\NotificationService::class);

            foreach ($newIds as $beneficiaryId) {
                $beneficiary = Beneficiary::find($beneficiaryId);
                $user = User::find($beneficiary->user_id);

                if ($user) {
                    $title = [
                        'en' => $campaign->name_en,
                        'ar' => $campaign->name_ar,
                    ];

                    $body = [
                        'en' => "You have been successfully added to the campaign '{$campaign->name_en}'. You will be contacted for coordination.",
                        'ar' => "تمت إضافتك بنجاح إلى الحملة '{$campaign->name_ar}' لتحصل على الاستفادة، سيتم التواصل معك للتنسيق.",
                    ];

                    $notificationService->sendFcmNotification(new Request([
                        'user_id' => $user->id,
                        'title_en' => $title['en'],
                        'title_ar' => $title['ar'],
                        'body_en' => $body['en'],
                        'body_ar' => $body['ar'],
                    ]));
                }
            }
            return response()->json([
                'message' => $locale === 'ar' ? 'تمت إضافة المستفيدين الجدد بنجاح' : 'New beneficiaries added successfully',
                'status' => 200,
                'data' => array_values($newIds)
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => $locale === 'ar' ? 'الحملة غير موجودة' : 'Campaign not found',
                'status' => 404
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $locale === 'ar' ? 'حدث خطأ أثناء إضافة المستفيدين' : 'Error adding beneficiaries',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }

/*
    public function removeBeneficiariesFromCampaign(Request $request, $campaignId)
    {
        $locale = app()->getLocale();

        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json([
                'message' => $locale === 'ar' ? 'غير مصرح' : 'Unauthorized'
            ], 401);
        }

        $request->validate([
            'beneficiary_ids' => 'required|array',
            'beneficiary_ids.*' => 'exists:beneficiaries,id',
        ]);

        try {
            $campaign = Campaign::with('category')->findOrFail($campaignId);

            if (!$campaign->category || $campaign->category->main_category !== 'Campaign') {
                return response()->json([
                    'message' => $locale === 'ar' ? 'الحملة غير صحيحة أو ليست من نوع حملة' : 'Invalid or non-campaign category',
                ], 404);
            }

            $campaign->beneficiaries()->detach($request->beneficiary_ids);
            // تحديث is_stored لكل المستفيدين الجدد
            Beneficiary::whereIn('id', $newIds)->update(['is_sorted' => false]);

            //  إرسال إشعار للمستخدم
            // إرسال إشعار لكل مستفيد جديد
            $notificationService = app()->make(\App\Services\NotificationService::class);

            foreach ($newIds as $beneficiaryId) {
                $beneficiary = Beneficiary::find($beneficiaryId);
                $user = User::find($beneficiary->user_id);

                if ($user) {
                    $title = [
                        'en' => $campaign->name_en,
                        'ar' => $campaign->name_ar,
                    ];

                    $body = [
                        'en' => 'You have been successfully added to this campaign.',
                        'ar' => 'تم إلغاء قبول استفادتك إلى هذه الحملة لتحصل على الاستفادة، نعتذر منك، عسى أن تقبل في استفادة جديدة.',
                    ];
                    $notificationService->sendFcmNotification(new Request([
                        'user_id' => $user->id,
                        'title_en' => $title['en'],
                        'title_ar' => $title['ar'],
                        'body_en' => $body['en'],
                        'body_ar' => $body['ar'],
                    ]));
                }
            }

            return response()->json([
                'message' => $locale === 'ar' ? 'تمت إزالة المستفيدين بنجاح' : 'Beneficiaries removed successfully',
                'status' => 200,
                'data' => $request->beneficiary_ids
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => $locale === 'ar' ? 'الحملة غير موجودة' : 'Campaign not found',
                'status' => 404
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $locale === 'ar' ? 'حدث خطأ أثناء إزالة المستفيدين' : 'Error removing beneficiaries',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }

*/
    public function removeBeneficiariesFromCampaign(Request $request, $campaignId)
    {
        $locale = app()->getLocale();

        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json([
                'message' => $locale === 'ar' ? 'غير مصرح' : 'Unauthorized'
            ], 401);
        }

        $request->validate([
            'beneficiary_ids' => 'required|array',
            'beneficiary_ids.*' => 'exists:beneficiaries,id',
        ]);

        try {
            $campaign = Campaign::with('category')->findOrFail($campaignId);

            if (!$campaign->category || $campaign->category->main_category !== 'Campaign') {
                return response()->json([
                    'message' => $locale === 'ar' ? 'الحملة غير صحيحة أو ليست من نوع حملة' : 'Invalid or non-campaign category',
                ], 404);
            }

            // Detach beneficiaries
            $campaign->beneficiaries()->detach($request->beneficiary_ids);

            // Update is_sorted = false for removed beneficiaries
            Beneficiary::whereIn('id', $request->beneficiary_ids)->update(['is_sorted' => false]);

            // Send notifications
            $notificationService = app()->make(\App\Services\NotificationService::class);

            foreach ($request->beneficiary_ids as $beneficiaryId) {
                $beneficiary = Beneficiary::find($beneficiaryId);
                $user = User::find($beneficiary->user_id);

                if ($user) {
                    $title = [
                        'en' => $campaign->name_en,
                        'ar' => $campaign->name_ar,
                    ];

                    $body = [
                        'en' => "Your participation in the campaign '{$campaign->name_en}' has been canceled. We are sorry, and we hope you will be accepted in another opportunity soon.",
                        'ar' => "تم إلغاء قبول استفادتك من هذه الحملة '{$campaign->name_ar}'، نعتذر منك، عسى أن تقبل في استفادة جديدة قريبًا.",
                    ];

                    $notificationService->sendFcmNotification(new Request([
                        'user_id'   => $user->id,
                        'title_en'  => $title['en'],
                        'title_ar'  => $title['ar'],
                        'body_en'   => $body['en'],
                        'body_ar'   => $body['ar'],
                    ]));
                }
            }

            return response()->json([
                'message' => $locale === 'ar' ? 'تمت إزالة المستفيدين بنجاح' : 'Beneficiaries removed successfully',
                'status'  => 200,
                'data'    => $request->beneficiary_ids
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => $locale === 'ar' ? 'الحملة غير موجودة' : 'Campaign not found',
                'status' => 404
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $locale === 'ar' ? 'حدث خطأ أثناء إزالة المستفيدين' : 'Error removing beneficiaries',
                'error'   => $e->getMessage(),
                'status'  => 500
            ], 500);
        }
    }

    public function getCampaignBeneficiaries($campaignId)
    {
        $locale = app()->getLocale();

        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json([
                'message' => $locale === 'ar' ? 'غير مصرح' : 'Unauthorized'
            ], 401);
        }

        $campaign = Campaign::with(['beneficiaries.beneficiary_request', 'category'])->find($campaignId);

        if (!$campaign || !$campaign->category || $campaign->category->main_category !== 'Campaign') {
            return response()->json([
                'message' => $locale === 'ar' ? 'الحملة غير موجودة أو غير صالحة' : 'Campaign not found or invalid',
                'status' => 404
            ], 404);
        }

        $beneficiaries = $campaign->beneficiaries->map(function ($beneficiary) use ($locale) {
            $request = $beneficiary->beneficiary_request;

            return [
                'id' => $beneficiary->id,
                'user_id' => $request?->user_id,
                'name' => $locale === 'ar' ? $request?->name_ar : $request?->name_en,
                'phone' => $request?->phone,
                'email' => $request?->user?->email, // من جدول users
                'gender' => $locale === 'ar' ? $request?->gender_ar : $request?->gender_en,
                'birth_date' => $request?->birth_date,
                'address' => $locale === 'ar' ? $request?->address_ar : $request?->address_en,
            ];
        });

        return response()->json([
            'message' => $locale === 'ar' ? 'تم جلب المستفيدين بنجاح' : 'Beneficiaries fetched successfully',
            'data' => $beneficiaries,
            'status' => 200
        ]);
    }



}
