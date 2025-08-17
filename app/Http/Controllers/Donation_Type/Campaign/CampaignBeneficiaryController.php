<?php

namespace App\Http\Controllers\Donation_Type\Campaign;

use App\Http\Controllers\Controller;
use App\Models\Beneficiary;
use App\Models\Campaigns\Campaign;
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
