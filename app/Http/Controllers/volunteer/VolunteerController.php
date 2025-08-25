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
        $locale = App::getLocale();

        // الحصول على المتطوع المرتبط بالمستخدم + علاقات إضافية
        $volunteer = Volunteer::with('volunteer_request.types')
            ->where('user_id', $user->id)
            ->first();

        // إذا المستخدم مش متطوع، نرجع قائمة فاضية
        if (!$volunteer) {
            return response()->json([
                'campaigns' => [],
            ]);
        }

        // جلب الحملات المرتبطة بالمتطوع
        $campaigns = $volunteer->campaigns()->with(['category'])->get();

        // تنسيق البيانات للإرجاع
        $data = $campaigns->map(function ($campaign) use ($volunteer, $locale) {
            return [
                'campaign_id' => $campaign->id,
                'campaign_title' => $locale === 'ar' ? $campaign->title_ar : $campaign->title_en,
                'campaign_image' => $campaign->image ?? null,
                'campaign_date' => $campaign->start_date,
                'volunteer_id' => $volunteer->id,
                'volunteer_name' => $volunteer->volunteer_request
                    ? ($locale === 'ar' ? $volunteer->volunteer_request->full_name_ar : $volunteer->volunteer_request->full_name_en)
                    : null,
                'volunteering_type' => $volunteer->volunteer_request && $volunteer->volunteer_request->types->count()
                    ? $volunteer->volunteer_request->types->map(function ($type) use ($locale) {
                        return $locale === 'ar' ? $type->name_ar : $type->name_en;
                    })->values()
                    : [],
            ];
        });

        // حتى لو ما عنده أي حملات، نرجع قائمة فاضية
        return response()->json([
            'campaigns' => $data->values(),
        ]);
    }


    public function getAllVolunteers(Request $request)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $locale = app()->getLocale();

        $volunteers = Volunteer::with(['volunteer_request.types'])
            ->get()
            ->map(function ($volunteer) use ($locale) {
                return [
                    'id' => $volunteer->id,
                    'name' => $volunteer->volunteer_request->{'full_name_' . $locale},
                    'phone' => $volunteer->volunteer_request->phone,
                    'preferred_times' => $volunteer->volunteer_request->{'preferred_times_' . $locale},
                    'preferred_types' => $volunteer->volunteer_request->types->map(function ($type) use ($locale) {
                        return $type->{'name_' . $locale};
                    })->toArray(),
                ];
            });

        return response()->json($volunteers);
    }

    /*
    public function getVolunteerById($id)
    {
        $locale = app()->getLocale();

        // جلب المتطوع مع طلب التطوع وأنواع التطوع + الحملات المرتبطة
        $volunteer = Volunteer::with([
            'volunteer_request.types','campaigns'  // نفترض أن كل نوع تطوع مرتبط بحملات
        ])->find($id);

        if (!$volunteer) {
            return response()->json([
                'message' => $locale === 'ar' ? 'المتطوع غير موجود' : 'Volunteer not found',
                'data' => null,
                'status_code' => 404
            ], 404);
        }

        // تنسيق البيانات
        $data = [
            'volunteer_id' => $volunteer->id,
            'volunteer_name' => $volunteer->volunteer_request
                ? ($locale === 'ar' ? $volunteer->volunteer_request->full_name_ar : $volunteer->volunteer_request->full_name_en)
                : null,
            'volunteering' => $volunteer->volunteer_request && $volunteer->volunteer_request->types->count()
                ? $volunteer->volunteer_request->types->map(function ($type) use ($locale) {
                    return [
                        'type_id' => $type->id,
                        'type_name' => $locale === 'ar' ? $type->name_ar : $type->name_en,
                        'campaigns' => $type->campaigns->map(fn($campaign) => [
                            'campaign_id' => $campaign->id,
                            'campaign_title' => $locale === 'ar' ? $campaign->title_ar : $campaign->title_en
                        ])
                    ];
                })->values()
                : [],
        ];

        return response()->json([
            'message' => $locale === 'ar' ? 'تم جلب بيانات المتطوع' : 'Volunteer fetched successfully',
            'data' => $data,
            'status_code' => 200
        ], 200);
    }

*/
    public function getVolunteerById($id)
    {
        $locale = app()->getLocale();

        // جلب المتطوع مع طلب التطوع + أنواع التطوع + الحملات مع الكاتيجوري
        $volunteer = Volunteer::with([
            'volunteer_request.types',
            'campaigns.category'
        ])->find($id);

        if (!$volunteer) {
            return response()->json([
                'message' => $locale === 'ar' ? 'المتطوع غير موجود' : 'Volunteer not found',
                'data' => null,
                'status_code' => 404
            ], 404);
        }

        $data = [
            'volunteer_id' => $volunteer->id,
            'volunteer_name' => $volunteer->volunteer_request
                ? $volunteer->volunteer_request->{'full_name_' . $locale}
                : null,

            'volunteering_types' => $volunteer->volunteer_request && $volunteer->volunteer_request->types->count()
                ? $volunteer->volunteer_request->types->map(function ($type) use ($locale) {
                    return [
                        'type_id' => $type->id,
                        'type_name' => $type->{'name_' . $locale},
                    ];
                })->values()
                : [],

            'campaigns' => $volunteer->campaigns->map(function ($campaign) use ($locale) {
                return [
                    'campaign_id' => $campaign->id,
                    'campaign_title' => $campaign->{'title_' . $locale},
                    'campaign_image' => $campaign->image,
                    'campaign_date' => $campaign->start_date,
                    'category' => $campaign->category ? [
                        'id' => $campaign->category->id,
                        'name' => $campaign->category->{'name_category_' . $locale},
                    ] : null,
                ];
            }),
        ];

        return response()->json([
            'message' => $locale === 'ar' ? 'تم جلب بيانات المتطوع' : 'Volunteer fetched successfully',
            'data' => $data,
            'status_code' => 200
        ], 200);
    }
}
