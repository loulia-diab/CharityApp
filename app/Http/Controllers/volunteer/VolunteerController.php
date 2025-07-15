<?php

namespace App\Http\Controllers\volunteer;

use App\Http\Controllers\Controller;
use App\Models\Volunteer;

class VolunteerController extends Controller
{
    // تطوعاتي من الحملات
    public function getVolunteerCampaigns($volunteerId)
    {
        $volunteer = Volunteer::with('campaigns')->find($volunteerId);

        if (!$volunteer) {
            return response()->json(['message' => 'Volunteer not found'], 404);
        }

        return response()->json([
            'message' => 'Campaigns fetched successfully',
            'data' => $volunteer->campaigns,
            'status' => 200,
        ]);
    }

}
