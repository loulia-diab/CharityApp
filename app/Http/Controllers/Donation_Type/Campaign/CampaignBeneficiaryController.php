<?php

namespace App\Http\Controllers\Donation_Type\Campaign;

use App\Http\Controllers\Controller;
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
            $campaign = Campaign::with('category')->findOrFail($campaignId);

            if (!$campaign->category || $campaign->category->main_category !== 'Campaign') {
                return response()->json([
                    'message' => $locale === 'ar' ? 'الحملة غير صحيحة أو ليست من نوع حملة' : 'Invalid or non-campaign category',
                ], 404);
            }

            // نحافظ على الموجود ونضيف الجدد
            $syncData = [];
            foreach ($request->beneficiary_ids as $id) {
                $syncData[$id] = ['admin_id' => $admin->id];
            }
            $campaign->beneficiaries()->syncWithoutDetaching($syncData);

            return response()->json([
                'message' => $locale === 'ar' ? 'تمت إضافة المستفيدين بنجاح' : 'Beneficiaries added successfully',
                'status' => 200,
                'data' => ''
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

    public function removeBeneficiaryFromCampaign($campaignId, $beneficiaryId)
    {
        $locale = app()->getLocale();

        $campaign = Campaign::with('category')->find($campaignId);

        if (!$campaign || !$campaign->category || $campaign->category->main_category !== 'Campaign') {
            return response()->json([
                'message' => $locale === 'ar' ? 'الحملة غير موجودة أو غير صالحة' : 'Campaign not found or invalid',
                'status' => 404
            ], 404);
        }

        $campaign->beneficiaries()->detach($beneficiaryId);

        return response()->json([
            'message' => $locale === 'ar' ? 'تمت إزالة المستفيد من الحملة بنجاح' : 'Beneficiary removed from campaign',
            'status' => 200
        ]);
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

        $campaign = Campaign::with(['beneficiaries', 'category'])->find($campaignId);

        if (!$campaign || !$campaign->category || $campaign->category->main_category !== 'Campaign') {
            return response()->json([
                'message' => $locale === 'ar' ? 'الحملة غير موجودة أو غير صالحة' : 'Campaign not found or invalid',
                'status' => 404
            ], 404);
        }

        return response()->json([
            'message' => $locale === 'ar' ? 'تم جلب المستفيدين بنجاح' : 'Beneficiaries fetched successfully',
            'data' => $campaign->beneficiaries,
            'status' => 200
        ]);
    }




}
