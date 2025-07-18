<?php

namespace App\Http\Controllers\Donation_Type\Sponsorship;

use App\Enums\CampaignStatus;
use App\Http\Controllers\Controller;
use App\Models\Campaigns\Campaign;
use App\Models\Category;
use App\Models\Sponsorship;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SponsorshipController extends Controller
{
    // Admin
    public function addSponsorship(Request $request)
    {
        $locale = app()->getLocale();

        $request->validate([
            'beneficiary_id' => 'required|exists:beneficiaries,id',
            'sponsorship_name_en' => 'required|string|max:255',
            'sponsorship_name_ar' => 'required|string|max:255',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'goal_amount' => 'numeric|nullable',
            'is_permanent' => 'boolean|nullable',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => ['nullable', Rule::in(CampaignStatus::values())],
        ]);

        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json([
                'message' => $locale === 'ar' ? 'غير مصرح' : 'Unauthorized'
            ], 401);
        }

        $category = Category::where('id', $request->category_id)
            ->where('main_category', 'Sponsorship')
            ->first();

        if (!$category) {
            return response()->json([
                'message' => $locale === 'ar' ? 'التصنيف غير صالح' : 'Invalid category'
            ], 422);
        }

        try {
            $campaign = Campaign::create([
                'title_en' => $request->sponsorship_name_en,
                'title_ar' => $request->sponsorship_name_ar,
                'description_en' => $request->description_en ?? '',
                'description_ar' => $request->description_ar ?? '',
                'category_id' => $request->category_id,
                'goal_amount' => $request->goal_amount ?? 0,
                'collected_amount' => 0,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => $request->status ?? CampaignStatus::Pending,
                'image' => '',
            ]);

            if ($request->hasFile('image')) {
                $imageFile = $request->file('image');
                $ext = $imageFile->getClientOriginalExtension();
                $imageName = 'sponsorship_' . $campaign->id . '.' . $ext;
                $path = $imageFile->storeAs('sponsorship_images', $imageName, 'public');
                $campaign->image = $path;
                $campaign->save();
            }

            $sponsorship = Sponsorship::create([
                'campaign_id' => $campaign->id,
                'beneficiary_id' => $request->beneficiary_id,
                'is_permanent' => $request->is_permanent ?? false,
            ]);

            return response()->json([
                'message' => $locale === 'ar' ? 'تم إنشاء الكفالة والحملة بنجاح' : 'Sponsorship and campaign created successfully',
                'data' => [
                    'sponsorship' => $sponsorship->load('campaign', 'beneficiary'),
                ],
                'status' => 201
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $locale === 'ar' ? 'حدث خطأ أثناء إنشاء الكفالة' : 'Error creating sponsorship',
                'error' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function updateSponsorship(Request $request, $id)
    {
        $admin = auth('admin')->user();
        $locale = app()->getLocale();

        if (!$admin) {
            return response()->json([
                'message' => $locale === 'ar' ? 'غير مصرح' : 'Unauthorized',
                'status' => 401
            ], 401);
        }

        $validated = $request->validate([
            'sponsorship_name_en' => 'nullable|string|max:255',
            'sponsorship_name_ar' => 'nullable|string|max:255',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'goal_amount' => 'nullable|numeric|min:0',
        ]);

        try {
            $sponsorship = Sponsorship::findOrFail($id);
            $campaign = $sponsorship->campaign;

            if (!$campaign->category || $campaign->category->main_category !== 'Sponsorship') {
                return response()->json([
                    'message' => $locale === 'ar' ? 'لا يمكن تعديل كفالة غير من تصنيف Sponsorship' : 'Cannot update a sponsorship not under Sponsorship main category',
                    'status' => 400
                ], 400);
            }

            if (isset($validated['goal_amount']) && $validated['goal_amount'] < $campaign->collected_amount) {
                return response()->json([
                    'message' => $locale === 'ar' ? 'المبلغ المستهدف لا يمكن أن يكون أقل من المبلغ المحصل' : 'Goal amount cannot be less than the collected amount.',
                    'status' => 422
                ], 422);
            }

            if (isset($validated['sponsorship_name_en'])) {
                $campaign->title_en = $validated['sponsorship_name_en'];
            }
            if (isset($validated['sponsorship_name_ar'])) {
                $campaign->title_ar = $validated['sponsorship_name_ar'];
            }
            if (isset($validated['description_en'])) {
                $campaign->description_en = $validated['description_en'];
            }
            if (isset($validated['description_ar'])) {
                $campaign->description_ar = $validated['description_ar'];
            }

            if ($request->hasFile('image')) {
                if ($campaign->image && \Storage::disk('public')->exists($campaign->image)) {
                    \Storage::disk('public')->delete($campaign->image);
                }

                $imageFile = $request->file('image');
                $ext = $imageFile->getClientOriginalExtension();
                $imageName = 'sponsorship_' . $campaign->id . '.' . $ext;
                $path = $imageFile->storeAs('sponsorship_images', $imageName, 'public');
                $campaign->image = $path;
            }

            if (isset($validated['goal_amount'])) {
                $campaign->goal_amount = $validated['goal_amount'];
            }

            $campaign->save();

            return response()->json([
                'message' => $locale === 'ar' ? 'تم تعديل الكفالة بنجاح' : 'Sponsorship updated successfully',
                'data' => $sponsorship->load('campaign', 'beneficiary'),
                'status' => 200
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => $locale === 'ar' ? 'الكفالة غير موجودة' : 'Sponsorship not found',
                'status' => 404
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $locale === 'ar' ? 'حدث خطأ أثناء تعديل الكفالة' : 'Error updating sponsorship',
                'error' => $e->getMessage(),
                'status' => 500
            ]);
        }
    }

    public function activateSponsorship(Request $request, $id)
    {
        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json([
                'message' => app()->getLocale() === 'ar' ? 'غير مصرح' : 'Unauthorized',
                'status' => 401
            ], 401);
        }

        $locale = app()->getLocale();

        try {
            $sponsorship = Sponsorship::findOrFail($id);
            $campaign = $sponsorship->campaign;

            if (!$campaign->category || $campaign->category->main_category !== 'Sponsorship') {
                return response()->json([
                    'message' => $locale === 'ar' ? 'لا يمكن تفعيل كفالة غير من تصنيف Sponsorship' : 'Cannot activate a sponsorship that does not belong to Sponsorship main category',
                    'status' => 400
                ], 400);
            }

            if ($campaign->status !== \App\Enums\CampaignStatus::Pending) {
                return response()->json([
                    'message' => $locale === 'ar' ? 'لا يمكن تفعيل الكفالة إلا إذا كانت في حالة انتظار' : 'Cannot activate a sponsorship unless it is pending',
                    'status' => 400
                ], 400);
            }

            $campaign->status = \App\Enums\CampaignStatus::Active;
            $campaign->save();

            return response()->json([
                'message' => $locale === 'ar' ? 'تم تفعيل الكفالة بنجاح' : 'Sponsorship activated successfully',
                'data' => $sponsorship->load('campaign', 'beneficiary'),
                'status' => 200
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => $locale === 'ar' ? 'الكفالة غير موجودة' : 'Sponsorship not found',
                'status' => 404
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $locale === 'ar' ? 'حدث خطأ أثناء تفعيل الكفالة' : 'Error activating sponsorship',
                'error' => $e->getMessage(),
                'status' => 500
            ]);
        }
    }
    public function cancelledSponsorship(Request $request, $id)
    {
        $admin = auth('admin')->user();
        $locale = app()->getLocale();

        if (!$admin) {
            return response()->json([
                'message' => $locale === 'ar' ? 'غير مصرح' : 'Unauthorized',
                'status' => 401
            ], 401);
        }

        $validated = $request->validate([
            'note' => 'nullable|string|max:1000'
        ]);

        try {
            $sponsorship = Sponsorship::findOrFail($id);
            $campaign = $sponsorship->campaign;

            if (!$campaign || !$campaign->category || $campaign->category->main_category !== 'Sponsorship') {
                return response()->json([
                    'message' => $locale === 'ar' ? 'لا يمكن إلغاء كفالة غير تابعة لتصنيف Sponsorship' : 'Cannot cancel a sponsorship not under Sponsorship main category',
                    'status' => 400
                ], 400);
            }

            if ($sponsorship->is_permanent) {
                return response()->json([
                    'message' => $locale === 'ar' ? 'الكفالة ملغاة بالفعل بشكل دائم' : 'Sponsorship is already permanently cancelled',
                    'status' => 400
                ], 400);
            }

            $sponsorship->is_permanent = true;
            $sponsorship->cancelled_note = $validated['note'] ?? null;
            $sponsorship->cancelled_at = now();
            $sponsorship->save();

            return response()->json([
                'message' => $locale === 'ar' ? 'تم إلغاء الكفالة بشكل دائم' : 'Sponsorship permanently cancelled',
                'status' => 200,
                'data' => $sponsorship->load('campaign', 'beneficiary')
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => $locale === 'ar' ? 'الكفالة غير موجودة' : 'Sponsorship not found',
                'status' => 404
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $locale === 'ar' ? 'حدث خطأ أثناء إلغاء الكفالة' : 'Error cancelling sponsorship',
                'error' => $e->getMessage(),
                'status' => 500
            ]);
        }
    }
    public function getSponsorshipsByCategory(Request $request, $categoryId)
    {
        $locale = app()->getLocale();

        try {
            $category = Category::findOrFail($categoryId);

            if ($category->main_category !== 'Sponsorship') {
                return response()->json([
                    'message' => $locale === 'ar' ? 'التصنيف المحدد ليس من نوع كفالة' : 'The selected category is not a Sponsorship category',
                    'status' => 400
                ], 400);
            }

            // السماح فقط بالحالات التالية
            $allowedStatuses = [
                \App\Enums\CampaignStatus::Pending,
                \App\Enums\CampaignStatus::Active,
                \App\Enums\CampaignStatus::Complete
            ];

            $status = $request->input('status');

            $campaignsQuery = Campaign::with('sponsorship.beneficiary')
                ->where('category_id', $categoryId)
                ->whereHas('category', function ($query) {
                    $query->where('main_category', 'Sponsorship');
                });

            // إذا تم تمرير status وكان مسموحًا
            if ($status !== null) {
                if (!in_array($status, $allowedStatuses)) {
                    return response()->json([
                        'message' => $locale === 'ar' ? 'الحالة غير صالحة' : 'Invalid status value',
                        'status' => 400
                    ], 400);
                }

                $campaignsQuery->where('status', $status);
            } else {
                $campaignsQuery->whereIn('status', $allowedStatuses);
            }

            $campaigns = $campaignsQuery->latest()->get();


            $data = $campaigns->map(function ($campaign) use ($locale) {
                return [
                    'id' => $campaign->sponsorship->id ?? null,
                    'sponsorship_name' => $locale === 'ar' ? $campaign->title_ar : $campaign->title_en,
                    'image' => $campaign->image ? asset('storage/' . $campaign->image) : null,
                    'description' => $campaign->sponsorship->beneficiary->description ?? null,
                    'goal_amount' => $campaign->goal_amount,
                    'collected_amount' => $campaign->collected_amount,
                    'remaining_amount' => $campaign->remaining_amount,
                    'beneficiary_id' => $campaign->sponsorship->beneficiary->id ?? null,
                    'beneficiary_name' => $campaign->sponsorship->beneficiary->name ?? null,
                    'status' => $statusTranslations[$locale][$campaign->status] ?? $campaign->status,
                ];
            });

            return response()->json([
                'message' => $locale === 'ar' ? 'تم جلب الكفالات بنجاح' : 'Sponsorships fetched successfully',
                'data' => $data,
                'status' => 200
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => $locale === 'ar' ? 'التصنيف غير موجود' : 'Category not found',
                'status' => 404
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $locale === 'ar' ? 'حدث خطأ أثناء جلب الكفالات' : 'Error fetching sponsorships',
                'error' => $e->getMessage(),
                'status' => 500
            ]);
        }
    }

    public function getCancelledSponsorships()
    {
        $locale = app()->getLocale();
        $admin = auth('admin')->user();

        if (!$admin) {
            return response()->json([
                'message' => $locale === 'ar' ? 'غير مصرح' : 'Unauthorized',
                'status' => 401
            ], 401);
        }

        try {
            $sponsorships = Sponsorship::whereHas('campaign.category', function ($q) {
                $q->where('main_category', 'Sponsorship');
            })->with(['campaign', 'beneficiary'])
                ->where('is_permanent', true)
                ->orderByDesc('cancelled_at')
                ->get();

            $data = $sponsorships->map(function ($sponsorship) use ($locale) {
                $campaign = $sponsorship->campaign;

                return [
                    'id' => $sponsorship->id,
                    'sponsorship_name' => $locale === 'ar' ? $campaign->title_ar : $campaign->title_en,
                    'image' => $campaign->image ? asset('storage/' . $campaign->image) : null,
                    'description' => $sponsorship->beneficiary->description ?? null,
                    'goal_amount' => $campaign->goal_amount,
                    'collected_amount' => $campaign->collected_amount,
                    'remaining_amount' => $campaign->remaining_amount,
                    'beneficiary_id' => $sponsorship->beneficiary->id ?? null,
                    'beneficiary_name' => $sponsorship->beneficiary->name ?? null,
                    'cancelled_note' => $sponsorship->cancelled_note,
                    'cancelled_at' => $sponsorship->cancelled_at,
                ];
            });

            return response()->json([
                'message' => $locale === 'ar' ? 'تم جلب الكفالات الملغاة بنجاح' : 'Cancelled sponsorships retrieved successfully',
                'status' => 200,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $locale === 'ar' ? 'حدث خطأ أثناء جلب الكفالات الملغاة' : 'Error retrieving cancelled sponsorships',
                'error' => $e->getMessage(),
                'status' => 500
            ]);
        }
    }

    public function getAllSponsorshipsByCreationDate()
    {
        $locale = app()->getLocale();

        $sponsorships = Sponsorship::whereHas('campaign.category', function ($q) {
            $q->where('main_category', 'Sponsorship');
        })
            ->with(['campaign', 'beneficiary'])
            ->orderByDesc('created_at')
            ->get();

        $data = $sponsorships->map(function ($sponsorship) use ($locale) {
            $campaign = $sponsorship->campaign;
            $beneficiary = $sponsorship->beneficiary;

            return [
                'id' => $sponsorship->id,
                'sponsorship_name' => $locale === 'ar' ? $campaign->title_ar : $campaign->title_en,
                'image' => $campaign->image ? asset('storage/' . $campaign->image) : null,
                'description' => $beneficiary->description ?? null,
                'goal_amount' => $campaign->goal_amount,
                'collected_amount' => $campaign->collected_amount,
                'remaining_amount' => $campaign->remaining_amount,
                'beneficiary_id' => $beneficiary->id ?? null,
                'beneficiary_name' => $beneficiary->name ?? null,
            ];
        });

        return response()->json([
            'message' => $locale === 'ar' ? 'تم جلب الكفالات بنجاح' : 'Sponsorships fetched successfully',
            'data' => $data,
            'status' => 200
        ]);
    }



    public function getSponsorshipDetails($id)
    {
        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json([
                'message' => 'Unauthorized - Admin access only',
            ], 401);
        }

        try {
            $sponsorship = Sponsorship::with(['campaign', 'beneficiary'])->findOrFail($id);
            $campaign = $sponsorship->campaign;

            return response()->json([
                'message' => 'Sponsorship fetched successfully',
                'data' => [
                    'id' => $sponsorship->id,
                    'beneficiary_id' => $sponsorship->beneficiary_id,
                    'beneficiary' => $sponsorship->beneficiary,
                    'category_id' => $campaign?->category_id,
                    'title_en' => $campaign?->title_en,
                    'title_ar' => $campaign?->title_ar,
                    'description_en' => $campaign?->description_en,
                    'description_ar' => $campaign?->description_ar,
                    'goal_amount' => $campaign?->goal_amount,
                    'collected_amount' => $campaign?->collected_amount,
                    'start_date' => $campaign?->start_date,
                    'end_date' => $campaign?->end_date,
                    'status' => $campaign?->status,
                    'image' => $campaign?->image,
                    'created_at' => $campaign?->created_at,
                ],
                'status' => 200
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Sponsorship not found',
                'status' => 404
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching sponsorship',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }




    public function getSponsorShipsByStatus(Request $request, $id)
    {
        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        try {
            $sponsorship = Sponsorship::findOrFail($id);
            $campaign = $sponsorship->campaign;

            if (!$campaign) {
                return response()->json(['message' => 'Associated campaign not found'], 404);
            }

            $campaign->status = \App\Enums\CampaignStatus::Archived;
            $campaign->save();

            return response()->json([
                'message' => 'Sponsorship archived successfully',
                'data' => $sponsorship,
                'status' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error archiving sponsorship',
                'error' => $e->getMessage(),
                'status' => 500
            ]);
        }
    }




    // User
    public function getAllVisibleSponsorshipsForUsers(Request $request)
    {
        try {

            $sponsorships = Sponsorship::with('campaign', 'beneficiary')
                ->whereHas('campaign', function ($query) {
                    $query->whereColumn('collected_amount', '<', 'goal_amount')
                        ->where('status', CampaignStatus::Active);
                })
                ->latest()
              ->get();

            $data = $sponsorships->map(function ($sponsorship) {
                $campaign = $sponsorship->campaign;
                return [
                    'id' => $sponsorship->id,
                    'beneficiary_id' => $sponsorship->beneficiary_id,
                    // name
                    'beneficiary' => $sponsorship->beneficiary,
                    'campaign_id' => $campaign?->id,
                    'title_en' => $campaign?->title_en,
                    'title_ar' => $campaign?->title_ar,
                    'description_en' => $campaign?->description_en,
                    'description_ar' => $campaign?->description_ar,
                    'category_id' => $campaign?->category_id,
                    'monthly_amount' => $campaign?->goal_amount,
                   //remaining amount
                    'image' => $campaign?->image,
                ];
            });

            return response()->json([
                'message' => 'Visible sponsorships fetched successfully',
                'data' => $data,
                'status' => 200,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching visible sponsorships',
                'error' => $e->getMessage(),
                'status' => 500
            ]);
        }
    }
    public function getVisibleSponsorshipDetailsForUser($id)
    {
        try {
            $sponsorship = Sponsorship::with('campaign', 'beneficiary')
                ->where('id', $id)
                ->whereHas('campaign', function ($query) {
                    $query->whereColumn('collected_amount', '<', 'goal_amount')
                        ->where('status', CampaignStatus::Active);
                })
                ->firstOrFail();

            $campaign = $sponsorship->campaign;

            return response()->json([
                'message' => 'Visible sponsorship fetched successfully',
                'data' => [
                    'id' => $sponsorship->id,
                    'beneficiary_id' => $sponsorship->beneficiary_id,
                    'beneficiary' => $sponsorship->beneficiary,
                    'campaign_id' => $campaign?->id,
                    'title_en' => $campaign?->title_en,
                    'title_ar' => $campaign?->title_ar,
                    'description_en' => $campaign?->description_en,
                    'description_ar' => $campaign?->description_ar,
                    'category_id' => $campaign?->category_id,
                    'monthly_amount' => $campaign?->goal_amount,
                    //remaining
                    'start_date' => $campaign?->start_date,
                    'end_date' => $campaign?->end_date,
                    'image' => $campaign?->image,
                ],
                'status' => 200,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Visible sponsorship not found',
                'status' => 404
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching visible sponsorship',
                'error' => $e->getMessage(),
                'status' => 500
            ]);
        }
    }

    public function getVisibleSponsorshipsByCategoryForUsers()
    {

    }
    // CATEGORY
    public function getVisibleArchivedSponsorships(Request $request)
    {
        $perPage = $request->get('per_page', 10);

        try {
            $sponsorships = Sponsorship::with('campaign')
                ->whereHas('campaign', function ($query) {
                    $query->where('status', \App\Enums\CampaignStatus::Archived);
                })
                ->latest()
                ->paginate($perPage);

            $data = $sponsorships->map(function ($sponsorship) {
                $campaign = $sponsorship->campaign;
                return [
                    'id' => $sponsorship->id,
                    'beneficiary_id' => $sponsorship->beneficiary_id,
                    'title_en' => $campaign?->title_en,
                    'title_ar' => $campaign?->title_ar,
                    'description_en' => $campaign?->description_en,
                    'description_ar' => $campaign?->description_ar,
                    'category_id' => $campaign?->category_id,
                    'goal_amount' => $campaign?->goal_amount,
                    'collected_amount' => $campaign?->collected_amount,
                    'start_date' => $campaign?->start_date,
                    'end_date' => $campaign?->end_date,
                    'status' => $campaign?->status,
                    'image' => $campaign?->image,
                ];
            });

            return response()->json([
                'message' => 'Archived sponsorships fetched successfully',
                'data' => $data,
                'pagination' => [
                    'current_page' => $sponsorships->currentPage(),
                    'last_page' => $sponsorships->lastPage(),
                    'per_page' => $sponsorships->perPage(),
                    'total' => $sponsorships->total(),
                ],
                'status' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching archived sponsorships',
                'error' => $e->getMessage(),
                'status' => 500
            ]);
        }
    }






}
