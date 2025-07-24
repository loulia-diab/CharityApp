<?php

namespace App\Http\Controllers\beneficiary;

use App\Http\Controllers\Controller;
use App\Models\Beneficiary;
use Illuminate\Http\Request;

class BeneficiaryController extends Controller
{
    // استفاداتي
    public function getBeneficiaryCampaigns($beneficiaryId)
    {
        $user = null;
        $admin = null;
        if (auth()->guard('admin')->check()) {
            $admin = auth()->guard('admin')->user();
        } elseif (auth()->guard('api')->check()) {
            $user = auth()->guard('api')->user();
        } else {
            return response()->json([
                'message' => app()->getLocale() === 'ar' ? 'غير مصرح' : 'Unauthorized',
                'status' => 401
            ], 401);
        }
        $beneficiary =Beneficiary::find($beneficiaryId);

        if (!$beneficiary) {
            $locale = app()->getLocale();
            return response()->json([
                'message' => $locale === 'ar' ? 'المستفيد غير موجود' : 'Beneficiary not found',
                'status' => 404
            ], 404);
        }

        $locale = app()->getLocale();
        $titleField = "title_{$locale}";
        $descField = "description_{$locale}";

        $campaigns = $beneficiary->campaigns()
            ->whereHas('category', function ($q) {
                $q->where('main_category', 'Campaign');
            })
            ->select(
                'id',
                'category_id',
                "{$titleField} as title",
                "{$descField} as description",
                'image',
                'start_date',
                'end_date',
            )
            ->get()
            ->map(function ($campaign) use ($locale) {
                return $campaign;
            });

        return response()->json([
            'message' => $locale === 'ar' ? 'تم جلب حملات المستفيد بنجاح' : 'Beneficiary campaigns fetched successfully',
            'data' => $campaigns,
            'status' => 200
        ]);
    }
    public function getBeneficiaryHumanCases($beneficiaryId)
    {
        $user = null;
        $admin = null;
        if (auth()->guard('admin')->check()) {
            $admin = auth()->guard('admin')->user();
        } elseif (auth()->guard('api')->check()) {
            $user = auth()->guard('api')->user();
        } else {
            return response()->json([
                'message' => app()->getLocale() === 'ar' ? 'غير مصرح' : 'Unauthorized',
                'status' => 401
            ], 401);
        }
        $beneficiary = Beneficiary::find($beneficiaryId);

        $locale = app()->getLocale();

        if (!$beneficiary) {
            return response()->json([
                'message' => $locale === 'ar' ? 'المستفيد غير موجود' : 'Beneficiary not found',
                'status' => 404
            ], 404);
        }

        $titleField = "title_{$locale}";
        $descField = "description_{$locale}";

        $humanCases = \App\Models\HumanCase::with(['campaign.category'])
            ->where('beneficiary_id', $beneficiaryId)
            ->whereHas('campaign.category', function ($q) {
                $q->where('main_category', 'HumanCase');
            })
            ->get()
            ->map(function ($humanCase) use ($titleField, $descField) {
                $campaign = $humanCase->campaign;

                return [
                    'id' => $humanCase->id,
                    'is_emergency' => $humanCase->is_emergency,
                    'title' => $campaign?->getAttribute($titleField),
                    'description' => $campaign?->getAttribute($descField),
                    'category_id' => $campaign?->category_id,
                    'image' => $campaign?->image,
                    'start_date' => $campaign?->start_date,
                    'end_date' => $campaign?->end_date,
                ];
            });

        return response()->json([
            'message' => $locale === 'ar' ? 'تم جلب الحالات الإنسانية الخاصة بالمستفيد بنجاح' : 'Beneficiary human cases fetched successfully',
            'data' => $humanCases,
            'status' => 200
        ]);
    }
    public function getBeneficiarySponsorships($beneficiaryId)
    {
        $user = null;
      if (auth()->guard('api')->check()) {
            $user = auth()->guard('api')->user();
        } else {
            return response()->json([
                'message' => app()->getLocale() === 'ar' ? 'غير مصرح' : 'Unauthorized',
                'status' => 401
            ], 401);
        }
        $beneficiary = Beneficiary::find($beneficiaryId);

        $locale = app()->getLocale();

        if (!$beneficiary) {
            return response()->json([
                'message' => $locale === 'ar' ? 'المستفيد غير موجود' : 'Beneficiary not found',
                'status' => 404
            ], 404);
        }

        $titleField = "title_{$locale}";
        $descField = "description_{$locale}";

        $sponsorships = \App\Models\Sponsorship::with(['campaign.category'])
            ->where('beneficiary_id', $beneficiaryId)
            ->whereHas('campaign.category', function ($q) {
                $q->where('main_category', 'Sponsorship');
            })
            ->get()
            ->map(function ($sponsorships) use ($titleField, $descField) {
                $campaign = $sponsorships->campaign;

                return [
                    'id' => $sponsorships->id,
                    'title' => $campaign?->getAttribute($titleField),
                    'description' => $campaign?->getAttribute($descField),
                    'category_id' => $campaign?->category_id,
                    'image' => $campaign?->image,
                    'start_date' => $campaign?->start_date,
                    'end_date' => $campaign?->end_date,
                ];
            });

        return response()->json([
            'message' => $locale === 'ar' ? 'تم جلب الكفالات الخاصة بالمستفيد بنجاح' : 'Beneficiary sponsorships fetched successfully',
            'data' => $sponsorships,
            'status' => 200
        ]);
        // getBeneficiaryInKinds
    }

    public function getBeneficiaryInKinds()
    {

    }

    // استفاداتي من كلشي
    public function getBeneficiarySummary($beneficiaryId)
    {
        $locale = app()->getLocale();
        $titleField = "title_{$locale}";

        $beneficiary = Beneficiary::find($beneficiaryId);

        if (!$beneficiary) {
            return response()->json([
                'message' => 'Beneficiary not found',
                'status' => 404
            ], 404);
        }

        $beneficiaryName = $beneficiary->name;

        // حملات مرتبطة بشكل مباشر
        $campaigns = $beneficiary->campaigns()
            ->whereHas('category', fn($q) => $q->where('main_category', 'Campaign'))
            ->select('id', "{$titleField} as title", 'start_date')
            ->get()
            ->map(fn($campaign) => [
                'type' => 'campaign',
                'title' => $campaign->title,
                'date' => $campaign->start_date,
                'beneficiary_name' => $beneficiaryName,
            ]);

        // حالات إنسانية
        $humanCases = \App\Models\HumanCase::with('campaign')
            ->where('beneficiary_id', $beneficiaryId)
            ->whereHas('campaign.category', fn($q) => $q->where('main_category', 'HumanCase'))
            ->get()
            ->map(fn($case) => [
                'type' => 'human_case',
                'title' => $case->campaign?->$titleField,
                'amount' => $case->campaign?->goal_amount,
                'beneficiary_name' => $beneficiaryName,
            ]);

        // كفالات
        $sponsorships = \App\Models\Sponsorship::with('campaign')
            ->where('beneficiary_id', $beneficiaryId)
            ->whereHas('campaign.category', fn($q) => $q->where('main_category', 'Sponsorship'))
            ->get()
            ->map(fn($sponsorship) => [
                'type' => 'sponsorship',
                'title' => $sponsorship->campaign?->$titleField,
                'amount' => $sponsorship->campaign?->goal_amount,
                'beneficiary_name' => $beneficiaryName,
            ]);

        // تبرعات عينية
        $inKinds = $beneficiary->inKinds()
            ->with('category')
            ->get()
            ->map(fn($inKind) => [
                'type' => 'in_kind',
                'category' => $inKind->category?->name,
                'date' => $inKind->created_at->format('Y-m-d'),
                'beneficiary_name' => $beneficiaryName,
            ]);

        // دمج الكل
        $all = $campaigns
            ->concat($humanCases)
            ->concat($sponsorships)
            ->concat($inKinds)
            ->values();

        return response()->json([
            'message' => 'Beneficiary summary retrieved successfully',
            'data' => $all,
            'status' => 200
        ]);
    }

    public function getAllBeneficiaryActivities($beneficiaryId)
    {
        $user = auth()->guard('api')->user();
        if (!$user) {
            return response()->json([
                'message' => app()->getLocale() === 'ar' ? 'غير مصرح' : 'Unauthorized',
                'status' => 401
            ], 401);
        }

        $beneficiary = Beneficiary::find($beneficiaryId);
        if (!$beneficiary) {
            return response()->json([
                'message' => __('Beneficiary not found'),
                'status' => 404
            ], 404);
        }

        $locale = app()->getLocale();
        $titleField = "title_{$locale}";
        $descField = "description_{$locale}";

        $activities = [];

        // === الحملات ===
        $campaigns = $beneficiary->campaigns()
            ->whereHas('category', fn($q) => $q->where('main_category', 'Campaign'))
            ->get();

        foreach ($campaigns as $campaign) {
            $activities[] = [
                'type' => 'campaign',
                'beneficiary_name' => $beneficiary->name,
                'title' => $campaign->$titleField,
                'image' => $campaign->image,
                'start_date' => $campaign->start_date,
                'end_date' => $campaign->end_date,
                'amount' => null,
                'category_name' => null,
            ];
        }

        // === الحالات الإنسانية ===
        $humanCases = \App\Models\HumanCase::with('campaign.category')
            ->where('beneficiary_id', $beneficiaryId)
            ->get();

        foreach ($humanCases as $case) {
            $activities[] = [
                'type' => 'human_case',
                'beneficiary_name' => $beneficiary->name,
                'title' => $case->campaign?->$titleField,
                'image' => $case->campaign?->image,
                'start_date' => $case->campaign?->start_date,
                'end_date' => $case->campaign?->end_date,
                'amount' => $case->campaign?->goal_amount,
                'category_name' => null,
            ];
        }

        // === الكفالات ===
        $sponsorships = \App\Models\Sponsorship::with('campaign.category')
            ->where('beneficiary_id', $beneficiaryId)
            ->get();

        foreach ($sponsorships as $sponsorship) {
            $activities[] = [
                'type' => 'sponsorship',
                'beneficiary_name' => $beneficiary->name,
                'title' => $sponsorship->campaign?->$titleField,
                'image' => $sponsorship->campaign?->image,
                'start_date' => $sponsorship->campaign?->start_date,
                'end_date' => $sponsorship->campaign?->end_date,
                'amount' => $sponsorship->campaign?->goal_amount,
                'category_name' => null,
            ];
        }

        // === التبرعات العينية ===
        $inKinds = \App\Models\InKind::with('category')
            ->whereHas('beneficiaries', fn($q) => $q->where('beneficiary_id', $beneficiaryId))
            ->get();

        foreach ($inKinds as $inKind) {
            $activities[] = [
                'type' => 'in_kind',
                'beneficiary_name' => $beneficiary->name,
                'title' => $inKind->$titleField,
                'image' => $inKind->image,
                'start_date' => $inKind->start_date,
                'end_date' => $inKind->end_date,
                'amount' => null,
                'category_name' => $inKind->category?->{"name_{$locale}"},
            ];
        }

        return response()->json([
            'message' => $locale === 'ar' ? 'تم جلب كافة الاستفادات بنجاح' : 'All beneficiary activities fetched successfully',
            'data' => $activities,
            'status' => 200
        ]);
    }


}
