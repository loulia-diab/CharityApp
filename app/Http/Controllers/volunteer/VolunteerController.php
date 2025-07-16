<?php

namespace App\Http\Controllers\volunteer;

use App\Http\Controllers\Controller;
use App\Models\Volunteer;

class VolunteerController extends Controller
{
    // تطوعاتي من الحملات
    public function getVolunteerCampaigns($volunteerId)
    {
        $volunteer = Volunteer::find($volunteerId);

        $locale = app()->getLocale();

        if (!$volunteer) {
            return response()->json([
                'message' => $locale === 'ar' ? 'المتطوع غير موجود' : 'Volunteer not found',
                'status' => 404
            ], 404);
        }

        $titleField = "title_{$locale}";
        $descField = "description_{$locale}";

        $campaigns = $volunteer->campaigns()
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
            'message' => $locale === 'ar' ? 'تم جلب حملات المتطوع بنجاح' : 'Volunteer campaigns fetched successfully',
            'data' => $campaigns,
            'status' => 200
        ]);
    }


}
