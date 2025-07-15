<?php

namespace App\Http\Controllers\Donation_Type\Campaign;

use App\Enums\CampaignStatus;
use App\Http\Controllers\Controller;
use App\Models\Campaigns\Campaign;
use App\Models\Campaigns\CampaignBeneficiary;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CampaignController extends Controller
{

    public function addCampaign(Request $request)
    {
        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json(['message' => 'Unauthorized', 'error' => '', 'status' => 401], 401);
        }

        $locale = app()->getLocale();
        $titleField = "title_{$locale}";
        $descField = "description_{$locale}";

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|string|max:255',
            'goal_amount' => 'required|numeric|min:0',
            'collected_amount' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => ['nullable', Rule::in(CampaignStatus::values())],
        ]);

        try {
            $campaign = Campaign::create([
                $titleField => $validated['title'],
                $descField => $validated['description'],
                'category_id' => $validated['category_id'],
                'image' => $validated['image'] ?? null,
                'goal_amount' => $validated['goal_amount'],
                'collected_amount' => $validated['collected_amount'] ?? 0,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'status' => CampaignStatus::tryFrom($validated['status'] ?? 'pending'),
            ]);

            return response()->json([
                'message' => 'Campaign added successfully',
                'data' => $campaign,
                'status' => 201
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error adding campaign',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }

    public function getAllCampaigns(Request $request)
    {
        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json([
                'message' => 'Unauthorized - Admin access only',
            ], 401);
        }

        $locale = app()->getLocale();
        $titleField = "title_{$locale}";
        $descField = "description_{$locale}";

        $query = Campaign::query();

        if ($request->has('status') && in_array($request->status, CampaignStatus::values())) {
            $query->where('status', $request->status);
        }

        $campaigns = $query->select(
            'id',
            "{$titleField} as title",
            "{$descField} as description",
            'image',
            'goal_amount',
            'collected_amount',
            'start_date',
            'end_date',
            'status'
        )->get();

        // أضف تسمية الحالة لكل عنصر
        $campaigns->getCollection()->transform(function ($campaign) use ($locale) {
            $campaign->status_label = CampaignStatus::from($campaign->status)->label($locale);
            return $campaign;
        });

        return response()->json([
            'message' => 'Campaigns fetched successfully',
            'data' => $campaigns->items(),
            'status' => 200
        ]);
    }

    public function getCampaignDetails($id)
    {
        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json([
                'message' => 'Unauthorized - Admin access only',
            ], 401);
        }

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
            ->withCount('beneficiaries')
            ->find($id);

        if (!$campaign) {
            return response()->json([
                'message' => 'Campaign not found',
                'error' => '',
                'status' => 404
            ], 404);
        }

        $campaign->status_label = CampaignStatus::from($campaign->status)->label($locale);

        return response()->json([
            'message' => 'Campaign details fetched successfully',
            'data' => $campaign,
            'status' => 200
        ]);
    }



    public function archiveCampaign(Request $request, $id)
    {
        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        try {
            $campaign = Campaign::findOrFail($id);

            // شرط: ممكن تأرشف فقط الحملة اللي مش مؤرشفة أو مكتملة
            if (in_array($campaign->status, [
                \App\Enums\CampaignStatus::Archived,
                \App\Enums\CampaignStatus::Complete
            ])) {
                return response()->json([
                    'message' => 'Cannot archive a campaign that is already archived or complete',
                    'status' => 400
                ], 400);
            }

            // غيّر الحالة إلى Archived باستخدام enum
            $campaign->status = \App\Enums\CampaignStatus::Archived;
            $campaign->save();

            return response()->json([
                'message' => 'Campaign archived successfully',
                'data' => $campaign,
                'status' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error archiving campaign',
                'error' => $e->getMessage(),
                'status' => 500
            ]);
        }
    }
    public function activateCampaign(Request $request, $id)
    {
        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        try {
            $campaign = Campaign::findOrFail($id);

            if (!in_array($campaign->status, [
                \App\Enums\CampaignStatus::Pending,
            ])) {
                return response()->json([
                    'message' => 'Cannot activate a campaign unless it is pending or archived',
                    'status' => 400
                ], 400);
            }

            // غيّر الحالة إلى Active باستخدام enum
            $campaign->status = \App\Enums\CampaignStatus::Active;
            $campaign->save();

            return response()->json([
                'message' => 'Campaign activated successfully',
                'data' => $campaign,
                'status' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error activating campaign',
                'error' => $e->getMessage(),
                'status' => 500
            ]);
        }
    }



    public function updateCampaign(Request $request, $id)
    {
        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json(['message' => 'Unauthorized', 'error' => '', 'status' => 401], 401);
        }

        $locale = app()->getLocale();
        $titleField = "title_{$locale}";
        $descField = "description_{$locale}";

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|string|max:255',
        ]);

        $campaign = Campaign::find($id);
        if (!$campaign) {
            return response()->json(['message' => 'Campaign not found', 'error' => '', 'status' => 404], 404);
        }

        if ($validated['goal_amount'] < $campaign->collected_amount) {
            return response()->json([
                'message' => 'Goal amount cannot be less than the collected amount.',
                'error' => '',
                'status' => 422
            ], 422);
        }

        $campaign->$titleField = $validated['title'];
        $campaign->$descField = $validated['description'];

        if (isset($validated['image'])) {
            $campaign->image = $validated['image'];
        }

        $campaign->save();

        return response()->json([
            'message' => 'Campaign updated successfully',
            'data' => $campaign,
            'status' => 200
        ]);
    }

    public function getCampaignsByStatus(Request $request)
    {
        try {
            $validated = $request->validate([
                'status' => ['required', Rule::in(CampaignStatus::values())],
            ]);
            $locale = app()->getLocale();

            $campaigns = Campaign::where('status', $validated['status'])->paginate(10);

            $campaigns->getCollection()->transform(function ($campaign) use ($locale) {
                $titleField = "title_{$locale}";
                $descField = "description_{$locale}";

                $campaign->status_label = CampaignStatus::from($campaign->status)->label($locale);

                return [
                    'id' => $campaign->id,
                    'title' => $campaign->$titleField,
                    'description' => $campaign->$descField,
                    'category_id' => $campaign->category_id,
                    'goal_amount' => $campaign->goal_amount,
                    'collected_amount' => $campaign->collected_amount,
                    'start_date' => $campaign->start_date,
                    'end_date' => $campaign->end_date,
                    'status' => $campaign->status,
                    'image' => $campaign->image,
                    'status_label' => $campaign->status_label,
                ];
            });

            return response()->json([
                'status' => $validated['status'],
                'message' => $locale === 'ar' ? 'تم جلب الحملات بنجاح' : 'Campaigns fetched successfully',
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
            $locale = app()->getLocale();
            return response()->json([
                'message' => $locale === 'ar' ? 'حدث خطأ أثناء جلب الحملات' : 'Error fetching campaigns',
                'error' => $e->getMessage(),
                'status_code' => 500
            ], 500);
        }
    }

    public function getCampaignsByCategory($categoryId)
    {
        try {
            $locale = app()->getLocale();
            $titleField = "title_{$locale}";
            $descField = "description_{$locale}";

            $campaigns = Campaign::where('category_id', $categoryId)
                ->select(
                    'id',
                    "{$titleField} as title",
                    "{$descField} as description",
                    'image',
                    'goal_amount',
                    'collected_amount',
                    'start_date',
                    'end_date',
                    'status'
                )
                ->paginate(10);

            $campaigns->getCollection()->transform(function ($campaign) use ($locale) {
                $campaign->status_label = CampaignStatus::from($campaign->status)->label($locale);
                return $campaign;
            });

            return response()->json([
                'message' => $locale === 'ar' ? 'تم جلب الحملات بنجاح' : 'Campaigns fetched successfully',
                'data' => $campaigns->items(),
                'pagination' => [
                    'current_page' => $campaigns->currentPage(),
                    'last_page' => $campaigns->lastPage(),
                    'per_page' => $campaigns->perPage(),
                    'total' => $campaigns->total(),
                ],
                'status' => 200
            ], 200);
        } catch (\Exception $e) {
            $locale = app()->getLocale();
            return response()->json([
                'message' => $locale === 'ar' ? 'حدث خطأ أثناء جلب الحملات' : 'Error fetching campaigns',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }

    // user $ admin

    public function getArchivedCampaigns(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $locale = app()->getLocale();  // تحديد اللغة الحالية (مثلاً 'ar' أو 'en')

        try {
            $campaigns = Campaign::where('status', \App\Enums\CampaignStatus::Archived)
                ->latest()
                ->paginate($perPage);

            $data = $campaigns->map(function ($campaign) use ($locale) {
                $titleField = "title_{$locale}";
                $descField = "description_{$locale}";

                return [
                    'id' => $campaign->id,
                    'title' => $campaign->$titleField,
                    'description' => $campaign->$descField,
                    'category_id' => $campaign->category_id,
                    'goal_amount' => $campaign->goal_amount,
                    'collected_amount' => $campaign->collected_amount,
                    'start_date' => $campaign->start_date,
                    'end_date' => $campaign->end_date,
                    'status' => $campaign->status,
                    'image' => $campaign->image,
                    'remaining_amount' => $campaign->remaining_amount,
                    'status_label' => $campaign->status_label,
                ];
            });

            return response()->json([
                'message' => $locale === 'ar' ? 'تم جلب الحملات المؤرشفة بنجاح' : 'Archived campaigns fetched successfully',
                'data' => $data,
                'pagination' => [
                    'current_page' => $campaigns->currentPage(),
                    'last_page' => $campaigns->lastPage(),
                    'per_page' => $campaigns->perPage(),
                    'total' => $campaigns->total(),
                ],
                'status' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $locale === 'ar' ? 'حدث خطأ أثناء جلب الحملات المؤرشفة' : 'Error fetching archived campaigns',
                'error' => $e->getMessage(),
                'status' => 500
            ]);
        }
    }

    // user //////////////////////
    public function getAllVisibleCampaignsForUser()
    {
        try {
            $locale = app()->getLocale();
            $titleField = "title_{$locale}";
            $descField = "description_{$locale}";

            $campaigns = Campaign::where('status', \App\Enums\CampaignStatus::Active)
                ->select(
                    'id',
                    "$titleField as title",
                    "$descField as description",
                    'image',
                    'goal_amount',
                    'collected_amount'
                )
                ->get()
                ->map(function ($campaign) {
                    $campaign->remaining_amount = max(0, $campaign->goal_amount - $campaign->collected_amount);
                    return $campaign;
                });

            return response()->json([
                'message' => app()->getLocale() === 'ar' ? 'تم جلب الحملات بنجاح' : 'Campaigns fetched successfully',
                'data' => $campaigns,
                'status' => 200
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => app()->getLocale() === 'ar' ? 'حدث خطأ أثناء جلب الحملات' : 'Error fetching campaigns',
                'error' => $e->getMessage(),
                'status' => 500
            ]);
        }
    }
    public function getVisibleCampaignsByCategoryForUser(Request $request, $categoryId)
    {
        try {
            $locale = app()->getLocale();
            $titleField = "title_{$locale}";
            $descField = "description_{$locale}";

            $campaigns = Campaign::where('category_id', $categoryId)
                ->where('status', \App\Enums\CampaignStatus::Active)
                ->select(
                    'id',
                    'category_id',
                    "$titleField as title",
                    "$descField as description",
                    'image',
                    'goal_amount',
                    'collected_amount',

                )
                ->get()
                ->map(function ($campaign) {
                    $campaign->remaining_amount = max(0, $campaign->goal_amount - $campaign->collected_amount);
                    return $campaign;
                });

            return response()->json([
                'message' => $locale === 'ar' ? 'تم جلب الحملات حسب التصنيف بنجاح' : 'Campaigns by category fetched successfully',
                'data' => $campaigns,
                'status' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $locale === 'ar' ? 'حدث خطأ أثناء جلب الحملات' : 'Error fetching campaigns',
                'error' => $e->getMessage(),
                'status' => 500
            ]);
        }
    }
    public function getVisibleCampaignByIdForUser($campaign_id)
    {
        try {
            $locale = app()->getLocale();
            $titleField = "title_{$locale}";
            $descField = "description_{$locale}";

            $campaign = Campaign::where('id', $campaign_id)
                ->where('status', \App\Enums\CampaignStatus::Active)
                ->withCount('beneficiaries')
                ->select(
                    'id',
                    'category_id',
                    "$titleField as title",
                    "$descField as description",
                    'image',
                    'goal_amount',
                    'collected_amount',
                    'start_date',
                    'end_date',
                )
                ->first();

            if (!$campaign) {
                return response()->json([
                    'message' => $locale === 'ar' ? 'الحملة غير موجودة أو غير متاحة' : 'Campaign not found or not visible',
                    'status' => 404
                ], 404);
            }

            $campaign->remaining_amount = max(0, $campaign->goal_amount - $campaign->collected_amount);

            return response()->json([
                'message' => $locale === 'ar' ? 'تم جلب تفاصيل الحملة بنجاح' : 'Campaign details fetched successfully',
                'data' => $campaign,
                'status' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $locale === 'ar' ? 'حدث خطأ أثناء جلب الحملة' : 'Error fetching campaign',
                'error' => $e->getMessage(),
                'status' => 500
            ]);
        }
    }


// الإحصائيات
    public function campaignStatistics()
    {
        return response()->json([
            'total_campaigns' => Campaign::count(),
            'total_beneficiaries' => CampaignBeneficiary::count(),
            'total_donated' => Campaign::sum('collected_amount'),
            'status' => 200
        ]);
    }




}
