<?php

namespace App\Http\Controllers\Donation_Type\Campaign;

use App\Http\Controllers\Controller;
use App\Models\Campaigns\Campaign;
use App\Models\User;
use App\Models\Volunteer;
use Illuminate\Http\Request;

class CampaignVolunteerController extends Controller
{
    /*
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
*/
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

            // إرسال إشعار لكل متطوع جديد
            $notificationService = app()->make(\App\Services\NotificationService::class);

            foreach ($request->volunteer_ids as $volunteerId) {
                $volunteer = Volunteer::find($volunteerId);
                $user = User::find($volunteer->user_id);

                if ($user) {
                    $title = [
                        'en' => $campaign->name_en,
                        'ar' => $campaign->name_ar,
                    ];

                    $body = [
                        'en' => "You have been successfully added as a volunteer to the campaign '{$campaign->name_en}'. You will be contacted for coordination.",
                        'ar' => "تمت إضافتك بنجاح كمتطوع في الحملة '{$campaign->name_ar}'، سيتم التواصل معك للتنسيق.",
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

            // إرسال إشعار لكل متطوع تم إزالة مشاركته
            $notificationService = app()->make(\App\Services\NotificationService::class);

            foreach ($request->volunteer_ids as $volunteerId) {
                $volunteer = Volunteer::find($volunteerId);
                $user = User::find($volunteer->user_id);

                if ($user) {
                    $title = [
                        'en' => $campaign->name_en,
                        'ar' => $campaign->name_ar,
                    ];

                    $body = [
                        'en' => "Your volunteer participation in the campaign '{$campaign->name_en}' has been canceled. We are sorry, and we hope you will join another opportunity soon.",
                        'ar' => "تم إلغاء مشاركتك كمتطوع في الحملة '{$campaign->name_ar}'، نعتذر منك، عسى أن تشارك في فرصة أخرى قريبًا.",
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
                'message' => $locale === 'ar' ? 'تمت إزالة المتطوعين بنجاح' : 'Volunteers removed successfully',
                'status' => 200,
                'data' => $request->volunteer_ids
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $locale === 'ar' ? 'حدث خطأ أثناء إزالة المتطوعين' : 'Error removing volunteers',
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




}
