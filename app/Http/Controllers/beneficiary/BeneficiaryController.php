<?php

namespace App\Http\Controllers\beneficiary;

use App\Http\Controllers\Controller;
use App\Models\Beneficiary;
use Illuminate\Http\Request;

class BeneficiaryController extends Controller
{
    // استفاداتي من الحملات
    // بدي وحدة للأدمن ووحدة لليوزر المستفيد/ المتطوع
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

}
