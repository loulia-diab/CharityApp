<?php

namespace App\Http\Controllers\Donation_Type\Sponsorship;

use App\Enums\CampaignStatus;
use App\Http\Controllers\Controller;
use App\Models\Beneficiary;
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
            'category_id' => 'required|exists:categories,id',
            'sponsorship_name_en' => 'required|string|max:255',
            'sponsorship_name_ar' => 'required|string|max:255',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
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
                'status' => $request->status ?? CampaignStatus::Pending->value,
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
            $beneficiary = Beneficiary::find($request->beneficiary_id);
            if ($beneficiary) {
                $beneficiary->is_sorted = true;
                $beneficiary->save();
            }
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

            if (!$campaign || !$campaign->category || $campaign->category->main_category !== 'Sponsorship') {
                return response()->json([
                    'message' => $locale === 'ar' ? 'لا يمكن تفعيل كفالة غير من تصنيف Sponsorship' : 'Cannot activate a sponsorship that does not belong to Sponsorship main category',
                    'status' => 400
                ], 400);
            }

            if ($campaign->status === \App\Enums\CampaignStatus::Active) {
                return response()->json([
                    'message' => $locale === 'ar' ? 'الكفالة مفعلة بالفعل' : 'Sponsorship is already active',
                    'status' => 200
                ], 200);
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
            ], 500);
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
            'note' => 'required|string|max:1000'
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
    public function getSponsorshipsByCategory($categoryId)
    {
        $locale = app()->getLocale();
        $titleField = $locale === 'ar' ? 'title_ar' : 'title_en';
        $descField = $locale === 'ar' ? 'description_ar' : 'description_en';

        try {
            $category = Category::findOrFail($categoryId);

            if ($category->main_category !== 'Sponsorship') {
                return response()->json([
                    'message' => $locale === 'ar' ? 'التصنيف المحدد ليس من نوع كفالة' : 'The selected category is not a Sponsorship category',
                    'status' => 400
                ], 400);
            }

            $campaigns = Campaign::with(['sponsorship.beneficiary', 'category'])
                ->where('category_id', $categoryId)
                ->whereHas('category', function ($query) {
                    $query->where('main_category', 'Sponsorship');
                })
                ->latest()
                ->get();

            $data = $campaigns->map(function ($campaign) use ($locale, $titleField, $descField) {
                return [
                    'id' => $campaign->sponsorship->id ?? null,
                    'sponsorship_name' => $campaign->$titleField,
                    'image' => $campaign->image ?? '',
                    'description' => $campaign->$descField ?? null,
                    'goal_amount' => $campaign->goal_amount,
                    'collected_amount' => $campaign->collected_amount,
                    'remaining_amount' => $campaign->goal_amount - $campaign->collected_amount,
                    'beneficiary_id' => $campaign->sponsorship->beneficiary->id ?? null,
                    'beneficiary_name' => $campaign->sponsorship->beneficiary->name ?? null,
                    'status' => $campaign->status->label($locale),
                ];
            })->values();

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
            ], 500);
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
                'remaining_amount' => $campaign->goal_amount - $campaign->collected_amount,
                'beneficiary_id' => $beneficiary->id ?? null,
                'beneficiary_name' => $beneficiary->name ?? null,
                'created_at' => $sponsorship->created_at,
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
        $locale = app()->getLocale();
        $admin = auth('admin')->user();

        if (!$admin) {
            return response()->json([
                'message' => $locale === 'ar' ? 'غير مصرح - للمشرفين فقط' : 'Unauthorized - Admin access only',
                'status' => 401
            ], 401);
        }

        try {
            $sponsorship = Sponsorship::with(['campaign', 'beneficiary'])->findOrFail($id);
            $campaign = $sponsorship->campaign;
            $beneficiary = $sponsorship->beneficiary;

            return response()->json([
                'message' => $locale === 'ar' ? 'تم جلب تفاصيل الكفالة بنجاح' : 'Sponsorship fetched successfully',
                'status' => 200,
                'data' => [
                    'id' => $sponsorship->id,
                    'beneficiary_id' => $sponsorship->beneficiary_id,
                    'beneficiary' => $beneficiary,
                    'category_id' => $campaign?->category_id,
                    'title' => $locale === 'ar' ? $campaign?->title_ar : $campaign?->title_en,
                    'description' => $locale === 'ar' ? $campaign?->description_ar : $campaign?->description_en,
                    'goal_amount' => $campaign?->goal_amount,
                    'collected_amount' => $campaign?->collected_amount,
                    'start_date' => $campaign?->start_date,
                    'end_date' => $campaign?->end_date,
                    'status' => $campaign?->status,
                    'image' => $campaign?->image ? asset('storage/' . $campaign->image) : null,
                    'created_at' => $campaign?->created_at,
                ]
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => $locale === 'ar' ? 'الكفالة غير موجودة' : 'Sponsorship not found',
                'status' => 404
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $locale === 'ar' ? 'حدث خطأ أثناء جلب الكفالة' : 'Error fetching sponsorship',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }
    public function getSponsorshipsByStatus($categoryId, $status)
    {
        $admin = auth('admin')->user();
        $locale = app()->getLocale();

        if (!$admin) {
            return response()->json([
                'message' => $locale === 'ar' ? 'غير مصرح - فقط للمسؤول' : 'Unauthorized - Admin access only',
                'status_code' => 401
            ], 401);
        }

        try {
            if (!in_array($status, \App\Enums\CampaignStatus::values())) {
                return response()->json([
                    'message' => $locale === 'ar' ? 'قيمة الحالة غير صالحة' : 'Invalid status value',
                    'status_code' => 422
                ], 422);
            }

            $titleField = "title_{$locale}";
            $descField = "description_{$locale}";
            $categoryNameField = "name_category_{$locale}";

            // التحقق من الفئة
            $category = Category::where('id', $categoryId)
                ->where('main_category', 'Sponsorship')
                ->first();

            if (!$category) {
                return response()->json([
                    'message' => $locale === 'ar' ? 'الفئة غير موجودة' : 'Category not found',
                    'status_code' => 404
                ], 404);
            }

            // جلب الكفالات المرتبطة بالحملات بنفس الحالة
            $sponsorships = Sponsorship::whereHas('campaign', function ($query) use ($status, $categoryId) {
                $query->where('status', $status)
                    ->where('status', '!=', \App\Enums\CampaignStatus::Archived)
                    ->where('category_id', $categoryId)
                    ->whereHas('category', function ($q) {
                        $q->where('main_category', 'Sponsorship');
                    });
            })
                ->with('campaign')
                ->get()
                ->map(function ($sponsorship) use ($locale, $titleField, $descField) {
                    $campaign = $sponsorship->campaign;
                    return [
                        'id' => $sponsorship->id,
                        'title' => $campaign->$titleField,
                        'description' => $campaign->$descField,
                        'goal_amount' => $campaign->goal_amount,
                        'collected_amount' => $campaign->collected_amount,
                        'start_date' => $campaign->start_date,
                        'end_date' => $campaign->end_date,
                        'status' => $campaign->status,
                        'image' => $campaign->image,
                        'status_label' => $campaign->status->label($locale),
                        'created_at' => $campaign->created_at,
                        'updated_at' => $campaign->updated_at,
                    ];
                });

            return response()->json([
                'status' => $status,
                'category_id' => $category->id,
                'category_name' => $category->$categoryNameField,
                'has_sponsorships' => $sponsorships->isNotEmpty(),
                'data' => $sponsorships,
                'message' => $locale === 'ar' ? 'تم جلب الكفالات بنجاح' : 'Sponsorships fetched successfully',
                'status_code' => 200
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $locale === 'ar' ? 'حدث خطأ أثناء جلب الكفالات' : 'Error fetching sponsorships',
                'error' => $e->getMessage(),
                'status_code' => 500
            ], 500);
        }
    }
    public function getAllSponsorShips()
     {
         $locale = app()->getLocale();

         $sponsorships = Sponsorship::whereHas('campaign.category', function ($q) {
             $q->where('main_category', 'Sponsorship');
         })
             ->with(['campaign', 'beneficiary'])
             ->get();

         $data = $sponsorships->map(function ($sponsorship) use ($locale) {
             $campaign = $sponsorship->campaign;
             $beneficiary = $sponsorship->beneficiary;

             return [
                 'id' => $sponsorship->id,
                 'sponsorship_name' => $locale === 'ar' ? $campaign->title_ar : $campaign->title_en,
                 'image' => $campaign->image ?? '',
                 'description' => $beneficiary->description ?? null,
                 'goal_amount' => $campaign->goal_amount,
                 'collected_amount' => $campaign->collected_amount,
                 'remaining_amount' => $campaign->goal_amount - $campaign->collected_amount,
                 'beneficiary_id' => $beneficiary->id ?? null,
                 'beneficiary_name' => $beneficiary->name ?? null,
                 'start_date' => $campaign->start_date,
                 'end_date' => $campaign->end_date,
                 'created_at' => $sponsorship->created_at->toDateTimeString(),
             ];
         });

         return response()->json([
             'message' => $locale === 'ar' ? 'تم جلب الكفالات بنجاح' : 'Sponsorships fetched successfully',
             'data' => $data,
             'status' => 200
         ]);
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
            $sponsorships = Sponsorship::where('is_permanent', true)
                ->whereHas('campaign.category', function ($q) {
                    $q->where('main_category', 'Sponsorship');
                })
                ->with(['campaign', 'beneficiary'])
                ->orderByDesc('cancelled_at')
                ->get();

            if ($sponsorships->isEmpty()) {
                return response()->json([
                    'message' => $locale === 'ar' ? 'لا توجد كفالات ملغاة حالياً' : 'No cancelled sponsorships available',
                    'status' => 200,
                    'data' => []
                ]);
            }

            $data = $sponsorships->map(function ($sponsorship) use ($locale) {
                $campaign = $sponsorship->campaign;
                $beneficiary = $sponsorship->beneficiary;

                return [
                    'id' => $sponsorship->id,
                    'sponsorship_name' => $locale === 'ar' ? $campaign->title_ar : $campaign->title_en,
                    'image' => $campaign->image ?? null,
                    'description' => $beneficiary->description ?? null,
                    'goal_amount' => $campaign->goal_amount,
                    'collected_amount' => $campaign->collected_amount,
                    'remaining_amount' => $campaign->goal_amount - $campaign->collected_amount,
                    'beneficiary_id' => $beneficiary->id ?? null,
                    'beneficiary_name' => $beneficiary->name ?? null,
                    'cancelled_note' => $sponsorship->cancelled_note,
                    'cancelled_at' => $sponsorship->cancelled_at
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


    // User
    public function getAllVisibleSponsorshipsForUsers($mainCategory)
    {
        try {
            $locale = app()->getLocale();

            $sponsorships = Sponsorship::with('campaign.category', 'beneficiary')
                ->where('is_permanent', false) // على جدول الكفالة مباشرة
                ->whereHas('campaign', function ($query) use ($mainCategory) {
                    $query->whereHas('category', function ($q) use ($mainCategory) {
                        $q->where('main_category', $mainCategory);
                    })
                        ->where('status', CampaignStatus::Active)
                        ->whereColumn('collected_amount', '<', 'goal_amount');
                })
                ->latest()
                ->get();


            $data = $sponsorships->map(function ($sponsorship) use ($locale) {
                $campaign = $sponsorship->campaign;
                return [
                    'id' => $sponsorship->id,
                    'category_id'=>$campaign->category_id,
                    'title' => $locale === 'ar' ? $campaign?->title_ar : $campaign?->title_en,
                    'description' => $locale === 'ar' ? $campaign?->description_ar : $campaign?->description_en,
                    'monthly_amount' => $campaign?->goal_amount,
                    'remaining_amount' => max(0, $campaign?->goal_amount - $campaign?->collected_amount),
                    'image' => $campaign?->image ?? '',
                ];
            });

            return response()->json([
                'message' => $locale === 'ar' ? 'تم جلب الكفالات بنجاح' : 'Visible sponsorships fetched successfully',
                'data' => $data,
                'status' => 200,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $locale === 'ar' ? 'حدث خطأ أثناء جلب الكفالات' : 'Error fetching visible sponsorships',
                'error' => $e->getMessage(),
                'status' => 500
            ]);
        }
    }

    public function getVisibleSponsorshipDetailsForUser($mainCategory, $id)
    {
        try {
            $locale = app()->getLocale();
            $sponsorship = Sponsorship::with('campaign.category', 'beneficiary.beneficiary_request')
                ->where('id', $id)
                ->whereHas('campaign', function ($query) use ($mainCategory) {
                    $query->whereColumn('collected_amount', '<', 'goal_amount')
                        ->where('status', CampaignStatus::Active)
                        ->whereHas('category', function ($catQuery) use ($mainCategory) {
                            $catQuery->where('main_category', $mainCategory);
                        });
                })
                ->firstOrFail();

            $campaign = $sponsorship->campaign;
            $category = $campaign?->category;
            $beneficiary = $sponsorship->beneficiary;
            return response()->json([
                'message' => $locale === 'ar' ? 'تم جلب تفاصيل الكفالة بنجاح' : 'Sponsorship fetched successfully',
                'status' => 200,
                'data' => [
                    'id' => $sponsorship->id,
                    'beneficiary_id' => $sponsorship->beneficiary_id,
                    'gender' => $locale === 'ar' ? $beneficiary?->beneficiary_request?->gender_ar : $beneficiary?->beneficiary_request?->gender_en,
                    'birth_date' => $beneficiary?->beneficiary_request?->birth_date,
                    'type' => $locale === 'ar' ? $campaign->category?->name_category_ar : $campaign->category?->name_category_en,

                    'title' => $locale === 'ar' ? $campaign?->title_ar : $campaign?->title_en,
                    'description' => $locale === 'ar' ? $campaign?->description_ar : $campaign?->description_en,
                    'goal_amount' => $campaign?->goal_amount,
                    'collected_amount' => $campaign?->collected_amount,
                    'remaining_amount' => max(0, $campaign?->goal_amount - $campaign?->collected_amount),
                    'status' => $campaign?->status,
                    'image' => $campaign?->image ?? null,
                    'created_at' => $campaign?->created_at,
                    'campaign_id'=>$campaign->id,
                ]
            ]);


        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => app()->getLocale() === 'ar' ? 'الكفالة غير موجودة' : 'Visible sponsorship not found',
                'status' => 404
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => app()->getLocale() === 'ar' ? 'حدث خطأ أثناء جلب الكفالة' : 'Error fetching visible sponsorship',
                'error' => $e->getMessage(),
                'status' => 500
            ]);
        }
    }


    public function getVisibleSponsorshipsByCategoryForUsers($mainCategory, $categoryId)
    {
        try {
            $locale = app()->getLocale();

            $sponsorships = Sponsorship::with('campaign.category', 'beneficiary')
                ->whereHas('campaign', function ($query) use ($mainCategory, $categoryId) {
                    $query->where('status', CampaignStatus::Active)
                        ->whereColumn('collected_amount', '<', 'goal_amount')
                        ->whereHas('category', function ($catQuery) use ($mainCategory, $categoryId) {
                            $catQuery->where('main_category', $mainCategory)
                                ->where('id', $categoryId); // <-- هنا أضفت شرط الـ ID
                        });
                })
                ->latest()
                ->get();

            $data = $sponsorships->map(function ($sponsorship) use ($locale) {
                $campaign = $sponsorship->campaign;
                return [
                    'id' => $sponsorship->id,
                    'category_id'=>$campaign->category_id,
                    'title' => $locale === 'ar' ? $campaign?->title_ar : $campaign?->title_en,
                    'description' => $locale === 'ar' ? $campaign?->description_ar : $campaign?->description_en,
                    'monthly_amount' => $campaign?->goal_amount,
                    'remaining_amount' => max(0, $campaign?->goal_amount - $campaign?->collected_amount),
                    'image' => $campaign?->image ?? '',
                ];
            });

            return response()->json([
                'message' => $locale === 'ar' ? 'تم جلب الكفالات حسب الفئة' : 'Sponsorships by category fetched successfully',
                'data' => $data,
                'status' => 200,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $locale === 'ar' ? 'حدث خطأ أثناء جلب الكفالات' : 'Error fetching sponsorships by category',
                'error' => $e->getMessage(),
                'status' => 500
            ]);
        }
    }







}
