<?php

namespace App\Http\Controllers\Donation_Type\Sponsorship;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function createPlanForSponsorship(Request $request)
    {
        $request->validate([
            'sponsorship_id' => 'required|exists:sponsorships,id',
            'amount' => 'required|numeric|min:1',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $user = auth('user')->user();
        if (!$user) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }

        try {
            $plan = Plan::create([
                'user_id' => $user->id,
                'sponsorships_id' => $request->sponsorship_id,
                'amount' => $request->amount,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'is_activated' => false, // مبدئيًا
            ]);

            return response()->json([
                'message' => 'Plan created successfully for the sponsorship',
                'data' => $plan,
                'status' => 201,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating plan',
                'error' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function activatePlan($id)
    {
        $plan = Plan::findOrFail($id);
        $plan->is_activated = true;
        $plan->save();

        return response()->json([
            'message' => 'Plan activated successfully',
            'data' => $plan,
            'status' => 200
        ]);
    }

    public function deactivatePlan($id)
    {
        $plan = Plan::findOrFail($id);
        $plan->is_activated = false;
        $plan->save();

        return response()->json([
            'message' => 'Plan deactivated successfully',
            'data' => $plan,
            'status' => 200
        ]);
    }

    public function cancelPlan($id)
    {

    }

    // كفالاتي
    public function getPlansForUser($id)
    {

    }

    public function getPlanDetails($id)
    {

    }

}
