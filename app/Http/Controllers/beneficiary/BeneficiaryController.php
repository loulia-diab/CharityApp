<?php

namespace App\Http\Controllers\beneficiary;

use App\Http\Controllers\Controller;
use App\Models\Beneficiary;

class BeneficiaryController extends Controller
{
    // استفاداتي من الحملات
    public function getBeneficiaryCampaigns($beneficiaryId)
    {
        $beneficiary = Beneficiary::with('campaigns')->find($beneficiaryId);

        if (!$beneficiary) {
            return response()->json(['message' => 'Beneficiary not found'], 404);
        }

        return response()->json([
            'message' => 'Campaigns fetched successfully',
            'data' => $beneficiary->campaigns,
            'status' => 200
        ]);
    }
}
