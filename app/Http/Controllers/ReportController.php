<?php

namespace App\Http\Controllers;

use App\Models\Beneficiary;
use App\Models\Campaigns\Campaign;
use App\Models\Report;
use App\Models\Sponsorship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    // USER
    public function  getUserReports()
    {
        $locale = app()->getLocale();
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'message' => $locale === 'ar' ? 'المستخدم غير مسجل الدخول.' : 'User not authenticated.',
                'status_code' => 401
            ], 401);
        }
        try {
            // 1. تقارير الحملات التي تم التبرع لها من قبل المستخدم
            $campaignReports = Report::whereHas('campaign.transactions', function($q) use ($user) {
                $q->where('transactions.user_id', $user->id);
            })
                ->with(['campaign' => fn($q) => $q->select('id', "title_$locale as title", 'image')])
                ->get();
/*
            // 2. تقارير الحملات التي تطوع فيها المستخدم
            $volunteerReports = Report::whereHas('campaign.volunteers.user', function($q) use ($user) {
                $q->where('users.id', $user->id);
            })
                ->with(['campaign' => fn($q) => $q->select('id', "title_$locale as title", 'image')])
                ->get();
*/
            // 3. تقارير الكفالات التي يملكها المستخدم
            $sponsorshipReports = Report::whereHas('sponsorship.plans', function($q) use ($user) {
                $q->where('plans.user_id', $user->id);
            })
                ->with(['sponsorship' => fn($q) => $q->select('id', "name_$locale as name", 'image')])
                ->get();

            // دمج كل النتائج وترتيبها حسب التاريخ
            $reports = $campaignReports
               // ->merge($volunteerReports)
                ->merge($sponsorshipReports)
                ->sortByDesc('created_at')
                ->values();

            return response()->json([
                'message' => $locale === 'ar' ? 'تم جلب التقارير بنجاح' : 'Reports fetched successfully',
                'data' => $reports,
                'status_code' => 200
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $locale === 'ar' ? 'حدث خطأ أثناء جلب التقارير' : 'Error fetching reports',
                'error' => $e->getMessage(),
                'status_code' => 500
            ], 500);
        }
    }

    //ADMIN
    public function addReport(Request $request)
    {
        $locale = app()->getLocale();
        $admin = auth('admin')->user();

        if (!$admin) {
            return response()->json([
                'message' => $locale === 'ar' ? 'غير مصرح لك' : 'Unauthorized',
                'status_code' => 401
            ], 401);
        }

        $request->validate([
            'file' => 'required|mimes:pdf|max:10240', // PDF بحد أقصى 10MB
            'campaign_id' => 'nullable|exists:campaigns,id',
            'sponsorship_id' => 'nullable|exists:sponsorships,id',
        ]);

        if ((!$request->campaign_id && !$request->sponsorship_id) ||
            ($request->campaign_id && $request->sponsorship_id)) {
            return response()->json([
                'message' => $locale === 'ar' ? 'يجب اختيار حملة أو كفالة واحدة فقط' : 'You must select either a campaign or a sponsorship',
                'status_code' => 422
            ], 422);
        }

        try {
            // خزّن الملف في storage/app/public/reports
            $path = $request->file('file')->store('reports', 'public');

            // خزّن المسار بقاعدة البيانات
            $report = Report::create([
                'campaign_id'    => $request->campaign_id,
                'sponsorship_id' => $request->sponsorship_id,
                'file_url'       => "storage/$path" // المسار العام بعد storage:link
            ]);

            return response()->json([
                'message' => $locale === 'ar' ? 'تم رفع التقرير بنجاح' : 'Report uploaded successfully',
                'data' => $report,
                'status_code' => 201
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $locale === 'ar' ? 'حدث خطأ أثناء رفع التقرير' : 'Error uploading report',
                'error' => $e->getMessage(),
                'status_code' => 500
            ], 500);
        }
    }

    public function getAdminReports()
    {
        $reports = Report::all();

        return response()->json([
            'message' => 'تم جلب جميع التقارير بنجاح',
            'data' => $reports
        ]);
    }


}
