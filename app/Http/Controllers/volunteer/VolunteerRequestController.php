<?php

namespace App\Http\Controllers\volunteer;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Day;
use App\Models\User;
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

    public function getAllUserVolunteerRequests(Request $request)
    {
        $user = auth()->guard('api')->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $locale = app()->getLocale();
        $fallback = $locale === 'ar' ? 'en' : 'ar';

        $requests = Volunteer_request::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($request) use ($locale, $fallback) {
                return [
                    'id' => $request->id,
                    'name' => $request->{'full_name_' . $locale} ?? $request->{'full_name_' . $fallback},
                    'status' => $request->{'status_' . $locale} ?? $request->{'status_' . $fallback},
                    'created_at' => $request->created_at->toDateTimeString(),
                ];
            });

        return response()->json(['data' => $requests]);
    }

    public function getVolunteerRequestDetails(Request $request, $id)
    {
        $user = auth()->guard('api')->user();
        $admin = auth()->guard('admin')->user();

        if (!$user && !$admin) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $locale = app()->getLocale();
        $fallback = $locale === 'ar' ? 'en' : 'ar';

        // إحضار الطلب (إذا لم يكن موجودًا يرجع 404)
        $requestData = Volunteer_request::with(['days', 'types'])->findOrFail($id);

        // إذا المستخدم هو يوزر، تأكد أن الطلب له
        if ($user && $requestData->user_id !== $user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        // إعداد البيانات للعرض
        $data = [
            'id' => $requestData->id,
            'user_id' => $requestData->user_id,
            'admin_id' => $requestData->admin_id,
            'name' => $requestData->{'full_name_' . $locale} ?? $requestData->{'full_name_' . $fallback},
            'gender' => $requestData->{'gender_' . $locale} ?? $requestData->{'gender_' . $fallback},
            'birth_date' => $requestData->birth_date,
            'address' => $requestData->{'address_' . $locale} ?? $requestData->{'address_' . $fallback},
            'study_qualification' => $requestData->{'study_qualification_' . $locale} ?? $requestData->{'study_qualification_' . $fallback},
            'job' => $requestData->{'job_' . $locale} ?? $requestData->{'job_' . $fallback},
            'preferred_times' => $requestData->{'preferred_times_' . $locale} ?? $requestData->{'preferred_times_' . $fallback},
            'has_previous_volunteer' => $requestData->has_previous_volunteer,
            'previous_volunteer' => $requestData->{'previous_volunteer_' . $locale} ?? $requestData->{'previous_volunteer_' . $fallback},
            'phone' => $requestData->phone,
            'notes' => $requestData->{'notes_' . $locale} ?? $requestData->{'notes_' . $fallback},
            'status' => $requestData->{'status_' . $locale} ?? $requestData->{'status_' . $fallback},
            'reason_of_rejection' => $requestData->{'reason_of_rejection_' . $locale} ?? $requestData->{'reason_of_rejection_' . $fallback},
            'days' => $requestData->days->map(function ($day) use ($locale, $fallback) {
                return [
                    'id' => $day->id,
                    'name' => $day->{'name_' . $locale} ?? $day->{'name_' . $fallback},
                ];
            }),
            'types' => $requestData->types->map(function ($type) use ($locale, $fallback) {
                return [
                    'id' => $type->id,
                    'name' => $type->{'name_' . $locale} ?? $type->{'name_' . $fallback},
                ];
            }),
            'created_at' => $requestData->created_at->toDateTimeString(),
        ];

        if (auth()->guard('admin')->check()) {
            $requestData->update(['is_read_by_admin' => true]);
        }

        return response()->json(['data' => $data]);
    }

    public function getVolunteerRequestsByStatusForAdmin(Request $request)
    {
        $admin = auth()->guard('admin')->user();
        if (!$admin) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $status = $request->query('status'); // قيم: 'accepted', 'rejected', 'pending'
        $locale = app()->getLocale();
        $fallback = $locale === 'ar' ? 'en' : 'ar';

        // تحديد الترجمة المقابلة للحالة
        $statusMap = [
            'accepted' => ['ar' => 'مقبول', 'en' => 'accepted'],
            'rejected' => ['ar' => 'مرفوض', 'en' => 'rejected'],
            'pending' => ['ar' => 'قيد الانتظار', 'en' => 'pending'],
        ];

        if (!array_key_exists($status, $statusMap)) {
            return response()->json(['message' => 'Invalid status filter'], 400);
        }

        $translatedStatus = $statusMap[$status][$locale];

        // جلب الطلبات التي تطابق الحالة
        $requests = Volunteer_request::where('status_' . $locale, $translatedStatus)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($request) use ($locale, $fallback) {
                return [
                    'id' => $request->id,
                    'full_name' => $request->{'full_name_' . $locale} ?? $request->{'full_name_' . $fallback},
                    'status' => $request->{'status_' . $locale} ?? $request->{'status_' . $fallback},
                    'created_at' => $request->created_at->toDateTimeString(),
                ];
            });

        return response()->json(['data' => $requests]);
    }

    public function getUnreadVolunteerRequests()
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $locale = app()->getLocale();
        $fallback = $locale === 'ar' ? 'en' : 'ar';

        $requests = Volunteer_request::where('is_read_by_admin', false)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($request) use ($locale, $fallback) {
                return [
                    'id' => $request->id,
                    'full_name' => $request->{'full_name_' . $locale} ?? $request->{'full_name_' . $fallback},
                    'created_at' => $request->created_at->toDateTimeString(),
                ];
            });

        return response()->json(['data' => $requests]);
    }

    public function updateVolunteerRequestStatus(Request $request, $id)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'status' => 'required|in:accepted,rejected',
            'reason_ar' => 'required_if:status,rejected|string|nullable',
            'reason_en' => 'required_if:status,rejected|string|nullable',
        ]);

        $volunteerRequest = Volunteer_request::findOrFail($id);

        // ترجمة الحالة
        $statusMap = [
            'accepted' => ['ar' => 'مقبول', 'en' => 'accepted'],
            'rejected' => ['ar' => 'مرفوض', 'en' => 'rejected'],
        ];

        $updateData = [
            'status_ar' => $statusMap[$validated['status']]['ar'],
            'status_en' => $statusMap[$validated['status']]['en'],
        ];

        // في حال الرفض، خزّن سببي الرفض
        if ($validated['status'] === 'rejected') {
            $updateData['reason_of_rejection_ar'] = $validated['reason_ar'];
            $updateData['reason_of_rejection_en'] = $validated['reason_en'];
        }

        $volunteerRequest->update($updateData);

        $response = ['message' => 'Status updated successfully'];

        // في حال القبول، أضف المتطوع
        if ($validated['status'] === 'accepted') {
            $volunteer =Volunteer::firstOrCreate(
                ['volunteer_request_id' => $volunteerRequest->id,],
                ['user_id' => $volunteerRequest->user_id,
            ]);
            $response['volunteer_id'] = $volunteer->id;
        }
        // إرسال إشعار للمستخدم
        $user = User::find($volunteerRequest->user_id); // صحّحت $requestData إلى $volunteerRequest
        if ($user) {
            $title = [];
            $body = [];


            if ($validated['status'] === 'accepted') {
                $title = [
                    'en' => 'Your request has been accepted',
                    'ar' => 'تم قبول طلبك',
                ];
                $body = [
                    'en' => 'Congratulations! Your request is now approved.',
                    'ar' => 'تهانينا! تم قبول طلبك وأصبحت متطوعًا.',
                ];
            } else {
                $title = [
                    'en' => 'Your request has been rejected',
                    'ar' => 'تم رفض طلبك',
                ];
                $body = [
                    'en' => 'Unfortunately, your request has been rejected. Reason: ' . $validated['reason_en'],
                    'ar' => 'نعتذر، تم رفض طلبك. السبب: ' . $validated['reason_ar'],
                ];
            }

            $notificationService = app()->make(\App\Services\NotificationService::class);
            $notificationService->sendFcmNotification(new Request([
                'user_id' => $user->id,
                'title_en' => $title['en'],
                'title_ar' => $title['ar'],
                'body_en' => $body['en'],
                'body_ar' => $body['ar'],
            ]));
        }
        /*
        return response()->json(['message' => 'Status updated successfully',
            'Volunteer_id'=> $Volunteer->id
            ]);
*/
       return response()->json([$response]);

    }
}
