<?php

namespace App\Http\Controllers\Donation_Type\Campaign;

use App\Http\Controllers\Controller;
use App\Models\Campaigns\Campaign;
use Illuminate\Http\Request;

class CampaignBeneficiaryController extends Controller
{
    public function addBeneficiariesToCampaign(Request $request, $campaignId)
    {
        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'beneficiary_ids' => 'required|array',
            'beneficiary_ids.*' => 'exists:beneficiaries,id',
        ]);

        try {
            $campaign = Campaign::findOrFail($campaignId);

            // نضيف أو نحتفظ بالمستفيدين بدون حذف الموجودين سابقاً
            $campaign->beneficiaries()->syncWithoutDetaching(
                array_map(fn($id) => ['admin_id' => $admin->id], $request->beneficiary_ids)
            );

            return response()->json([
                'message' => 'Beneficiaries added successfully',
                'status' => 200,
                'data' => '',
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Campaign not found', 'status' => 404], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error adding beneficiaries',
                'error' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function removeBeneficiaryFromCampaign($campaignId, $beneficiaryId)
    {
        $campaign = Campaign::find($campaignId);

        if (!$campaign) {
            return response()->json(['message' => 'Campaign not found'], 404);
        }

        $campaign->beneficiaries()->detach($beneficiaryId);

        return response()->json([
            'message' => 'Beneficiary removed from campaign',
            'status' => 200
        ]);
    }
    public function getCampaignWithBeneficiaries($campaignId)
    {
        $locale = app()->getLocale();
        $titleField = "title_{$locale}";
        $descField = "description_{$locale}";

        $campaign = Campaign::select(
            'id',
            'category_id',
            "{$titleField} as title",
            "{$descField} as description",
            'image',
            'goal_amount',
            'collected_amount',
            'start_date',
            'end_date',
            'status'
        )
            ->with(['beneficiaries']) // جيب المستفيدين مع الحملة
            ->find($campaignId);

        if (!$campaign) {
            return response()->json([
                'message' => 'Campaign not found',
                'status' => 404
            ], 404);
        }

        return response()->json([
            'message' => 'Campaign and beneficiaries fetched successfully',
            'data' => $campaign,
            'status' => 200
        ]);
    }
}
