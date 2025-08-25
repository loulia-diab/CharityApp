<?php

namespace App\Http\Controllers\beneficiary;

use App\Http\Controllers\Controller;
use App\Models\Beneficiary;
use App\Models\Campaigns\CampaignBeneficiary;
use App\Models\HumanCase;
use App\Models\Sponsorship;
use Illuminate\Http\Request;

class BeneficiaryController extends Controller
{
    // استفاداتي
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
        if (auth()->guard('api')->check()) {
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

    public function getBeneficiaryInKinds()
    {
    }

    // استفاداتي من كلشي
    public function getBeneficiaryActivities1(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'message' => 'المستخدم غير مسجل الدخول.',
            ], 401);
        }

        // جلب المستفيد المرتبط بالمستخدم الحالي (أو أكثر من مستفيد، حسب حالتك)
        $beneficiary = Beneficiary::where('user_id', $user->id)->first();

        if (!$beneficiary) {
            return response()->json([
                'message' => 'لا يوجد مستفيد مرتبط بالمستخدم الحالي.',
            ], 404);
        }

        $query = CampaignBeneficiary::with([
            'campaign.category',
            'beneficiary.user',
            'admin'
        ])->where('beneficiary_id', $beneficiary->id);

        $activities = $query->orderByDesc('created_at')->get();

        $formatted = $activities->map(function ($activity) {
            $campaign = $activity->campaign;

            // تحديد النوع حسب العلاقات الموجودة
            if ($campaign->sponsorship) {
                $type = 'sponsorship';
            } elseif ($campaign->humanCase) {
                $type = 'human_case';
            } elseif ($campaign->inKind) {
                $type = 'in_kind';
            } else {
                $type = 'campaign'; // الافتراضي
            }

            return [
                'beneficiary_id' => $activity->beneficiary_id,
                'beneficiary_name'=>
                    [
                        'ar'=> $activity->beneficiary->beneficiary_request->name_ar,
                        'en'=> $activity->beneficiary->beneficiary_request->name_en,
                    ],
                'id' => $activity->id,
                'type' => $type,
                'title' => [
                    'ar' => $campaign->title_ar,
                    'en' => $campaign->title_en,
                ],
                'image' => $campaign->image,
                'category' => [
                    'ar' => $campaign->category->name_category_ar ?? null,
                    'en' => $campaign->category->name_category_en ?? null,
                ],

            ];
        });

        return response()->json($formatted);
    }

    public function getBeneficiaryActivities2(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'message' => 'المستخدم غير مسجل الدخول.',
            ], 401);
        }

        $beneficiary = Beneficiary::where('user_id', $user->id)->first();

        if (!$beneficiary) {
            return response()->json([
                'message' => 'لا يوجد مستفيد مرتبط بالمستخدم الحالي.',
            ], 404);
        }

        $locale = app()->getLocale();  // لغة التطبيق الحالية

        $activities = CampaignBeneficiary::with([
            'campaign.category',
            'beneficiary.beneficiary_request',
            'admin'
        ])->where('beneficiary_id', $beneficiary->id)
            ->orderByDesc('created_at')
            ->get();

        $formatted = $activities->map(function ($activity) use ($locale) {
            $campaign = $activity->campaign;
            if ($campaign->sponsorship) {
                $type = 'sponsorship';
            } elseif ($campaign->humanCase) {
                $type = 'human_case';
            } elseif ($campaign->inKind) {
                $type = 'in_kind';
            } else {
                $type = 'campaign';
            }

            return [
                'beneficiary_id' => $activity->beneficiary_id,
                'beneficiary_name' => $activity->beneficiary->beneficiary_request
                    ? $activity->beneficiary->beneficiary_request->{"name_{$locale}"}
                    : null,
                'id' => $activity->id,
                'type' => $type,
                'title' => $campaign ? $campaign->{"title_{$locale}"} : null,
                'image' => $campaign ? $campaign->image : null,
                'category' => $campaign && $campaign->category
                    ? $campaign->category->{"name_category_{$locale}"}
                    : null,
                'admin_id' => $activity->admin?->id ?? null,
                'date' => $campaign->start_date,
            ];
        });

        return response()->json($formatted);
    }

    ///////////////////////////////////////////////////////
// USER

    public function getBeneficiaryActivities(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'المستخدم غير مسجل الدخول.'], 401);
        }

        $beneficiary = Beneficiary::where('user_id', $user->id)->first();

        if (!$beneficiary) {
            return response()->json(['message' => 'لا يوجد مستفيد مرتبط بالمستخدم الحالي.'], 404);
        }

        $locale = app()->getLocale();

        // جلب الحالات الإنسانية
        $humanCases = HumanCase::with('campaign.category',
            'beneficiary.beneficiary_request',
            'campaign.admin')
            ->where('beneficiary_id', $beneficiary->id)
            ->get()
            ->map(function($hc) use ($locale) {
                return [
                    'id' => $hc->id,
                    'type' => 'human_case',
                    'beneficiary_id' => $hc->beneficiary_id,
                    'beneficiary_name' => $hc->beneficiary->beneficiary_request?->{"name_{$locale}"},
                    'title' => $hc->campaign?->{"title_{$locale}"},
                    'image' => $hc->campaign?->image,
                    'category' => $hc->campaign?->category?->{"name_category_{$locale}"},
                    'admin_id' => $hc->campaign->admin?->id,
                    'date' => $hc->created_at,
                ];
            })
            ->values()
            ->toBase(); // ← تحويل لـ Support Collection

        // جلب الكفالات
        $sponsorships = Sponsorship::with('campaign.category', 'beneficiary.beneficiary_request', 'campaign.admin')
            ->where('beneficiary_id', $beneficiary->id)
            ->get()
            ->map(function($sp) use ($locale) {
                return [
                    'id' => $sp->id,
                    'type' => 'sponsorship',
                    'beneficiary_id' => $sp->beneficiary_id,
                    'beneficiary_name' => $sp->beneficiary->beneficiary_request?->{"name_{$locale}"},
                    'title' => $sp->campaign?->{"title_{$locale}"},
                    'image' => $sp->campaign?->image,
                    'category' => $sp->campaign?->category?->{"name_category_{$locale}"},
                    'admin_id' => $sp->campaign->admin?->id,
                    'date' => $sp->created_at,
                ];
            })
            ->values()
            ->toBase();

        // جلب التبرعات العينية
        $inKinds = $beneficiary->inKinds()
            ->with('campaign.category', 'campaign.admin')
            ->get()
            ->map(function($ik) use ($locale, $beneficiary) {
                return [
                    'id' => $ik->id,
                    'type' => 'in_kind',
                    'beneficiary_id' => $beneficiary->id,
                    'beneficiary_name' => $beneficiary->beneficiary_request?->{"name_{$locale}"},
                    'title' => $ik->campaign?->{"title_{$locale}"},
                    'image' => $ik->campaign?->image,
                    'category' => $ik->campaign?->category?->{"name_category_{$locale}"},
                    'admin_id' => $ik->campaign->admin?->id,
                    'date' => $ik->created_at,
                ];
            })
            ->values()
            ->toBase();

        // جلب الحملات العامة عبر جدول الربط
        $campaignActivities = CampaignBeneficiary::with('campaign.category', 'beneficiary.beneficiary_request', 'admin')
            ->where('beneficiary_id', $beneficiary->id)
            ->get()
            ->map(function($activity) use ($locale) {
                $campaign = $activity->campaign;
                return [
                    'id' => $activity->id,
                    'type' => 'campaign',
                    'beneficiary_id' => $activity->beneficiary_id,
                    'beneficiary_name' => $activity->beneficiary->beneficiary_request?->{"name_{$locale}"},
                    'title' => $campaign?->{"title_{$locale}"},
                    'image' => $campaign?->image,
                    'category' => $campaign?->category?->{"name_category_{$locale}"},
                    'admin_id' => $activity->admin?->id,
                    'date' => $activity->created_at,
                ];
            })
            ->values()
            ->toBase();

        // الدمج والترتيب
        $allActivities = collect()
            ->merge($humanCases)
            ->merge($sponsorships)
            ->merge($inKinds)
            ->merge($campaignActivities)
            ->sortByDesc('date')
            ->values();

        return response()->json($allActivities);
    }

    // ADMIN
    public function getSortedBeneficiariesActivities(Request $request)
    {
        $locale = app()->getLocale();
        $admin = auth('admin')->user();

        if (!$admin) {
            return response()->json([
                'message' => $locale === 'ar' ? 'غير مصرح' : 'Unauthorized',
                'status' => 401
            ], 401);
        }

        $beneficiaries = Beneficiary::where('is_sorted',true)
            ->with([
                'beneficiary_request',
                'humanCases.campaign.category',
                'humanCases.campaign.admin',
                'sponsorships.campaign.category',
                'sponsorships.campaign.admin',
                'inKinds.campaign.category',
                'inKinds.campaign.admin',
                'campaigns.category',
                'campaigns.admin',
            ])
            ->get()
            ->map(function ($b) use ($locale, $request) {
                $data = [
                    'id' => $b->id,
                    'full_name' => $b->beneficiary_request?->{"name_{$locale}"},
                    // 'details' => $b->beneficiary_request?->details ?? null,
                ];

                return $data;
            });

        return response()->json([
            'message' =>( $locale === 'ar' ? 'المستفيدين المفروزين' : 'Stored beneficiaries'),
            'data' => $beneficiaries,
        ]);
    }

    public function getSortedBeneficiaryDetails(Request $request, $beneficiaryId)
    {
        $locale = app()->getLocale();
        $admin = auth('admin')->user();

        if (!$admin) {
            return response()->json([
                'message' => $locale === 'ar' ? 'غير مصرح' : 'Unauthorized',
                'status' => 401
            ], 401);
        }

        // هلق ما عاد في داعي نقرأ الـ ID من $request
        $b = Beneficiary::where('is_sorted', true)
            ->where('id', $beneficiaryId)
            ->with([
                'beneficiary_request',
                'humanCases.campaign.category',
                'humanCases.campaign.admin',
                'sponsorships.campaign.category',
                'sponsorships.campaign.admin',
                'inKinds.campaign.category',
                'inKinds.campaign.admin',
                'campaigns.category',
                'campaigns.admin',
            ])
            ->first();

        if (!$b) {
            return response()->json([
                'message' => $locale === 'ar' ? 'المستفيد غير موجود أو غير مفروز' : 'Beneficiary not found or not sorted',
                'status' => 404
            ], 404);
        }

        $data = [
            'id' => $b->id,
            'full_name' => $b->beneficiary_request?->{"name_{$locale}"},
        ];

        $activities = collect();

        // الحالات الإنسانية
        foreach ($b->humanCases as $hc) {
            $activities->push([
                'id' => $hc->id,
                'type' => 'human_case',
                'beneficiary_id' => $hc->beneficiary_id,
                'beneficiary_name' => $b->beneficiary_request?->{"name_{$locale}"},
                'title' => $hc->campaign?->{"title_{$locale}"},
                'image' => $hc->campaign?->image,
                'category' => $hc->campaign?->category?->{"name_category_{$locale}"},
                'admin_id' => $hc->campaign->admin?->id,
                'date' => $hc->created_at,
            ]);
        }

        // الكفالات
        foreach ($b->sponsorships as $sp) {
            $activities->push([
                'id' => $sp->id,
                'type' => 'sponsorship',
                'beneficiary_id' => $sp->beneficiary_id,
                'beneficiary_name' => $b->beneficiary_request?->{"name_{$locale}"},
                'title' => $sp->campaign?->{"title_{$locale}"},
                'image' => $sp->campaign?->image,
                'category' => $sp->campaign?->category?->{"name_category_{$locale}"},
                'admin_id' => $sp->campaign->admin?->id,
                'date' => $sp->created_at,
            ]);
        }

        // التبرعات العينية
        foreach ($b->inKinds as $ik) {
            $activities->push([
                'id' => $ik->id,
                'type' => 'in_kind',
                'beneficiary_id' => $b->id,
                'beneficiary_name' => $b->beneficiary_request?->{"name_{$locale}"},
                'title' => $ik->campaign?->{"title_{$locale}"},
                'image' => $ik->campaign?->image,
                'category' => $ik->campaign?->category?->{"name_category_{$locale}"},
                'admin_id' => $ik->campaign->admin?->id,
                'date' => $ik->created_at,
            ]);
        }

        // الحملات العامة
        foreach ($b->campaigns as $c) {
            $activities->push([
                'id' => $c->id,
                'type' => 'campaign',
                'beneficiary_id' => $b->id,
                'beneficiary_name' => $b->beneficiary_request?->{"name_{$locale}"},
                'title' => $c?->{"title_{$locale}"},
                'image' => $c->image,
                'category' => $c->category?->{"name_category_{$locale}"},
                'admin_id' => $c->admin?->id,
                'date' => $c->created_at,
            ]);
        }

        $data['activities'] = $activities->sortByDesc('date')->values();

        return response()->json([
            'message' => $locale === 'ar' ? 'تم جلب تفاصيل المستفيد المفروز' : 'Sorted beneficiary details fetched successfully',
            'data' => $data,
        ]);
    }

    public function getUnsortedBeneficiaries(Request $request)
    {
        $admin = auth()->guard('admin')->user();
        if (!$admin) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $locale = app()->getLocale();

        $beneficiaries = Beneficiary::where('is_sorted', false)
            ->with('beneficiary_request') // جلب بيانات الطلب المرتبط
            ->get()
            ->map(function ($beneficiary) use ($locale) {
                return [
                    'beneficiary_id' => $beneficiary->id,
                    'name' => $beneficiary->beneficiary_request->{'name_' . $locale},
                    'main_category' => $beneficiary->beneficiary_request->{'main_category_' . $locale},
                    'sub_category' => $beneficiary->beneficiary_request->{'sub_category_' . $locale},
                    'priority' => $beneficiary->{'priority_' . $locale},
                ];
            });

        return response()->json($beneficiaries);
    }

    /*
        public function getBeneficiaryDetails($beneficiary_id)
        {
            $locale = app()->getLocale();
            $admin = auth('admin')->user();

            if (!$admin) {
                return response()->json([
                    'message' => $locale === 'ar' ? 'غير مصرح' : 'Unauthorized',
                    'status' => 401
                ], 401);
            }
    if()
            $beneficiaries = Beneficiary::where('is_sorted',true)
                ->with([
                    'beneficiary_request',
                    'humanCases.campaign.category',
                    'humanCases.campaign.admin',
                    'sponsorships.campaign.category',
                    'sponsorships.campaign.admin',
                    'inKinds.campaign.category',
                    'inKinds.campaign.admin',
                    'campaigns.category',
                    'campaigns.admin',
                ])
                ->get()
                ->map(function ($b) use ($locale) {
                    $data = [
                        'id' => $b->id,
                        'full_name' => $b->beneficiary_request?->{"name_{$locale}"},
                        // 'details' => $b->beneficiary_request?->details ?? null,
                    ];
                    $activities = collect();
                    // الحالات الإنسانية
                    foreach ($b->humanCases as $hc) {
                        $activities->push([
                            'id' => $hc->id,
                            'type' => 'human_case',
                            'beneficiary_id' => $hc->beneficiary_id,
                            'beneficiary_name' => $b->beneficiary_request?->{"name_{$locale}"},
                            'title' => $hc->campaign?->{"title_{$locale}"},
                            'image' => $hc->campaign?->image,
                            'category' => $hc->campaign?->category?->{"name_category_{$locale}"},
                            'admin_id' => $hc->campaign->admin?->id,
                            'date' => $hc->created_at,
                        ]);
                    }

                    // الكفالات
                    foreach ($b->sponsorships as $sp) {
                        $activities->push([
                            'id' => $sp->id,
                            'type' => 'sponsorship',
                            'beneficiary_id' => $sp->beneficiary_id,
                            'beneficiary_name' => $b->beneficiary_request?->{"name_{$locale}"},
                            'title' => $sp->campaign?->{"title_{$locale}"},
                            'image' => $sp->campaign?->image,
                            'category' => $sp->campaign?->category?->{"name_category_{$locale}"},
                            'admin_id' => $sp->campaign->admin?->id,
                            'date' => $sp->created_at,
                        ]);
                    }

                    // التبرعات العينية
                    foreach ($b->inKinds as $ik) {
                        $activities->push([
                            'id' => $ik->id,
                            'type' => 'in_kind',
                            'beneficiary_id' => $b->id,
                            'beneficiary_name' => $b->beneficiary_request?->{"name_{$locale}"},
                            'title' => $ik->campaign?->{"title_{$locale}"},
                            'image' => $ik->campaign?->image,
                            'category' => $ik->campaign?->category?->{"name_category_{$locale}"},
                            'admin_id' => $ik->campaign->admin?->id,
                            'date' => $ik->created_at,
                        ]);
                    }

                    // الحملات العامة
                    foreach ($b->campaigns as $c) {
                        $activities->push([
                            'id' => $c->id,
                            'type' => 'campaign',
                            'beneficiary_id' => $b->id,
                            'beneficiary_name' => $b->beneficiary_request?->{"name_{$locale}"},
                            'title' => $c?->{"title_{$locale}"},
                            'image' => $c->image,
                            'category' => $c->category?->{"name_category_{$locale}"},
                            'admin_id' => $c->admin?->id,
                            'date' => $c->created_at,
                        ]);
                    }

                    $data['activities'] = $activities->sortByDesc('date')->values();


                    return $data;
                });

            return response()->json([
                'message' =>( $locale === 'ar' ? 'المستفيدين المفروزين' : 'Stored beneficiaries'),
                'data' => $beneficiaries,
            ]);
        }

    */
}
