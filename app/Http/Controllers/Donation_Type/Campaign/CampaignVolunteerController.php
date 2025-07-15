<?php

namespace App\Http\Controllers\Donation_Type\Campaign;

use App\Http\Controllers\Controller;
use App\Models\Campaigns\Campaign;
use Illuminate\Http\Request;

class CampaignVolunteerController extends Controller
{
    public function addVolunteersToCampaign(Request $request, $campaignId)
    {
        $request->validate([
            'volunteer_ids' => 'required|array',
            'volunteer_ids.*' => 'exists:volunteers,id',
        ]);

        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $campaign = Campaign::find($campaignId);
        if (!$campaign) {
            return response()->json(['message' => 'Campaign not found'], 404);
        }

        try {
            foreach ($request->volunteer_ids as $volunteerId) {
                $campaign->volunteers()->syncWithoutDetaching([$volunteerId => ['admin_id' => $admin->id]]);
            }

            return response()->json([
                'message' => 'Volunteers added successfully',
                'status' => 200,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error adding volunteers',
                'error' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function getCampaignWithVolunteers($campaignId)
    {
        $campaign = Campaign::with('volunteers')->find($campaignId);

        if (!$campaign) {
            return response()->json(['message' => 'Campaign not found'], 404);
        }

        return response()->json([
            'message' => 'Volunteers fetched successfully',
            'data' => $campaign->volunteers,
            'status' => 200,
        ]);
    }

    public function removeVolunteerFromCampaign($campaignId, $volunteerId)
    {
        $campaign = Campaign::find($campaignId);

        if (!$campaign) {
            return response()->json(['message' => 'Campaign not found'], 404);
        }

        $campaign->volunteers()->detach($volunteerId);

        return response()->json([
            'message' => 'Volunteer removed successfully',
            'status' => 200,
        ]);
    }
}
