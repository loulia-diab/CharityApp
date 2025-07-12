<?php

namespace App\Http\Controllers\volunteer;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Day;
use App\Models\Volunteer;
use App\Models\Volunteer_request;
use App\Models\Volunteering_type;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class VolunteerRequestController extends Controller
{
    public function addVolunteerRequest(Request $request)
    {
        $user = null;
        $admin = null;
        if (auth()->guard('admin')->check()) {
            $admin = auth()->guard('admin')->user();
        } elseif (auth()->guard('api')->check()) {
            $user = auth()->guard('api')->user();
        } else {
            return response()->json(['message' => ' Unauthorized'], 401);
        }
        /*
        // تحديد من هو المصادق (user أو admin)
        $user = auth()->guard('user')->check() ? auth()->guard('user')->user() : null;
        $admin = auth()->guard('admin')->check() ? auth()->guard('admin')->user() : null;

        if (!$user && !$admin) {
            return response()->json(['message' => 'غير مصرح'], 401);
        }
*/
        $locale = App::getLocale(); // 'ar' or 'en'
        $nameColumn = $locale === 'ar' ? 'name_ar' : 'name_en';

        // تحقق من صحة البيانات المدخلة (validation)
        $validated = $request->validate([
            'full_name' => 'nullable|string',
            'gender' => 'nullable|string',
            'birth_date' => 'required|date',
            'address' => 'nullable|string',
            'study_qualification' => 'nullable|string',
            'job' => 'nullable|string',
            'preferred_times' => 'nullable|string',
            'has_previous_volunteer' => 'required|boolean',
            'previous_volunteer' => 'nullable|string',
            'phone' => 'required|string',
            'notes' => 'nullable|string',
            'status' => 'nullable|string',
            'reason_of_rejection' => 'nullable|string',
            'days' => 'nullable|array',
            'days.*' => 'string',
            'types' => 'nullable|array',
            'types.*' => 'string',
        ]);

        // تحويل أسماء الأيام إلى IDs
        $dayIds = [];
        if (!empty($validated['days'])) {
            $dayIds = Day::whereIn($nameColumn, $validated['days'])->pluck('id')->toArray();

            if (count($dayIds) !== count($validated['days'])) {
                return response()->json([
                    'message' => 'بعض أسماء الأيام غير موجودة',
                ], 422);
            }
        }

        // تحويل أسماء أنواع التطوع إلى IDs
        $typeIds = [];
        if (!empty($validated['types'])) {
            $typeIds = Volunteering_type::whereIn($nameColumn, $validated['types'])->pluck('id')->toArray();

            if (count($typeIds) !== count($validated['types'])) {
                return response()->json([
                    'message' => 'بعض أسماء أنواع التطوع غير موجودة',
                ], 422);
            }
        }

        DB::beginTransaction();

        try {
            $volunteerRequest = Volunteer_request::create([
                'user_id' => $user ? $user->id : null,
                'admin_id' => $admin ? $admin->id : null,

                // إدخال بيانات اللغة المختارة فقط
                'full_name_' . $locale => $validated['full_name'] ?? null,
                'gender_' . $locale => $validated['gender'] ?? null,
                'birth_date' => $validated['birth_date'],
                'address_' . $locale => $validated['address'] ?? null,
                'study_qualification_' . $locale => $validated['study_qualification'] ?? null,
                'job_' . $locale => $validated['job'] ?? null,
                'preferred_times_' . $locale => $validated['preferred_times'] ?? null,
                'has_previous_volunteer' => $validated['has_previous_volunteer'],
                'previous_volunteer_' . $locale => $validated['previous_volunteer'] ?? null,
                'phone' => $validated['phone'],
                'notes_' . $locale => $validated['notes'] ?? null,
                'status_' . $locale => $validated['status'] ?? ($locale === 'ar' ? 'قيد الانتظار' : 'pending'),
               // 'status_' . $locale => $validated['status'] ?? null,
                'reason_of_rejection_' . $locale => $validated['reason_of_rejection'] ?? null,
                // اللغة الثانية نتركها null تلقائياً
            ]);

            $volunteerRequest->days()->attach($dayIds);
            $volunteerRequest->types()->attach($typeIds);

            DB::commit();

            return response()->json([
                'message' => 'volunteer request added successfully',
                'data' => $volunteerRequest
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'An error occurred while creating a volunteer request',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function getMyVolunteerRequests(Request $request)
    {
        $user = auth()->guard('api')->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $locale = app()->getLocale(); // ar أو en
        $fallback = $locale === 'ar' ? 'en' : 'ar';

        $requests = Volunteer_request::where('user_id', $user->id)
            ->with([
                'days:id,name_' . $locale, 'days:id,name_' . $fallback,
                'types:id,name_' . $locale, 'types:id,name_' . $fallback
            ])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($request) use ($locale, $fallback) {
                return [
                    'id' => $request->id,
                    'user_id' => $request->user_id,
                    'admin_id' => $request->admin_id,
                    'full_name' => $request->{'full_name_' . $locale} ?? $request->{'full_name_' . $fallback},
                    'gender' => $request->{'gender_' . $locale} ?? $request->{'gender_' . $fallback},
                    'birth_date' => $request->birth_date,
                    'address' => $request->{'address_' . $locale} ?? $request->{'address_' . $fallback},
                    'study_qualification' => $request->{'study_qualification_' . $locale} ?? $request->{'study_qualification_' . $fallback},
                    'job' => $request->{'job_' . $locale} ?? $request->{'job_' . $fallback},
                    'preferred_times' => $request->{'preferred_times_' . $locale} ?? $request->{'preferred_times_' . $fallback},
                    'has_previous_volunteer' => $request->has_previous_volunteer,
                    'previous_volunteer' => $request->{'previous_volunteer_' . $locale} ?? $request->{'previous_volunteer_' . $fallback},
                    'phone' => $request->phone,
                    'notes' => $request->{'notes_' . $locale} ?? $request->{'notes_' . $fallback},
                    'status' => $request->{'status_' . $locale} ?? $request->{'status_' . $fallback},
                    //'reason_of_rejection' => $request->{'reason_of_rejection_' . $locale} ?? $request->{'reason_of_rejection_' . $fallback},
                    'days' => $request->days->map(function ($day) use ($locale, $fallback) {
                        return [
                            'id' => $day->id,
                            'name' => $day->{'name_' . $locale} ?? $day->{'name_' . $fallback},
                        ];
                    }),
                    'types' => $request->types->map(function ($type) use ($locale, $fallback) {
                        return [
                            'id' => $type->id,
                            'name' => $type->{'name_' . $locale} ?? $type->{'name_' . $fallback},
                        ];
                    }),
                    'created_at' => $request->created_at->toDateTimeString(),
                    //'updated_at' => $request->updated_at->toDateTimeString(),
                ];
            });

        return response()->json(['data' => $requests]);
    }


    public function filterVolunteerRequests(Request $request)
    {
        $admin = auth()->guard('admin')->user();
        if (!$admin || !$admin instanceof Admin) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $locale = app()->getLocale();
        $fallback = $locale === 'ar' ? 'en' : 'ar';

        $statusColumn = 'status_' . $locale;

        $requests = Volunteer_request::with([
            'days:id,name_' . $locale, 'days:id,name_' . $fallback,
            'types:id,name_' . $locale, 'types:id,name_' . $fallback,
            'user:id,name,email' // لو بدك تفاصيل عن المستخدم
        ])
            ->when($request->has('status'), function ($query) use ($request, $statusColumn) {
                $query->where($statusColumn, $request->status);
            })
            ->when($request->has('type_id'), function ($query) use ($request) {
                $query->whereHas('types', function ($q) use ($request) {
                    $q->where('volunteering_types.id', $request->type_id);
                });
            })
            ->when($request->has('day_id'), function ($query) use ($request) {
                $query->whereHas('days', function ($q) use ($request) {
                    $q->where('days.id', $request->day_id);
                });
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($request) use ($locale, $fallback) {
                return [
                    'id' => $request->id,
                    'full_name' => $request->{'full_name_' . $locale} ?? $request->{'full_name_' . $fallback},
                    'status' => $request->{'status_' . $locale} ?? $request->{'status_' . $fallback},
                    'user' => $request->user?->name,
                    'types' => $request->types->map(fn($type) => [
                        'id' => $type->id,
                        'name' => $type->{'name_' . $locale} ?? $type->{'name_' . $fallback}
                    ]),
                    'days' => $request->days->map(fn($day) => [
                        'id' => $day->id,
                        'name' => $day->{'name_' . $locale} ?? $day->{'name_' . $fallback}
                    ]),
                    'created_at' => $request->created_at->toDateTimeString(),
                ];
            });

        return response()->json(['data' => $requests]);
    }


    public function getVolunteerRequestDetails($id, Request $request)
    {
        $admin = auth()->guard('admin')->user();

        // التحقق من صلاحية المسؤول
        if (!$admin || !$admin instanceof Admin) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $locale = app()->getLocale();
        $fallback = $locale === 'ar' ? 'en' : 'ar';

        $volunteer = Volunteer_request::with([
            'user:id,name,email',
            'admin:id,name,email',
            'days:id,name_' . $locale, 'days:id,name_' . $fallback,
            'types:id,name_' . $locale, 'types:id,name_' . $fallback
        ])->findOrFail($id);

        return response()->json([
            'id' => $volunteer->id,
            'full_name' => $volunteer->{'full_name_' . $locale} ?? $volunteer->{'full_name_' . $fallback},
            'gender' => $volunteer->{'gender_' . $locale} ?? $volunteer->{'gender_' . $fallback},
            'birth_date' => $volunteer->birth_date,
            'address' => $volunteer->{'address_' . $locale} ?? $volunteer->{'address_' . $fallback},
            'study_qualification' => $volunteer->{'study_qualification_' . $locale} ?? $volunteer->{'study_qualification_' . $fallback},
            'job' => $volunteer->{'job_' . $locale} ?? $volunteer->{'job_' . $fallback},
            'preferred_times' => $volunteer->{'preferred_times_' . $locale} ?? $volunteer->{'preferred_times_' . $fallback},
            'has_previous_volunteer' => $volunteer->has_previous_volunteer,
            'previous_volunteer' => $volunteer->{'previous_volunteer_' . $locale} ?? $volunteer->{'previous_volunteer_' . $fallback},
            'phone' => $volunteer->phone,
            'notes' => $volunteer->{'notes_' . $locale} ?? $volunteer->{'notes_' . $fallback},
            'status' => $volunteer->{'status_' . $locale} ?? $volunteer->{'status_' . $fallback},
            'reason_of_rejection' => $volunteer->{'reason_of_rejection_' . $locale} ?? $volunteer->{'reason_of_rejection_' . $fallback},
            'user' => $volunteer->user,
            'admin' => $volunteer->admin,
            'days' => $volunteer->days->map(fn($day) => [
                'id' => $day->id,
                'name' => $day->{'name_' . $locale} ?? $day->{'name_' . $fallback}
            ]),
            'types' => $volunteer->types->map(fn($type) => [
                'id' => $type->id,
                'name' => $type->{'name_' . $locale} ?? $type->{'name_' . $fallback}
            ]),
            'created_at' => $volunteer->created_at->toDateTimeString(),
            'updated_at' => $volunteer->updated_at->toDateTimeString()
        ]);
    }


    public function updateVolunteerRequestStatus(Request $request, $id)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'status' => 'required|string|in:مقبول,مرفوض,معلق,accepted,rejected,pending',
            'reason_of_rejection' => 'nullable|string',
            'lang' => 'required|in:ar,en', // اللغة التي تم بها الإدخال
        ]);

        $statusInput = $validated['status'];
        $lang = $validated['lang'];
        $otherLang = $lang === 'ar' ? 'en' : 'ar';

        $statusMap = [
            'مقبول' => 'accepted',
            'مرفوض' => 'rejected',
            'معلق'  => 'pending',
            'accepted' => 'مقبول',
            'rejected' => 'مرفوض',
            'pending'  => 'معلق',
        ];

        // تحديد الحالتين بكلتا اللغتين
        $status_ar = $lang === 'ar' ? $statusInput : $statusMap[$statusInput];
        $status_en = $lang === 'en' ? $statusInput : $statusMap[$statusInput];

        $volunteerRequest = Volunteer_request::findOrFail($id);

        $volunteerRequest->status_ar = $status_ar;
        $volunteerRequest->status_en = $status_en;
        $volunteerRequest->admin_id = $admin->id;

        // سبب الرفض يتم تخزينه في لغة واحدة فقط
        if ($status_ar === 'مرفوض') {
            $volunteerRequest->{'reason_of_rejection_' . $lang} = $validated['reason_of_rejection'] ?? '';
            $volunteerRequest->{'reason_of_rejection_' . $otherLang} = null;
        } else {
            $volunteerRequest->reason_of_rejection_ar = null;
            $volunteerRequest->reason_of_rejection_en = null;
        }

        $volunteerRequest->save();

        // إضافة إلى جدول المتطوعين في حال القبول
        if ($status_ar === 'مقبول' && !$volunteerRequest->volunteer) {
            Volunteer::create([
                'user_id' => $volunteerRequest->user_id,
                'volunteer_request_id' => $volunteerRequest->id,
            ]);
        }

        return response()->json(['message' => 'Volunteer request status updated successfully.']);
    }

}
