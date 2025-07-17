<?php

namespace App\Http\Controllers\Donation_Type\HumanCase;

use App\Enums\CampaignStatus;
use App\Http\Controllers\Controller;
use App\Models\Campaigns\Campaign;
use App\Models\Category;
use App\Models\HumanCase;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class HumanCaseController extends Controller
{
    // Admin
    public function addHumanCase2(Request $request)
    {
        $locale = app()->getLocale();

        $request->validate([
            'beneficiary_id' => 'required|exists:beneficiaries,id',
            'case_name_en' => 'required|string|max:255',
            'case_name_ar' => 'required|string|max:255',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'goal_amount' => 'numeric',
            'is_emergency' => 'boolean',
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
            ->where('main_category', 'HumanCase')
            ->first();

        if (!$category) {
            return response()->json([
                'message' => $locale === 'ar' ? 'التصنيف غير صالح' : 'Invalid category'
            ], 422);
        }

        try {
            // أولاً ننشئ الحملة بدون صورة
            $campaign = Campaign::create([
                'title_en' => $request->case_name_en,
                'title_ar' => $request->case_name_ar,
                'description_en' => $request->description_en ?? '',
                'description_ar' => $request->description_ar ?? '',
                'category_id' => $request->category_id,
                'goal_amount' => $request->goal_amount ?? 0,
                'collected_amount' => 0,
                'start_date' => now(),
                'end_date' => null,
                'status' => $request->status ?? CampaignStatus::Pending,
                'image' => '', // بدون صورة في البداية
            ]);

            // إذا في صورة، نخزنها مع اسم يحوي الـ ID
            if ($request->hasFile('image')) {
                $imageFile = $request->file('image');
                $ext = $imageFile->getClientOriginalExtension();
                $imageName = 'human_case_' . $campaign->id . '.' . $ext;
                $path = $imageFile->storeAs('human_case_images', $imageName, 'public');

                // نحدث صورة الحملة بالمسار الجديد
                $campaign->image = $path;
                $campaign->save();
            }

            // إنشاء الحالة الإنسانية وربطها بالحملة والمستفيد
            $humanCase = HumanCase::create([
                'campaign_id' => $campaign->id,
                'beneficiary_id' => $request->beneficiary_id,
                'is_emergency' => $request->is_emergency ?? false,
            ]);

            return response()->json([
                'message' => $locale === 'ar' ? 'تم إنشاء الحالة الإنسانية والحملة بنجاح' : 'Human case and campaign created successfully',
                'data' => [
                    'human_case' => $humanCase->load('campaign', 'beneficiary'),
                ],
                'status' => 201
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $locale === 'ar' ? 'حدث خطأ أثناء إنشاء الحالة الإنسانية' : 'Error creating human case',
                'error' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }
    public function addHumanCase(Request $request)
    {
        $locale = app()->getLocale();

        $request->validate([
            'beneficiary_id' => 'required|exists:beneficiaries,id',
            'case_name_en' => 'required|string|max:255',
            'case_name_ar' => 'required|string|max:255',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'goal_amount' => 'numeric',
            'is_emergency' => 'boolean',
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
            ->where('main_category', 'HumanCase')
            ->first();

        if (!$category) {
            return response()->json([
                'message' => $locale === 'ar' ? 'التصنيف غير صالح' : 'Invalid category'
            ], 422);
        }

        try {
            // إنشاء الحملة بدون صورة أولًا
            $campaign = Campaign::create([
                'title_en' => $request->case_name_en,
                'title_ar' => $request->case_name_ar,
                'description_en' => $request->description_en ?? '',
                'description_ar' => $request->description_ar ?? '',
                'category_id' => $request->category_id,
                'goal_amount' => $request->goal_amount ?? 0,
                'collected_amount' => 0,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => $request->status ?? CampaignStatus::Pending,
                'image' => '', // بدون صورة في البداية
            ]);

            // إذا في صورة، رفعها باسم يحتوي رقم ال ID
            if ($request->hasFile('image')) {
                $imageFile = $request->file('image');
                $ext = $imageFile->getClientOriginalExtension();
                $imageName = 'human_case_' . $campaign->id . '.' . $ext;
                $path = $imageFile->storeAs('human_case_images', $imageName, 'public');

                $campaign->image = $path;
                $campaign->save();
            }

            // إنشاء الحالة الإنسانية وربطها بالحملة والمستفيد
            $humanCase = HumanCase::create([
                'campaign_id' => $campaign->id,
                'beneficiary_id' => $request->beneficiary_id,
                'is_emergency' => $request->is_emergency ?? false,
            ]);

            return response()->json([
                'message' => $locale === 'ar' ? 'تم إنشاء الحالة الإنسانية والحملة بنجاح' : 'Human case and campaign created successfully',
                'data' => [
                    'human_case' => $humanCase->load('campaign', 'beneficiary'),
                ],
                'status' => 201
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $locale === 'ar' ? 'حدث خطأ أثناء إنشاء الحالة الإنسانية' : 'Error creating human case',
                'error' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }


    public function updateHumanCase(Request $request, $id)
    {
        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json([
                'message' => app()->getLocale() === 'ar' ? 'غير مصرح' : 'Unauthorized',
                'error' => '',
                'status' => 401
            ], 401);
        }

        $locale = app()->getLocale();

        $validated = $request->validate([
            'case_name_en' => 'nullable|string|max:255',
            'case_name_ar' => 'nullable|string|max:255',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'goal_amount' => 'nullable|numeric|min:0',
        ]);

        try {
            $humanCase = HumanCase::findOrFail($id);
            $campaign = $humanCase->campaign;

            if (!$campaign->category || $campaign->category->main_category !== 'HumanCase') {
                return response()->json([
                    'message' => $locale === 'ar' ? 'لا يمكن تعديل حالة إنسانية غير من تصنيف HumanCase' : 'Cannot update a human case that does not belong to HumanCase main category',
                    'status' => 400
                ], 400);
            }

            if (isset($validated['goal_amount']) && $validated['goal_amount'] < $campaign->collected_amount) {
                return response()->json([
                    'message' => $locale === 'ar' ? 'المبلغ المستهدف لا يمكن أن يكون أقل من المبلغ المحصل' : 'Goal amount cannot be less than the collected amount.',
                    'error' => '',
                    'status' => 422
                ], 422);
            }

            // تحديث الحقول العربية والإنجليزية
            if (isset($validated['case_name_en'])) {
                $campaign->title_en = $validated['case_name_en'];
            }
            if (isset($validated['case_name_ar'])) {
                $campaign->title_ar = $validated['case_name_ar'];
            }
            if (isset($validated['description_en'])) {
                $campaign->description_en = $validated['description_en'];
            }
            if (isset($validated['description_ar'])) {
                $campaign->description_ar = $validated['description_ar'];
            }

            // إدارة الصورة مع حذف القديمة إذا تم رفع صورة جديدة
            if ($request->hasFile('image')) {
                if ($campaign->image && \Storage::disk('public')->exists($campaign->image)) {
                    \Storage::disk('public')->delete($campaign->image);
                }

                $imageFile = $request->file('image');
                $ext = $imageFile->getClientOriginalExtension();
                $imageName = 'human_case_' . $campaign->id . '.' . $ext;
                $path = $imageFile->storeAs('human_case_images', $imageName, 'public');
                $campaign->image = $path;
            }

            if (isset($validated['goal_amount'])) {
                $campaign->goal_amount = $validated['goal_amount'];
            }

            $campaign->save();

            return response()->json([
                'message' => $locale === 'ar' ? 'تم تعديل الحالة الإنسانية بنجاح' : 'Human case updated successfully',
                'data' => $humanCase->load('campaign', 'beneficiary'),
                'status' => 200
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => $locale === 'ar' ? 'الحالة الإنسانية غير موجودة' : 'Human case not found',
                'status' => 404
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $locale === 'ar' ? 'حدث خطأ أثناء تعديل الحالة الإنسانية' : 'Error updating human case',
                'error' => $e->getMessage(),
                'status' => 500
            ]);
        }
    }
    public function getAllHumanCases2(Request $request)
    {
        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json([
                'message' => app()->getLocale() === 'ar' ? 'غير مصرح - فقط للمسؤول' : 'Unauthorized - Admin access only',
            ], 401);
        }

        try {
            $locale = app()->getLocale();
            $titleField = "title_{$locale}";
            $descField = "description_{$locale}";

            // جلب الحالات الإنسانية مع الحملة والمستفيد مع شرط main_category = 'HumanCase' عبر علاقة category
            $humanCases = HumanCase::whereHas('campaign.category', function ($q) {
                $q->where('main_category', 'HumanCase');
            })
                ->with(['campaign.category', 'beneficiary'])
                ->latest()
                ->get();

            $formattedCases = $humanCases->map(function ($humanCase) use ($locale, $titleField, $descField) {
                $campaign = $humanCase->campaign;

                return [
                    'id' => $humanCase->id,
                    'is_emergency' => $humanCase->is_emergency,
                    'title' => $campaign ? $campaign->$titleField : null,
                    'description' => $campaign ? $campaign->$descField : null,
                    'category_id' => $campaign ? $campaign->category_id : null,
                    'goal_amount' => $campaign ? $campaign->goal_amount : null,
                    'collected_amount' => $campaign ? $campaign->collected_amount : null,
                    'start_date' => $campaign ? $campaign->start_date : null,
                    'end_date' => $campaign ? $campaign->end_date : null,
                    'status_label' => $campaign && $campaign->status ? CampaignStatus::from($campaign->status)->label($locale) : null,
                    'image' => $campaign ? $campaign->image : null,
                ];
            });

            return response()->json([
                'message' => $locale === 'ar' ? 'تم جلب الحالات الإنسانية بنجاح' : 'Human cases fetched successfully',
                'data' => $formattedCases,
                'status' => 200
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => app()->getLocale() === 'ar' ? 'حدث خطأ أثناء جلب الحالات الإنسانية' : 'Error fetching human cases',
                'error' => $e->getMessage(),
                'status' => 500
            ]);
        }
    }

    public function getAllHumanCases(Request $request)
    {
        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json([
                'message' => app()->getLocale() === 'ar' ? 'غير مصرح - فقط للمسؤول' : 'Unauthorized - Admin access only',
            ], 401);
        }

        try {
            $locale = app()->getLocale();
            $titleField = "title_{$locale}";
            $descField = "description_{$locale}";

            // جلب الحالات الإنسانية مع الحملة والمستفيد مع شرط main_category = 'HumanCase' عبر علاقة category
            $humanCases = HumanCase::whereHas('campaign.category', function ($q) {
                $q->where('main_category', 'HumanCase');
            })
                ->with(['campaign', 'beneficiary'])
                ->latest()
                ->get();

            $formattedCases = $humanCases->map(function ($humanCase) use ($locale, $titleField, $descField) {
                $campaign = $humanCase->campaign;
                return [
                    'id' => $humanCase->id,
                    'is_emergency' => $humanCase->is_emergency,
                    'case_name' => $campaign ? $campaign->$titleField : null,  // هنا اسم الحالة حسب اللغة
                    'description' => $campaign ? $campaign->$descField : null,
                    'category_id' => $campaign ? $campaign->category_id : null,
                    'goal_amount' => $campaign ? $campaign->goal_amount : null,
                    'collected_amount' => $campaign ? $campaign->collected_amount : null,
                    'status_label' => $campaign->status->label($locale),
                    'image' => $campaign ? $campaign->image : null,
                ];
            });

            return response()->json([
                'message' => $locale === 'ar' ? 'تم جلب الحالات الإنسانية بنجاح' : 'Human cases fetched successfully',
                'data' => $formattedCases,
                'status' => 200
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => app()->getLocale() === 'ar' ? 'حدث خطأ أثناء جلب الحالات الإنسانية' : 'Error fetching human cases',
                'error' => $e->getMessage(),
                'status' => 500
            ]);
        }
    }

    public function getHumanCaseDetails($id)
    {
        $locale = app()->getLocale();
        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json([
                'message' => $locale === 'ar' ? 'غير مصرح - فقط للمسؤول' : 'Unauthorized - Admin access only',
            ], 401);
        }

        $titleField = "title_{$locale}";
        $descField = "description_{$locale}";

        // جلب الحالة مع الحملة بشرط أن يكون main_category = 'HumanCase'
        $humanCase = HumanCase::whereHas('campaign.category', function ($q) {
            $q->where('main_category', 'HumanCase');
        })
            ->with(['campaign' => function($query) use ($titleField, $descField) {
                $query->select(
                    'id',
                    'category_id',
                    "{$titleField} as title",
                    "{$descField} as description",
                    'goal_amount',
                    'collected_amount',
                    'start_date',
                    'end_date',
                    'image',
                    'status'
                );
            }])
            ->find($id);

        if (!$humanCase || !$humanCase->campaign) {
            return response()->json([
                'message' => $locale === 'ar' ? 'لم يتم العثور على الحالة الإنسانية' : 'Human case not found',
                'status' => 404
            ], 404);
        }

        $campaign = $humanCase->campaign;
        $campaign->status_label = $campaign->status->label($locale);

        return response()->json([
            'message' => $locale === 'ar' ? 'تم جلب تفاصيل الحالة الإنسانية بنجاح' : 'Human case fetched successfully',
            'data' => [
                'id' => $humanCase->id,
                'is_emergency' => $humanCase->is_emergency,
                'title' => $campaign->title,
                'description' => $campaign->description,
                'category_id' => $campaign->category_id,
                'goal_amount' => $campaign->goal_amount,
                'collected_amount' => $campaign->collected_amount,
                'start_date' => $campaign->start_date,
                'end_date' => $campaign->end_date,
                'status_label' => $campaign->status_label,
                'image' => $campaign->image,
            ],
            'status' => 200
        ]);
    }


    public function activateEmergency(Request $request, $id)
    {
        $locale = app()->getLocale();

        try {
            $humanCase = HumanCase::findOrFail($id);
            $humanCase->is_emergency = true;
            $humanCase->save();

            return response()->json([
                'message' => $locale === 'ar' ? 'تم تحديث حالة الطوارئ بنجاح' : 'Human case emergency status updated successfully',
                'data' => $humanCase,
                'status' => 200
            ]);
        }
        catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => $locale === 'ar' ? 'الحالة الإنسانية غير موجودة' : 'Human case not found',
                'status' => 404
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $locale === 'ar' ? 'حدث خطأ أثناء تحديث حالة الطوارئ' : 'Error updating emergency status',
                'error' => $e->getMessage(),
                'status' => 500
            ]);
        }
    }

    public function getEmergencyHumanCases()
    {
        $locale = app()->getLocale();

        try {
            $emergencyCases = HumanCase::where('is_emergency', true)
                ->whereHas('campaign.category', function ($q) {
                    $q->where('main_category', 'HumanCase');
                })
                ->with('campaign')
                ->latest()
                ->get()
                ->map(function ($humanCase) use ($locale) {
                    $campaign = $humanCase->campaign;
                    $titleField = "title_{$locale}";
                    $descField = "description_{$locale}";

                    return [
                        'id' => $humanCase->id,
                        'is_emergency' => $humanCase->is_emergency,
                        'title' => $campaign ? $campaign->$titleField : null,
                        'description' => $campaign ? $campaign->$descField : null,
                        'category_id' => $campaign ? $campaign->category_id : null,
                        'goal_amount' => $campaign ? $campaign->goal_amount : null,
                        'collected_amount' => $campaign ? $campaign->collected_amount : null,
                        'start_date' => $campaign ? $campaign->start_date : null,
                        'end_date' => $campaign ? $campaign->end_date : null,
                        'status' => $campaign ? $campaign->status : null,
                        'image' => $campaign ? $campaign->image : null,
                    ];
                });

            return response()->json([
                'message' => $locale === 'ar' ? 'تم جلب الحالات الإنسانية الطارئة بنجاح' : 'Emergency human cases fetched successfully',
                'data' => $emergencyCases,
                'status' => 200
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $locale === 'ar' ? 'حدث خطأ أثناء جلب الحالات الطارئة' : 'Error fetching emergency human cases',
                'error' => $e->getMessage(),
                'status' => 500
            ]);
        }
    }

    // خليها لبعدين
    public function getHumanCasesByStatus(Request $request)
    {
        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json([
                'message' => app()->getLocale() === 'ar' ? 'غير مصرح - فقط للمسؤول' : 'Unauthorized - Admin access only',
                'status_code' => 401
            ], 401);
        }

        try {
            $validated = $request->validate([
                'status' => ['required', Rule::in(CampaignStatus::values())],
            ]);

            $locale = app()->getLocale();
            $titleField = "title_{$locale}";
            $descField = "description_{$locale}";

            $cases = HumanCase::whereHas('campaign', function ($query) use ($validated) {
                $query->where('status', $validated['status'])
                    ->whereHas('category', function ($q) {
                        $q->where('main_category', 'HumanCase');
                    });
            })
                ->with('campaign')
                ->get()
                ->map(function ($case) use ($locale, $titleField, $descField) {
                    $campaign = $case->campaign;
                    return [
                        'id' => $case->id,
                        'beneficiary_id' => $case->beneficiary_id,
                        'is_emergency' => $case->is_emergency,
                        'title' => $campaign->$titleField,
                        'description' => $campaign->$descField,
                        'category_id' => $campaign->category_id,
                        'goal_amount' => $campaign->goal_amount,
                        'collected_amount' => $campaign->collected_amount,
                        'start_date' => $campaign->start_date,
                        'end_date' => $campaign->end_date,
                        'status' => $campaign->status,
                        'image' => $campaign->image,
                        'status_label' => CampaignStatus::from($campaign->status)->label($locale),
                    ];
                });

            return response()->json([
                'status' => $validated['status'],
                'message' => $locale === 'ar' ? 'تم جلب الحالات بنجاح' : 'Human cases fetched successfully',
                'data' => $cases,
                'status_code' => 200
            ], 200);

        } catch (\Exception $e) {
            $locale = app()->getLocale();
            return response()->json([
                'message' => $locale === 'ar' ? 'حدث خطأ أثناء جلب الحالات' : 'Error fetching human cases',
                'error' => $e->getMessage(),
                'status_code' => 500
            ], 500);
        }
    }

    public function getHumanCasesByCategory($categoryId)
    {
        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json([
                'message' => app()->getLocale() === 'ar' ? 'غير مصرح - فقط للمسؤول' : 'Unauthorized - Admin access only',
                'status' => 401
            ], 401);
        }

        try {
            $locale = app()->getLocale();
            $titleField = "title_{$locale}";
            $descField = "description_{$locale}";

            $cases = HumanCase::whereHas('campaign', function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId)
                    ->whereHas('category', function ($q) {
                        $q->where('main_category', 'HumanCase');
                    });
            })
                ->with('campaign')
                ->get()
                ->map(function ($case) use ($locale, $titleField, $descField) {
                    $campaign = $case->campaign;
                    return [
                        'id' => $case->id,
                        'beneficiary_id' => $case->beneficiary_id,
                        'is_emergency' => $case->is_emergency,
                        'title' => $campaign->$titleField,
                        'description' => $campaign->$descField,
                        'category_id' => $campaign->category_id,
                        'goal_amount' => $campaign->goal_amount,
                        'collected_amount' => $campaign->collected_amount,
                        'start_date' => $campaign->start_date,
                        'end_date' => $campaign->end_date,
                        'image' => $campaign->image,
                        'status_label' => $campaign->status->label($locale),
                    ];
                });

            return response()->json([
                'message' => $locale === 'ar' ? 'تم جلب الحالات حسب التصنيف بنجاح' : 'Human cases by category fetched successfully',
                'data' => $cases,
                'status' => 200
            ]);
        } catch (\Exception $e) {
            $locale = app()->getLocale();
            return response()->json([
                'message' => $locale === 'ar' ? 'حدث خطأ أثناء جلب الحالات' : 'Error fetching human cases',
                'error' => $e->getMessage(),
                'status' => 500
            ]);
        }
    }


    public function archiveHumanCase(Request $request, $id)
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
            $humanCase = HumanCase::findOrFail($id);
            $campaign = $humanCase->campaign;

            if (!$campaign->category || $campaign->category->main_category !== 'HumanCase') {
                return response()->json([
                    'message' => app()->getLocale() === 'ar' ? 'لا يمكن أرشفة حالة إنسانية غير من تصنيف HumanCase' : 'Cannot archive a human case that does not belong to HumanCase main category',
                    'status' => 400
                ], 400);
            }

            if (in_array($campaign->status, [
                \App\Enums\CampaignStatus::Archived,
                \App\Enums\CampaignStatus::Complete
            ])) {
                return response()->json([
                    'message' => app()->getLocale() === 'ar' ? 'لا يمكن أرشفة حالة إنسانية مؤرشفة أو مكتملة' : 'Cannot archive a Human Case that is already archived or complete',
                    'status' => 400
                ], 400);
            }

            $campaign->status = \App\Enums\CampaignStatus::Archived;
            $campaign->save();

            return response()->json([
                'message' => $locale === 'ar' ? 'تم أرشفة الحالة الإنسانية بنجاح' : 'Human case archived successfully',
                'data' => $humanCase,
                'status' => 200
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => $locale === 'ar' ? 'الحالة الإنسانية غير موجودة' : 'Human case not found',
                'status' => 404
            ], 404);
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => $locale === 'ar' ? 'حدث خطأ أثناء أرشفة الحالة الإنسانية' : 'Error archiving human case',
                'error' => $e->getMessage(),
                'status' => 500
            ]);
        }
    }


    public function activateHumanCase(Request $request, $id)
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
            $humanCase = HumanCase::findOrFail($id);
            $campaign = $humanCase->campaign;

            if (!$campaign->category || $campaign->category->main_category !== 'HumanCase') {
                return response()->json([
                    'message' => $locale === 'ar' ? 'لا يمكن تفعيل حالة إنسانية غير من تصنيف HumanCase' : 'Cannot activate a human case that does not belong to HumanCase main category',
                    'status' => 400
                ], 400);
            }

            if (!in_array($campaign->status, [
                \App\Enums\CampaignStatus::Pending,
            ])) {
                return response()->json([
                    'message' => $locale === 'ar' ? 'لا يمكن تفعيل الحملة إلا إذا كانت في حالة انتظار' : 'Cannot activate a campaign unless it is pending or archived',
                    'status' => 400
                ], 400);
            }

            $campaign->status = \App\Enums\CampaignStatus::Active;
            $campaign->save();

            return response()->json([
                'message' => $locale === 'ar' ? 'تم تفعيل الحالة الإنسانية بنجاح' : 'Human case activated successfully',
                'data' => $humanCase,
                'status' => 200
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => $locale === 'ar' ? 'الحالة الإنسانية غير موجودة' : 'Human case not found',
                'status' => 404
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $locale === 'ar' ? 'حدث خطأ أثناء تفعيل الحالة الإنسانية' : 'Error activating human case',
                'error' => $e->getMessage(),
                'status' => 500
            ]);
        }
    }
    public function getArchivedHumanCases(Request $request)
    {
        $locale = app()->getLocale();
        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json([
                'message' => $locale === 'ar' ? 'غير مصرح' : 'Unauthorized'
            ], 401);
        }
        $titleField = "title_{$locale}";
        $descField = "description_{$locale}";

        try {
            $humanCases = HumanCase::with(['campaign' => function ($query) use ($titleField, $descField) {
                $query->select(
                    'id',
                    'category_id',
                    'goal_amount',
                    'collected_amount',
                    'start_date',
                    'end_date',
                    'status',
                    'image',
                    "{$titleField} as title",
                    "{$descField} as description"
                )->whereHas('category', function ($q) {
                    $q->where('main_category', 'HumanCase');
                });
            }])
                ->whereHas('campaign', function ($query) {
                    $query->where('status', \App\Enums\CampaignStatus::Archived)
                        ->whereHas('category', function ($q) {
                            $q->where('main_category', 'HumanCase');
                        });
                })
                ->latest()
                ->get();

            $data = $humanCases->map(function ($humanCase) use ($locale) {
                $campaign = $humanCase->campaign;

                return [
                    'id' => $humanCase->id,
                    'is_emergency' => $humanCase->is_emergency,
                    'title' => $campaign?->title,
                    'description' => $campaign?->description,
                    'category_id' => $campaign?->category_id,
                    'goal_amount' => $campaign?->goal_amount,
                    'collected_amount' => $campaign?->collected_amount,
                    'start_date' => $campaign?->start_date,
                    'end_date' => $campaign?->end_date,
                    'image' => $campaign?->image,
                ];
            });

            return response()->json([
                'message' => $locale === 'ar' ? 'تم جلب الحالات المؤرشفة بنجاح' : 'Archived human cases fetched successfully',
                'data' => $data,
                'status' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $locale === 'ar' ? 'حدث خطأ أثناء جلب الحالات المؤرشفة' : 'Error fetching archived human cases',
                'error' => $e->getMessage(),
                'status' => 500
            ]);
        }
    }


    // User ///////////////////////////// uuuuuuuuuuuuuuuuuuuuuu

    public function getAllVisibleHumanCasesForUser($mainCategory)
    {
        $locale = app()->getLocale();

        if ($mainCategory !== 'HumanCase') {
            return response()->json([
                'message' => $locale === 'ar' ? 'نوع التصنيف غير صالح' : 'Invalid main category type',
                'status' => 400,
            ], 400);
        }

        try {
            $titleField = "title_{$locale}";
            $descField = "description_{$locale}";

            $humanCases = \App\Models\HumanCase::with(['campaign' => function ($query) use ($titleField, $descField, $mainCategory) {
                $query->where('status', \App\Enums\CampaignStatus::Active)
                    ->whereHas('category', function ($q) use ($mainCategory) {
                        $q->where('main_category', $mainCategory);
                    })
                    ->select(
                        'id',
                        'category_id',
                        "{$titleField} as title",
                        "{$descField} as description",
                        'image',
                        'goal_amount',
                        'collected_amount',
                        'start_date',
                        'end_date'
                    );
            }])->get()
                ->filter(fn($humanCase) => $humanCase->campaign !== null)
                ->map(function ($humanCase) {
                    $campaign = $humanCase->campaign;
                    return [
                        'id' => $humanCase->id,
                        'category_id' => $campaign->category_id,
                        'title' => $campaign->title,
                        'description' => $campaign->description,
                        'image' => $campaign->image,
                        'goal_amount' => $campaign->goal_amount,
                        'collected_amount' => $campaign->collected_amount,
                        'remaining_amount' => max(0, $campaign->goal_amount - $campaign->collected_amount),
                        'start_date' => $campaign->start_date,
                        'end_date' => $campaign->end_date,
                    ];
                });

            return response()->json([
                'message' => $locale === 'ar' ? 'تم جلب الحالات الإنسانية بنجاح' : 'Human cases fetched successfully',
                'data' => $humanCases,
                'status' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $locale === 'ar' ? 'حدث خطأ أثناء جلب الحالات' : 'Error fetching human cases',
                'error' => $e->getMessage(),
                'status' => 500
            ]);
        }
    }

    public function getVisibleHumanCasesByCategoryForUser($mainCategory, $categoryId)
    {
        try {
            $locale = app()->getLocale();
            $titleField = "title_{$locale}";
            $descField = "description_{$locale}";

            $humanCases = \App\Models\HumanCase::with(['campaign' => function ($query) use ($mainCategory, $categoryId, $titleField, $descField) {
                $query->where('status', \App\Enums\CampaignStatus::Active)
                    ->where('category_id', $categoryId)
                    ->whereHas('category', function ($q) use ($mainCategory) {
                        $q->where('main_category', $mainCategory);
                    })
                    ->select(
                        'id',
                        'category_id',
                        "{$titleField} as title",
                        "{$descField} as description",
                        'image',
                        'goal_amount',
                        'collected_amount',
                        'start_date',
                        'end_date'
                    );
            }])
                ->get()
                ->filter(fn($humanCase) => $humanCase->campaign !== null)
                ->map(function ($humanCase) {
                    $campaign = $humanCase->campaign;
                    return [
                        'id' => $humanCase->id,
                        'category_id' => $campaign->category_id,
                        'title' => $campaign->title,
                        'description' => $campaign->description,
                        'image' => $campaign->image,
                        'goal_amount' => $campaign->goal_amount,
                        'collected_amount' => $campaign->collected_amount,
                    ];
                });

            return response()->json([
                'message' => $locale === 'ar' ? 'تم جلب الحالات حسب التصنيف بنجاح' : 'Human cases by category fetched successfully',
                'data' => $humanCases,
                'status' => 200
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $locale === 'ar' ? 'حدث خطأ أثناء جلب الحالات' : 'Error fetching human cases',
                'error' => $e->getMessage(),
                'status' => 500
            ]);
        }
    }

    public function getVisibleHumanCaseByIdForUser($mainCategory, $id)
    {
        try {
            $locale = app()->getLocale();
            $titleField = "title_{$locale}";
            $descField = "description_{$locale}";

            $humanCase = \App\Models\HumanCase::with(['campaign' => function ($query) use ($mainCategory, $titleField, $descField) {
                $query->where('status', \App\Enums\CampaignStatus::Active)
                    ->whereHas('category', function ($q) use ($mainCategory) {
                        $q->where('main_category', $mainCategory);
                    })
                    ->select(
                        'id',
                        'category_id',
                        "{$titleField} as title",
                        "{$descField} as description",
                        'image',
                        'goal_amount',
                        'collected_amount',
                        'start_date',
                        'end_date'
                    );
            }])->with('beneficiary')->find($id);

            if (!$humanCase || !$humanCase->campaign) {
                return response()->json([
                    'message' => $locale === 'ar' ? 'الحالة غير موجودة أو غير متاحة' : 'Human case not found or not visible',
                    'status' => 404
                ], 404);
            }

            $campaign = $humanCase->campaign;

            return response()->json([
                'message' => $locale === 'ar' ? 'تم جلب تفاصيل الحالة بنجاح' : 'Human case details fetched successfully',
                'data' => [
                    'id' => $humanCase->id,
                    'title' => $campaign->title,
                    'description' => $campaign->description,
                    'image' => $campaign->image,
                    'goal_amount' => $campaign->goal_amount,
                    'collected_amount' => $campaign->collected_amount,
                    'remaining_amount' => max(0, $campaign->goal_amount - $campaign->collected_amount),
                ],
                'status' => 200
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $locale === 'ar' ? 'حدث خطأ أثناء جلب الحالة' : 'Error fetching human case',
                'error' => $e->getMessage(),
                'status' => 500
            ]);
        }
    }

    public function getVisibleEmergencyHumanCaseByIdForUser($mainCategory, $id)
    {
        try {
            $locale = app()->getLocale();
            $titleField = "title_{$locale}";
            $descField = "description_{$locale}";

            $humanCase = \App\Models\HumanCase::with(['campaign' => function ($query) use ($mainCategory, $titleField, $descField) {
                $query->where('status', \App\Enums\CampaignStatus::Active)
                    ->whereHas('category', function ($q) use ($mainCategory) {
                        $q->where('main_category', $mainCategory);
                    })
                    ->select(
                        'id',
                        'category_id',
                        "{$titleField} as title",
                        "{$descField} as description",
                        'image',
                        'goal_amount',
                        'collected_amount'
                    );
            }])
                ->with('beneficiary')
                ->where('is_emergency', true)
                ->find($id);

            if (!$humanCase || !$humanCase->campaign) {
                return response()->json([
                    'message' => $locale === 'ar' ? 'الحالة الطارئة غير موجودة أو غير متاحة' : 'Emergency human case not found or not visible',
                    'status' => 404
                ], 404);
            }

            $campaign = $humanCase->campaign;

            return response()->json([
                'message' => $locale === 'ar' ? 'تم جلب تفاصيل الحالة الطارئة بنجاح' : 'Emergency human case details fetched successfully',
                'data' => [
                    'id' => $humanCase->id,
                    'category_id' => $campaign->category_id,
                    'title' => $campaign->title,
                    'description' => $campaign->description,
                    'image' => $campaign->image,
                    'goal_amount' => $campaign->goal_amount,
                    'collected_amount' => $campaign->collected_amount,
                    'remaining_amount' => max(0, $campaign->goal_amount - $campaign->collected_amount),
                ],
                'status' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $locale === 'ar' ? 'حدث خطأ أثناء جلب الحالة الطارئة' : 'Error fetching emergency human case',
                'error' => $e->getMessage(),
                'status' => 500
            ]);
        }
    }

    public function getAllVisibleEmergencyHumanCasesForUser($mainCategory)
    {
        $locale = app()->getLocale();
        $titleField = "title_{$locale}";
        $descField = "description_{$locale}";

        try {
            $humanCases = HumanCase::with(['campaign' => function ($query) use ($mainCategory, $titleField, $descField) {
                $query->where('status', CampaignStatus::Active)
                    ->whereHas('category', function ($q) use ($mainCategory) {
                        $q->where('main_category', $mainCategory);
                    })
                    ->select(
                        'id',
                        'category_id',
                        "{$titleField} as title",
                        "{$descField} as description",
                        'image',
                        'goal_amount',
                        'collected_amount',
                    );
            }])
                ->where('is_emergency', true)
                ->get()
                ->filter(fn($humanCase) => $humanCase->campaign !== null)
                ->map(function ($humanCase) {
                    $campaign = $humanCase->campaign;
                    return [
                        'id' => $humanCase->id,
                        'category_id' => $campaign->category_id,
                        'title' => $campaign->title,
                        'description' => $campaign->description,
                        'image' => $campaign->image,
                        'goal_amount' => $campaign->goal_amount,
                        'collected_amount' => $campaign->collected_amount,
                        'remaining_amount' => max(0, $campaign->goal_amount - $campaign->collected_amount),
                    ];
                });

            return response()->json([
                'message' => $locale === 'ar' ? 'تم جلب الحالات الإنسانية الطارئة بنجاح' : 'Emergency human cases fetched successfully',
                'data' => $humanCases,
                'status' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $locale === 'ar' ? 'حدث خطأ أثناء جلب الحالات الطارئة' : 'Error fetching emergency human cases',
                'error' => $e->getMessage(),
                'status' => 500
            ]);
        }
    }

    public function getVisibleArchivedHumanCases($mainCategory = 'HumanCase')
    {
        $locale = app()->getLocale();
        $titleField = "title_{$locale}";
        $descField = "description_{$locale}";

        try {
            $humanCases = HumanCase::with(['campaign' => function ($query) use ($mainCategory, $titleField, $descField) {
                $query->select(
                    'id',
                    'category_id',
                    'goal_amount',
                    'collected_amount',
                    'start_date',
                    'end_date',
                    'status',
                    'image',
                    "{$titleField} as title",
                    "{$descField} as description"
                )->whereHas('category', function ($q) use ($mainCategory) {
                    $q->where('main_category', $mainCategory);
                });
            }])
                ->whereHas('campaign', function ($query) use ($mainCategory) {
                    $query->where('status', \App\Enums\CampaignStatus::Archived)
                        ->whereHas('category', function ($q) use ($mainCategory) {
                            $q->where('main_category', $mainCategory);
                        });
                })
                ->latest()
                ->get();

            $data = $humanCases->map(function ($humanCase) use ($locale) {
                $campaign = $humanCase->campaign;

                return [
                    'id' => $humanCase->id,
                    'is_emergency' => $humanCase->is_emergency,
                    'title' => $campaign?->title,
                    'description' => $campaign?->description,
                    'category_id' => $campaign?->category_id,
                    'goal_amount' => $campaign?->goal_amount,
                    'collected_amount' => $campaign?->collected_amount,
                    'start_date' => $campaign?->start_date,
                    'end_date' => $campaign?->end_date,
                    'image' => $campaign?->image,
                ];
            });

            return response()->json([
                'message' => $locale === 'ar' ? 'تم جلب الحالات المؤرشفة بنجاح' : 'Archived human cases fetched successfully',
                'data' => $data,
                'status' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $locale === 'ar' ? 'حدث خطأ أثناء جلب الحالات المؤرشفة' : 'Error fetching archived human cases',
                'error' => $e->getMessage(),
                'status' => 500
            ]);
        }
    }


}
