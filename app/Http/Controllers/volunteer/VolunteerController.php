<?php

namespace App\Http\Controllers\volunteer;

use App\Http\Controllers\Controller;
use App\Models\Volunteer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class VolunteerController extends Controller
{
    // تطوعاتي من الحملات

    public function getVolunteerCampaigns(Request $request)
    {
        $user = auth()->user();

        // الخطوة 1: الحصول على المتطوع المرتبط بهذا المستخدم + علاقات إضافية
        $volunteer = Volunteer::with('volunteer_request.types') // <-- مضافة هون
        ->where('user_id', $user->id)
            ->first();

        if (!$volunteer) {
            return response()->json([
                'message' => 'المستخدم ليس متطوعًا بعد.'
            ], 404);
        }

        // الخطوة 2: جلب الحملات المرتبطة بالمتطوع مع تفاصيل إضافية
        $campaigns = $volunteer->campaigns()->with(['category'])->get();

        // الخطوة 3: تنسيق البيانات للإرجاع
        $locale = App::getLocale();

        $data = $campaigns->map(function ($campaign) use ($volunteer, $locale) {
            $pivot = $campaign->pivot;

            return [
                'campaign_id'      => $campaign->id,
                'campaign_title'   => $locale === 'ar' ? $campaign->title_ar : $campaign->title_en,
                'campaign_image'   => $campaign->image ?? null,
                'campaign_date'    => $campaign->start_date,

                'volunteer_id'     => $volunteer->id,
                'volunteer_name'   => $volunteer->volunteer_request
                    ? ($locale === 'ar' ? $volunteer->volunteer_request->full_name_ar : $volunteer->volunteer_request->full_name_en)
                    : null,

                'volunteering_type' => $volunteer->volunteer_request && $volunteer->volunteer_request->types->count()
                    ? $volunteer->volunteer_request->types->map(function ($type) use ($locale) {
                        return $locale === 'ar' ? $type->name_ar : $type->name_en;
                    })->values()
                    : [],
            ];
        });

        return response()->json([
            'campaigns' => $data,
        ]);
    }


}
