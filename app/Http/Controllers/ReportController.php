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

    public function getUserReports()
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => __('messages.unauthenticated'),
                'data' => []
            ], 401);
        }

        $locale = app()->getLocale();

        // جيب IDs الحملات/الكفالات يلي تبرع فيها المستخدم
        $donatedCampaigns = $user->transactions()
            ->whereNotNull('campaign_id')
            ->pluck('campaign_id')
            ->toArray();

        $donatedSponsorships = $user->plans()
            ->whereNotNull('sponsorship_id')
            ->pluck('sponsorship_id')
            ->toArray();

        // إذا ما في تبرعات لا حملات ولا كفالات
        if (empty($donatedCampaigns) && empty($donatedSponsorships)) {
            return response()->json([
                'status' => true,
                'message' => __('messages.no_reports'),
                'data' => []
            ]);
        }

        // جيب تقارير الحملات والكفالات فقط يلي المستخدم متبرع فيهن
        $reports = Report::with([
            'campaign' => fn($q) => $q->select('id', "title_$locale as title", 'image'),
            // هنا نجيب campaign المرتبط بالكفالة بدل title من sponsorship مباشرة
            'sponsorship.campaign' => fn($q) => $q->select('id', "title_$locale as title", 'image'),
        ])
            ->where(function ($q) use ($donatedCampaigns, $donatedSponsorships) {
                $q->whereIn('campaign_id', $donatedCampaigns)
                    ->orWhereIn('sponsorship_id', $donatedSponsorships);
            })
            ->get();

        if ($reports->isEmpty()) {
            return response()->json([
                'status' => true,
                'message' => __('messages.no_reports'),
                'data' => []
            ]);
        }

        $data = $reports->map(function ($report) {
            if ($report->campaign) {
                $type = 'campaign';
                $title = $report->campaign->title;
                $image = $report->campaign->image;
            } elseif ($report->sponsorship && $report->sponsorship->campaign) {
                $type = 'sponsorship';
                $title = $report->sponsorship->campaign->title;
                $image = $report->sponsorship->campaign->image;
            } else {
                $type = 'unknown';
                $title = '---';
                $image = null;
            }

            return [
                'id' => $report->id,
                'type' => $type,
                'title' => $title,
                'image' => $image,
                'created_at' => $report->created_at,
            ];
        });

        return response()->json([
            'message' => __('messages.reports_retrieved'),
            'data' => $data
        ]);
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
