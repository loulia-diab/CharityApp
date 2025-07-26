<?php

namespace App\Http\Controllers\Donation_Type\Campaign;

use App\Http\Controllers\Controller;
use App\Models\Campaigns\Campaign;
use Illuminate\Http\Request;

class CampaignVolunteerController extends Controller
{
    public function addVolunteersToCampaign(Request $request, $campaignId)
    {
        $locale = app()->getLocale();

        $request->validate([
            'volunteer_ids' => 'required|array',
            'volunteer_ids.*' => 'exists:volunteers,id',
        ]);

        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json([
                'message' => $locale === 'ar' ? 'غير مصرح' : 'Unauthorized',
            ], 401);
        }

        $campaign = Campaign::with('category')->find($campaignId);
        if (!$campaign || !$campaign->category || $campaign->category->main_category !== 'Campaign') {
            return response()->json([
                'message' => $locale === 'ar' ? 'الحملة غير موجودة أو ليست من نوع حملة' : 'Campaign not found or not of type Campaign',
            ], 404);
        }

        try {
            foreach ($request->volunteer_ids as $volunteerId) {
                $campaign->volunteers()->syncWithoutDetaching([$volunteerId => ['admin_id' => $admin->id]]);
            }

            return response()->json([
                'message' => $locale === 'ar' ? 'تمت إضافة المتطوعين بنجاح' : 'Volunteers added successfully',
                'status' => 200,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $locale === 'ar' ? 'حدث خطأ أثناء إضافة المتطوعين' : 'Error adding volunteers',
                'error' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }


    public function getCampaignVolunteers($campaignId)
    {
        $locale = app()->getLocale();

        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json([
                'message' => $locale === 'ar' ? 'غير مصرح' : 'Unauthorized',
            ], 401);
        }

        $campaign = Campaign::with('volunteers', 'category')->find($campaignId);
        if (!$campaign || !$campaign->category || $campaign->category->main_category !== 'Campaign') {
            return response()->json([
                'message' => $locale === 'ar' ? 'الحملة غير موجودة أو ليست من نوع حملة' : 'Campaign not found or not of type Campaign',
            ], 404);
        }

        return response()->json([
            'message' => $locale === 'ar' ? 'تم جلب المتطوعين بنجاح' : 'Volunteers fetched successfully',
            'data' => $campaign->volunteers,
            'status' => 200,
        ]);
    }


    public function removeVolunteersFromCampaign(Request $request, $campaignId)
    {
        $locale = app()->getLocale();

        $request->validate([
            'volunteer_ids' => 'required|array',
            'volunteer_ids.*' => 'exists:volunteers,id',
        ]);

        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json([
                'message' => $locale === 'ar' ? 'غير مصرح' : 'Unauthorized',
            ], 401);
        }

        $campaign = Campaign::with('category')->find($campaignId);
        if (!$campaign || !$campaign->category || $campaign->category->main_category !== 'Campaign') {
            return response()->json([
                'message' => $locale === 'ar' ? 'الحملة غير موجودة أو ليست من نوع حملة' : 'Campaign not found or not of type Campaign',
            ], 404);
        }

        try {
            $campaign->volunteers()->detach($request->volunteer_ids);

            return response()->json([
                'message' => $locale === 'ar' ? 'تمت إزالة المتطوعين بنجاح' : 'Volunteers removed successfully',
                'status' => 200,
                'data' => $request->volunteer_ids
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $locale === 'ar' ? 'حدث خطأ أثناء إزالة المتطوعين' : 'Error removing volunteers',
                'error' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }


}
