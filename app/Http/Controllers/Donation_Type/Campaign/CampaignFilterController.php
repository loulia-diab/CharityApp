<?php

namespace App\Http\Controllers\Donation_Type\Campaign;

use App\Enums\CampaignStatus;
use App\Http\Controllers\Controller;
use App\Models\Campaigns\Campaign;
use Illuminate\Http\Request;

class CampaignFilterController extends Controller
{
    public function filterCampaignsByDate(Request $request)
    {
        try {
            $validated = $request->validate([
                'from' => 'required|date',
                'to' => 'required|date|after_or_equal:from',
            ]);
            $locale = app()->getLocale();

            $campaigns = Campaign::whereBetween('start_date', [$validated['from'], $validated['to']])
                ->orWhereBetween('end_date', [$validated['from'], $validated['to']])
                ->paginate(10);

            $campaigns->getCollection()->transform(function ($campaign) use ($locale) {
                $campaign->status_label = CampaignStatus::from($campaign->status)->label($locale);
                return $campaign;
            });

            return response()->json([
                'from' => $validated['from'],
                'to' => $validated['to'],
                'message' => 'Campaigns fetched successfully',
                'data' => $campaigns->items(),
                'pagination' => [
                    'current_page' => $campaigns->currentPage(),
                    'last_page' => $campaigns->lastPage(),
                    'per_page' => $campaigns->perPage(),
                    'total' => $campaigns->total(),
                ],
                'status_code' => 200
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching campaigns',
                'error' => $e->getMessage(),
                'status_code' => 500
            ], 500);
        }
    }

    public function filterCampaignsByGoalAmount(Request $request)
    {
        try {
            $validated = $request->validate([
                'min' => 'required|numeric|min:0',
                'max' => 'required|numeric|gte:min',
            ]);
            $locale = app()->getLocale();

            $campaigns = Campaign::whereBetween('goal_amount', [$validated['min'], $validated['max']])
                ->paginate(10);

            $campaigns->getCollection()->transform(function ($campaign) use ($locale) {
                $campaign->status_label = CampaignStatus::from($campaign->status)->label($locale);
                return $campaign;
            });

            return response()->json([
                'min' => $validated['min'],
                'max' => $validated['max'],
                'message' => 'Campaigns fetched successfully',
                'data' => $campaigns->items(),
                'pagination' => [
                    'current_page' => $campaigns->currentPage(),
                    'last_page' => $campaigns->lastPage(),
                    'per_page' => $campaigns->perPage(),
                    'total' => $campaigns->total(),
                ],
                'status_code' => 200
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching campaigns',
                'error' => $e->getMessage(),
                'status_code' => 500
            ], 500);
        }
    }
    public function filterCampaignsByBeneficiariesCount(Request $request)
    {
        $validated = $request->validate([
            'min' => 'required|integer|min:0',
        ]);

        $campaigns = Campaign::withCount('beneficiaries')
            ->having('beneficiaries_count', '>=', $validated['min'])
            ->paginate(10);

        return response()->json([
            'message' => 'Campaigns filtered by beneficiaries count',
            'data' => $campaigns,
            'status' => 200
        ]);
    }
}
