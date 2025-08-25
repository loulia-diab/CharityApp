<?php

namespace App\Http\Controllers\Donation_Type\Campaign;

use App\Enums\CampaignStatus;
use App\Http\Controllers\Controller;
use App\Models\Campaigns\Campaign;
use App\Models\Campaigns\CampaignBeneficiary;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;


class CampaignController extends Controller
{
    // Admin
    public function addCampaign(Request $request)
    {
        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json([
                'message' => 'Unauthorized',
                'error' => '',
                'status' => 401
            ], 401);
        }

        $validated = $request->validate([
            'title_en' => 'required|string|max:255',
            'title_ar' => 'required|string|max:255',
            'description_en' => 'required|string',
            'description_ar' => 'required|string',
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')->where(function ($query) {
                    $query->where('main_category', 'Campaign');
                }),
            ],
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'goal_amount' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => ['nullable', Rule::in(CampaignStatus::values())],
        ]);

        try {
            // أول شي ننشئ الحملة بدون صورة
            $campaign = Campaign::create([
                'title_en' => $validated['title_en'],
                'title_ar' => $validated['title_ar'],
                'description_en' => $validated['description_en'],
                'description_ar' => $validated['description_ar'],
                'category_id' => $validated['category_id'],
                'goal_amount' => $validated['goal_amount'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'status' => CampaignStatus::tryFrom($validated['status'] ?? 'pending'),
                'created_at'
            ]);

            // إذا في صورة نرفعها بعد ما نعرف الـ ID
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $ext = $image->getClientOriginalExtension();
                $imageName = 'campaign_' . $campaign->id . '.' . $ext;
                $path = $image->storeAs('campaign_images', $imageName, 'public');

                $campaign->image = $path;
                $campaign->save();
            }

            return response()->json([
                'message' => 'Campaign added successfully',
                'data' => $campaign,
                'status' => 201
            ], 201);

        }catch (\Exception $e) {
            return response()->json([
                'message' => 'Error adding campaign',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }
    public function getAllCampaigns()
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

        // شرط: الفئة الرئيسية = 'Campaign'
        $query->whereHas('category', function ($q) {
            $q->where('main_category', 'Campaign');
        });

        // شرط: الحالة لا تساوي Archived
        $query->where('status', '!=', CampaignStatus::Archived->value);

        $campaigns = $query->select(
            'id',
            "{$titleField} as title",
            "{$descField} as description",
            'image',
            'goal_amount',
            'collected_amount',
            'start_date',
            'end_date',
            'status',
            'created_at'
        )->get();

        if ($campaigns->isEmpty()) {
            return response()->json([
                'message' => $locale === 'ar' ? 'لا يوجد حملات' : 'No campaigns found',
                'status' => 404
            ], 404);
        }

        // إضافة تسمية الحالة
        $campaigns->transform(function ($campaign) use ($locale) {
            $campaign->status_label = $campaign->status->label($locale);
           // $campaign->created_date = $campaign->created_at->format('Y-m-d');
            return $campaign;
        });

        return response()->json([
            'message' => $locale === 'ar' ? 'تم جلب الحملات بنجاح' : 'Campaigns fetched successfully',
            'data' => $campaigns,
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

        $campaign = Campaign::whereHas('category', function ($q) {
            $q->where('main_category', 'Campaign');
        })
            ->where('status', '!=', CampaignStatus::Archived->value) //  شرط استثناء المؤرشفة
            ->select(
                'id',
                'category_id',
                "{$titleField} as title",
                "{$descField} as description",
                'image',
                'goal_amount',
                'collected_amount',
                'start_date',
                'end_date',
                'status',
                'created_at'
            )
            ->withCount('beneficiaries')
            ->find($id);

        if (!$campaign) {
            return response()->json([
                'message' => $locale === 'ar' ? 'الحملة غير موجودة' : 'Campaign not found',
                'error' => '',
                'status' => 404
            ], 404);
        }

        $campaign->status_label = $campaign->status->label($locale);
        $campaign->remaining_amount = max(0, $campaign->goal_amount - $campaign->collected_amount);

        return response()->json([
            'message' => $locale === 'ar' ? 'تم جلب تفاصيل الحملة بنجاح' : 'Campaign details fetched successfully',
            'data' => $campaign,
            'status' => 200
        ]);
    }
    public function archiveCampaign($id)
    {
        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        try {
            $campaign = Campaign::whereHas('category', function($q) {
                $q->where('main_category', 'Campaign');
            })->findOrFail($id);
            if (!in_array($campaign->status, [
                \App\Enums\CampaignStatus::Complete,
            ])) {
                return response()->json([
                    'message' => 'Cannot archive a campaign unless it is complete',
                    'status' => 400
                ], 400);
            }
            $campaign->status = CampaignStatus::Archived->value;
            $campaign->save();

            return response()->json([
                'message' => 'Campaign archived successfully',
                'data' => $campaign,
                'status' => 200
            ]);
        }
        catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Campaign not found',
                'status' => 404
            ], 404);
        }
        catch (\Exception $e) {
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
            $campaign = Campaign::where('id', $id)
                ->whereHas('category', function($q) {
                    $q->where('main_category', 'Campaign');
                })
                ->firstOrFail();

            if (!in_array($campaign->status, [
                \App\Enums\CampaignStatus::Pending,
            ])) {
                return response()->json([
                    'message' => 'Cannot activate a campaign unless it is pending',
                    'status' => 400
                ], 400);
            }

            $campaign->status = \App\Enums\CampaignStatus::Active;
            $campaign->save();

            return response()->json([
                'message' => 'Campaign activated successfully',
                'data' => $campaign,
                'status' => 200
            ]);
        }
        catch (ModelNotFoundException $e) {
            return response()->json([
                'message' =>  'Campaign not found',
                'status' => 404
            ], 404);
        }catch (\Exception $e) {
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

        $validated = $request->validate([
            'title_en' => 'nullable|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'goal_amount' => 'nullable|numeric|min:0',
            'collected_amount' => 'prohibited', //  لا يسمح بتعديل collected_amount
        ]);

        $campaign = Campaign::whereHas('category', function ($q) {
            $q->where('main_category', 'Campaign');
        })->find($id);

        if (!$campaign) {
            return response()->json(['message' => 'Campaign not found or invalid category', 'error' => '', 'status' => 404], 404);
        }


        if (in_array($campaign->status, [CampaignStatus::Archived, CampaignStatus::Complete])) {
            return response()->json([
                'message' => 'Cannot update an archived or completed campaign.',
                'error' => '',
                'status' => 403
            ], 403);
        }

        if (isset($validated['goal_amount']) && $validated['goal_amount'] < $campaign->collected_amount) {
            return response()->json([
                'message' => 'Goal amount cannot be less than the collected amount.',
                'error' => '',
                'status' => 422
            ], 422);
        }

        // تحديث البيانات
        $campaign->fill([
            'title_en' => $validated['title_en'] ?? $campaign->title_en,
            'title_ar' => $validated['title_ar'] ?? $campaign->title_ar,
            'description_en' => $validated['description_en'] ?? $campaign->description_en,
            'description_ar' => $validated['description_ar'] ?? $campaign->description_ar,
            'goal_amount' => $validated['goal_amount'] ?? $campaign->goal_amount,
        ]);

        // إذا في صورة جديدة
        if ($request->hasFile('image')) {
            // حذف القديمة
            if ($campaign->image && \Storage::disk('public')->exists($campaign->image)) {
                \Storage::disk('public')->delete($campaign->image);
            }

            $image = $request->file('image');
            $ext = $image->getClientOriginalExtension();
            $imageName = 'campaign_' . $campaign->id . '.' . $ext;
            $path = $image->storeAs('campaign_images', $imageName, 'public');

            $campaign->image = $path;
        }

        $campaign->save();

        return response()->json([
            'message' => 'Campaign updated successfully',
            'data' => $campaign,
            'status' => 200
        ]);
    }
    public function getCampaignsByStatus($categoryId, $status)
    {
        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json(['message' => 'Unauthorized', 'error' => '', 'status_code' => 401], 401);
        }

        try {
            $locale = app()->getLocale();
            $titleField = "title_{$locale}";
            $descField = "description_{$locale}";

            if (!in_array($status, \App\Enums\CampaignStatus::values())) {
                return response()->json([
                    'message' => $locale === 'ar' ? 'قيمة الحالة غير صحيحة' : 'Invalid status value',
                    'status_code' => 400
                ], 400);
            }

            $campaigns = Campaign::where('status', $status)
                ->where('status', '!=', CampaignStatus::Archived->value)
                ->where('category_id', $categoryId)
                ->whereHas('category', function ($q) {
                    $q->where('main_category', 'Campaign');
                })
                ->with('category')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($campaign) use ($locale, $titleField, $descField) {
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
                        'status_label' => $campaign->status->label($locale),
                        'image' => $campaign->image,
                        'created_at' => $campaign->created_at,
                    ];
                });

            return response()->json([
                'status' => $status,
                'message' => $locale === 'ar' ? 'تم جلب الحملات بنجاح' : 'Campaigns fetched successfully',
                'data' => $campaigns,
                'status_code' => 200
            ]);
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
        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json(['message' => 'Unauthorized', 'error' => '', 'status' => 401], 401);
        }

        try {
            $locale = app()->getLocale();
            $titleField = "title_{$locale}";
            $descField = "description_{$locale}";

            $campaigns = Campaign::where('category_id', $categoryId)
                ->where('status', '!=', CampaignStatus::Archived->value)
                ->whereHas('category', function ($q) {
                    $q->where('main_category', 'Campaign');
                })
                ->select(
                    'id',
                    "{$titleField} as title",
                    "{$descField} as description",
                    'image',
                    'goal_amount',
                    'collected_amount',
                    'start_date',
                    'end_date',
                    'status',
                    'created_at'
                )
                ->get();

            $campaigns->transform(function ($campaign) use ($locale) {
                $campaign->status_label = $campaign->status->label($locale);
                return $campaign;
            });

            return response()->json([
                'message' => $locale === 'ar' ? 'تم جلب الحملات بنجاح' : 'Campaigns fetched successfully',
                'data' => $campaigns,
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
    public function getCampaignsByCreationDate(Request $request)
    {
        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json(['message' => 'Unauthorized', 'error' => '', 'status' => 401], 401);
        }

        try {
            $locale = app()->getLocale();
            $titleField = "title_{$locale}";
            $descField = "description_{$locale}";

            $campaigns = Campaign::whereHas('category', function ($q) {
                $q->where('main_category', 'Campaign');
            })
                ->where('status', '!=', CampaignStatus::Archived->value)
                ->orderBy('created_at', 'desc')
                ->select(
                    'id',
                    "$titleField as title",
                    "$descField as description",
                    'image',
                    'goal_amount',
                    'collected_amount',
                    'start_date',
                    'end_date',
                    'status',
                    'created_at'
                )
                ->get()
                ->map(function ($campaign) {
                    $campaign->remaining_amount = max(0, $campaign->goal_amount - $campaign->collected_amount);
                    return $campaign;
                });

            $campaigns->transform(function ($campaign) use ($locale) {
                $campaign->status_label = $campaign->status?->label($locale) ?? '';
                return $campaign;
            });

            return response()->json([
                'message' => $locale === 'ar' ? 'تم جلب الحملات حسب تاريخ الإضافة بنجاح' : 'Campaigns fetched by creation date successfully',
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
    public function getArchivedCampaigns(Request $request)
    {
        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json(['message' => 'Unauthorized', 'error' => '', 'status' => 401], 401);
        }
        $locale = app()->getLocale();  // تحديد اللغة الحالية (مثلاً 'ar' أو 'en')

        try {
            $campaigns = Campaign::where('status', \App\Enums\CampaignStatus::Archived)
                ->whereHas('category', function ($q) {
                    $q->where('main_category', 'Campaign');
                })
                ->latest()
                ->get();

            $data = $campaigns->transform(function ($campaign) use ($locale) {
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
                    'status_label' => $campaign->status->label($locale),
                    'created_at'
                ];
            });

            return response()->json([
                'message' => $locale === 'ar' ? 'تم جلب الحملات المؤرشفة بنجاح' : 'Archived campaigns fetched successfully',
                'data' => $data,
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

    public function getCampaignsByStatus2(Request $request)
    {
        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json(['message' => 'Unauthorized', 'error' => '', 'status' => 401], 401);
        }
        try {
            $validated = $request->validate([
                'status' => ['required', Rule::in(CampaignStatus::values())],
            ]);
            $locale = app()->getLocale();

            $campaigns = Campaign::where('status', $validated['status'])
                ->whereHas('category', function ($q) {
                    $q->where('main_category', 'Campaign');
                })
                ->get();

            $campaigns->transform(function ($campaign) use ($locale) {
                $titleField = "title_{$locale}";
                $descField = "description_{$locale}";

                // $campaign->status_label = CampaignStatus::from($campaign->status)->label($locale);
                $campaign->status_label = $campaign->status->label($locale);

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
                    'created_at'
                ];
            });

            return response()->json([
                'status' => $validated['status'],
                'message' => $locale === 'ar' ? 'تم جلب الحملات بنجاح' : 'Campaigns fetched successfully',
                'data' => $campaigns->items(),
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
    public function getCampaignsByCategory2($categoryId)
    {
        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json(['message' => 'Unauthorized', 'error' => '', 'status' => 401], 401);
        }
        try {
            $locale = app()->getLocale();
            $titleField = "title_{$locale}";
            $descField = "description_{$locale}";

            $campaigns = Campaign::where('category_id', $categoryId)
                ->whereHas('category', function ($q) {
                    $q->where('main_category', 'Campaign');
                })
                ->select(
                    'id',
                    "{$titleField} as title",
                    "{$descField} as description",
                    'image',
                    'goal_amount',
                    'collected_amount',
                    'start_date',
                    'end_date',
                    'status',
                    'created_at'
                )
                ->get();


            $campaigns->transform(function ($campaign) use ($locale) {
                $campaign->status_label = $campaign->status->label($locale);
                return $campaign;
            });

            return response()->json([
                'message' => $locale === 'ar' ? 'تم جلب الحملات بنجاح' : 'Campaigns fetched successfully',
                'data' => $campaigns,
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
    public function getCampaignsByCreationDate2(Request $request)
    {
        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json(['message' => 'Unauthorized', 'error' => '', 'status' => 401], 401);
        }
        try {
            $locale = app()->getLocale();
            $titleField = "title_{$locale}";
            $descField = "description_{$locale}";
            $campaigns = Campaign::whereHas('category', function ($q) {
                $q->where('main_category', 'Campaign');
            })
                ->orderBy('created_at', 'desc') // ترتيب حسب تاريخ الإضافة (الأحدث أولاً)
                ->select(
                    'id',
                    "$titleField as title",
                    "$descField as description",
                    'image',
                    'goal_amount',
                    'collected_amount',
                    'start_date',
                    'end_date',
                    'status',
                    'created_at'
                )
                ->get()
                ->map(function ($campaign) {
                    $campaign->remaining_amount = max(0, $campaign->goal_amount - $campaign->collected_amount);
                    return $campaign;
                });
            $campaigns->transform(function ($campaign) use ($locale) {
                $campaign->status_label = $campaign->status?->label($locale) ?? '';
                return $campaign;
            });
            return response()->json([
                'message' => $locale === 'ar' ? 'تم جلب الحملات حسب تاريخ الإضافة بنجاح' : 'Campaigns fetched by creation date successfully',
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


    // user //////////////////////

    public function getAllVisibleCampaignsForUser($mainCategory)
    {
        try {
            $locale = app()->getLocale();
            $titleField = "title_{$locale}";
            $descField = "description_{$locale}";

            $campaigns = Campaign::where('status', \App\Enums\CampaignStatus::Active)
                ->whereHas('category', function ($q) use ($mainCategory) {
                    $q->where('main_category', $mainCategory);
                })
                ->select(
                    'id',
                    'category_id',
                    "$titleField as title",
                    "$descField as description",
                    'image',
                    'goal_amount',
                    'collected_amount',
                    'status'
                )
                ->get()
                ->map(function ($campaign) {
                    $campaign->remaining_amount = max(0, $campaign->goal_amount - $campaign->collected_amount);
                    return $campaign;
                });

            return response()->json([
                'message' => $locale === 'ar' ? 'تم جلب الحملات بنجاح' : 'Campaigns fetched successfully',
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
/*
    public function getAllVisibleCampaignsForUser($mainCategory)
    {
        try {
            $locale = app()->getLocale();
            $titleField = "title_{$locale}";
            $descField = "description_{$locale}";

            // جلب الحملات Active أو Complete (لأن بعض Active ممكن تتحول Complete عند fetch)
            $campaigns = Campaign::whereHas('category', function ($q) use ($mainCategory) {
                $q->where('main_category', $mainCategory);
            })
                ->select(
                    'id',
                    'category_id',
                    "$titleField as title",
                    "$descField as description",
                    'image',
                    'goal_amount',
                    'collected_amount',
                    'status',
                    'end_date',
                    'completed_at'
                )
                ->get()
                ->map(function ($campaign) {

                    // تحديث الحالة تلقائياً قبل عرضها
                    $today = now()->toDateString();

                    if ($campaign->status === 'active' &&
                        ($campaign->collected_amount >= $campaign->goal_amount ||
                            ($campaign->end_date && $campaign->end_date->toDateString() <= $today))
                    ) {
                        $campaign->status = 'complete';
                        if (!$campaign->completed_at) {
                            $campaign->completed_at = now();
                        }
                        $campaign->saveQuietly(); // تحديث الـ DB بدون loop
                    }

                    // حساب المبلغ المتبقي
                    $campaign->remaining_amount = max(0, $campaign->goal_amount - $campaign->collected_amount);

                    return $campaign;
                });

            return response()->json([
                'message' => $locale === 'ar' ? 'تم جلب الحملات بنجاح' : 'Campaigns fetched successfully',
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
*/

    public function getVisibleCampaignByIdForUser($mainCategory='Campaign',$id)
    {
        try {
            $locale = app()->getLocale();
            $titleField = "title_{$locale}";
            $descField = "description_{$locale}";

            $campaign = Campaign::where('id', $id)
                ->where('status', \App\Enums\CampaignStatus::Active)
                ->whereHas('category', function ($q) use ($mainCategory) {
                    $q->where('main_category', $mainCategory);
                })
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
                    'status'
                )
                ->first();

            if (!$campaign) {
                return response()->json([
                    'message' => $locale === 'ar' ? 'الحملة غير موجودة أو غير متاحة' : 'Campaign not found or not visible',
                    'status' => 404
                ], 404);
            }

            $campaign->remaining_amount = max(0, $campaign->goal_amount - $campaign->collected_amount);
            $campaign->status_label = $campaign->status?->label($locale) ?? '';
            $campaign->beneficiaries_count = $campaign->beneficiaries_count ?? 0; // نضمن إنه موجود
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
    public function getVisibleCampaignsByCategoryForUser2( $mainCategory, $categoryId)
    {
        $locale = app()->getLocale();
        $titleField = "title_{$locale}";
        $descField = "description_{$locale}";

        $query = Campaign::where('status', \App\Enums\CampaignStatus::Active)
            ->whereHas('category', function ($q) use ($mainCategory, $categoryId) {
                $q->where('main_category', $mainCategory);
                if ($categoryId) {
                    $q->where('id', $categoryId);
                }
            })
            ->select(
                'id',
                'category_id',
                "$titleField as title",
                "$descField as description",
                'image',
                'goal_amount',
                'collected_amount',
                'status'
            );

        $campaigns = $query->get()->map(function ($campaign) {
            $campaign->remaining_amount = max(0, $campaign->goal_amount - $campaign->collected_amount);
            return $campaign;
        });

        return response()->json([
            'message' => $locale === 'ar' ? 'تم جلب العناصر حسب التصنيف بنجاح' : 'Items by category fetched successfully',
            'data' => $campaigns,
            'status' => 200
        ]);
    }
    public function getVisibleCampaignsByCategoryForUser($mainCategory, $categoryId)
    {
        $locale = app()->getLocale();
        $titleField = "title_{$locale}";
        $descField  = "description_{$locale}";

        $campaigns = Campaign::where('status', \App\Enums\CampaignStatus::Active)
            ->whereHas('category', function ($q) use ($mainCategory, $categoryId) {
                $q->where('main_category', $mainCategory);
                if ($categoryId) {
                    $q->where('id', $categoryId);
                }
            })
            ->with('category') // إذا حابب تجيب معلومات التصنيف
            ->get()
            ->map(function ($campaign) use ($locale, $titleField, $descField) {
                return [
                    'id'              => $campaign->id,
                    'category_id'     => $campaign->category_id,
                    'title'           => $campaign->$titleField,
                    'description'     => $campaign->$descField,
                    'image'           => $campaign->image,
                    'goal_amount'     => $campaign->goal_amount,
                    'collected_amount'=> $campaign->collected_amount,
                    'remaining_amount'=> max(0, $campaign->goal_amount - $campaign->collected_amount),

                ];
            });

        return response()->json([
            'message' => $locale === 'ar' ? 'تم جلب العناصر حسب التصنيف بنجاح' : 'Items by category fetched successfully',
            'data'    => $campaigns,
            'status'  => 200,
        ]);
    }


    public function getVisibleArchivedCampaigns( $mainCategory = 'Campaign')
    {
        $locale = app()->getLocale();

        try {
            $campaigns = Campaign::where('status', \App\Enums\CampaignStatus::Archived)
                ->whereHas('category', function ($q) use ($mainCategory) {
                    $q->where('main_category', $mainCategory);
                })
                ->latest()
                ->get();

            $data = $campaigns->transform(function ($campaign) use ($locale) {
                $titleField = "title_{$locale}";
                $descField = "description_{$locale}";

                $campaign->status_label = $campaign->status?->label($locale) ?? '';

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
                    'status_label' => $campaign->status_label,
                    'image' => $campaign->image,
                ];
            });

            return response()->json([
                'message' => $locale === 'ar' ? 'تم جلب الحملات المؤرشفة بنجاح' : 'Archived campaigns fetched successfully',
                'data' => $data,
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

    public function getVisibleCampaignsByCreationDate($mainCategory = 'Campaign')
    {
        try {
            $locale = app()->getLocale();
            $titleField = "title_{$locale}";
            $descField = "description_{$locale}";

            $campaigns = Campaign::whereHas('category', function ($q) use ($mainCategory) {
                $q->where('main_category', $mainCategory);
            })
                ->where('status', CampaignStatus::Active) // ✅ فقط الحملات الفعالة
                ->orderBy('created_at', 'desc')
                ->select(
                    'id',
                    "$titleField as title",
                    "$descField as description",
                    'image',
                    'goal_amount',
                    'collected_amount',
                    'start_date',
                    'end_date',
                    'status',
                    'created_at'
                )
                ->get()
                ->map(function ($campaign) {
                    $campaign->remaining_amount = max(0, $campaign->goal_amount - $campaign->collected_amount);
                    return $campaign;
                });

            $campaigns->transform(function ($campaign) use ($locale) {
                $campaign->status_label = $campaign->status?->label($locale) ?? '';
               $campaign->created_at_formatted = \Carbon\Carbon::parse($campaign->created_at)->translatedFormat('d F Y');
                return $campaign;
            });


            return response()->json([
                'message' => $locale === 'ar' ? 'تم جلب الحملات حسب تاريخ الإضافة بنجاح' : 'Campaigns fetched by creation date successfully',
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


    ////////////////////////////




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
