<?php

namespace App\Http\Controllers\beneficiary;

use App\Http\Controllers\Controller;
use App\Models\Beneficiary;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Models\Beneficiary_request;
use App\Models\AssistanceDetail;
use Illuminate\Http\Request;

class BeneficiaryRequestController extends Controller
{
    public function addBeneficiaryRequest(Request $request)
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

        $lang = App::getLocale();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'mother_name' => 'required|string|max:255',
            'gender' => 'required|string',
            'birth_date' => 'required|date',
            'marital_status' => 'required|string',
            'num_of_members' => 'required|integer|min:1',
            'study' => 'required|string|max:255',
            'has_job' => 'required|boolean',
            'job' => 'nullable|string|max:255',
            'housing_type' => 'required|string|max:255',
            'has_fixed_income' => 'required|boolean',
            'fixed_income' => 'nullable|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'main_category' => 'required|string|max:255',
            'sub_category' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'status'=>'nullable|string',
            'reason_of_rejection'=>'nullable|string',
            'details' => 'required|array',
            'details.*.field_name' => 'required|string|max:255',
            'details.*.field_value' => 'required|string|max:255',
        ]);

        DB::beginTransaction();

        try {

            $beneficiaryRequest = Beneficiary_request::create([
                'user_id' => $user ? $user->id : null,
                'admin_id' => $admin ? $admin->id : null,

                'name_' . $lang => $validated['name'],
                'father_name_' . $lang => $validated['father_name'] ?? null,
                'mother_name_' . $lang => $validated['mother_name'] ?? null,
                'gender_' . $lang => $validated['gender'],
                'birth_date' => $validated['birth_date'],
                'marital_status_' . $lang => $validated['marital_status'],
                'num_of_members' => $validated['num_of_members'],
                'study_' . $lang => $validated['study'] ?? null,
                'has_job' => $validated['has_job'],
                'job_' . $lang => $validated['job'] ?? null,
                'housing_type_' . $lang => $validated['housing_type'] ?? null,
                'has_fixed_income' => $validated['has_fixed_income'],
                'fixed_income' => $validated['fixed_income'] ?? null,
                'address_' . $lang => $validated['address'],
                'phone' => $validated['phone'],
                'main_category_' . $lang => $validated['main_category'],
                'sub_category_' . $lang => $validated['sub_category'],
                'notes_' . $lang => $validated['notes'] ?? null,
                'status_' . $lang => $validated['status'] ?? ($lang === 'ar' ? 'قيد الانتظار' : 'pending'),
                'reason_of_rejection_' . $lang => $validated['reason_of_rejection'] ?? null,

            ]);

            foreach ($validated['details'] as $detail) {
                $beneficiaryRequest->details()->create([
                    'field_name_' . $lang => $detail['field_name'],
                    'field_value_' . $lang => $detail['field_value'],
                ]);
            }

            DB::commit();

            return response()->json([
                'message' =>'Beneficiary request added successfully',
                'data' => $beneficiaryRequest
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' =>'An error occurred while creating a Beneficiary request',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getAllUserBeneficiaryRequests(Request $request)
    {
        $user = auth()->guard('api')->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $locale = app()->getLocale(); // 'ar' or 'en'
        $fallback = $locale === 'ar' ? 'en' : 'ar';

        $requests = Beneficiary_request::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($request) use ($locale, $fallback) {
                return [
                    'id' => $request->id,
                    'name' => $request->{'name_' . $locale} ?? $request->{'name_' . $fallback},
                    'status' => $request->{'status_' . $locale} ?? $request->{'status_' . $fallback},
                    'created_at' => $request->created_at->toDateTimeString(),
                ];
            });

        return response()->json(['data' => $requests]);
    }

    public function getBeneficiaryRequestDetails(Request $request, $id)
    {
        $user = auth()->guard('api')->user();
        $admin = auth()->guard('admin')->user();

        if (!$user && !$admin) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $locale = app()->getLocale(); // 'ar' or 'en'
        $fallback = $locale === 'ar' ? 'en' : 'ar';

        // جلب الطلب مع التفاصيل
        $requestData = Beneficiary_request::with('details')->findOrFail($id);

        // السماح فقط لصاحب الطلب أو الأدمن
        if ($user && $requestData->user_id !== $user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        // تحضير البيانات
        $data = [
            'id' => $requestData->id,
            'user_id' => $requestData->user_id,
            'admin_id' => $requestData->admin_id,
            'name' => $requestData->{'name_' . $locale} ?? $requestData->{'name_' . $fallback},
            'father_name' => $requestData->{'father_name_' . $locale} ?? $requestData->{'father_name_' . $fallback},
            'mother_name' => $requestData->{'mother_name_' . $locale} ?? $requestData->{'mother_name_' . $fallback},
            'gender' => $requestData->{'gender_' . $locale} ?? $requestData->{'gender_' . $fallback},
            'birth_date' => $requestData->birth_date,
            'marital_status' => $requestData->{'marital_status_' . $locale} ?? $requestData->{'marital_status_' . $fallback},
            'num_of_members' => $requestData->num_of_members,
            'study' => $requestData->{'study_' . $locale} ?? $requestData->{'study_' . $fallback},
            'has_job' => $requestData->has_job,
            'job' => $requestData->{'job_' . $locale} ?? $requestData->{'job_' . $fallback},
            'housing_type' => $requestData->{'housing_type_' . $locale} ?? $requestData->{'housing_type_' . $fallback},
            'has_fixed_income' => $requestData->has_fixed_income,
            'fixed_income' => $requestData->fixed_income,
            'address' => $requestData->{'address_' . $locale} ?? $requestData->{'address_' . $fallback},
            'phone' => $requestData->phone,
            'main_category' => $requestData->{'main_category_' . $locale} ?? $requestData->{'main_category_' . $fallback},
            'sub_category' => $requestData->{'sub_category_' . $locale} ?? $requestData->{'sub_category_' . $fallback},
            'notes' => $requestData->{'notes_' . $locale} ?? $requestData->{'notes_' . $fallback},
            'status' => $requestData->{'status_' . $locale} ?? $requestData->{'status_' . $fallback},
            'reason_of_rejection' => $requestData->{'reason_of_rejection_' . $locale} ?? $requestData->{'reason_of_rejection_' . $fallback},
            'created_at' => $requestData->created_at->toDateTimeString(),
            'details' => $requestData->details->map(function ($detail) use ($locale, $fallback) {
                return [
                    'id' => $detail->id,
                    'field_name' => $detail->{'field_name_' . $locale} ?? $detail->{'field_name_' . $fallback},
                    'field_value' => $detail->{'field_value_' . $locale} ?? $detail->{'field_value_' . $fallback},
                ];
            }),
        ];
        // إذا الأدمن فتح الطلب يتم تحديث حالة القراءة
        if ($admin && !$requestData->is_read_by_admin) {
            $requestData->update(['is_read_by_admin' => true]);
        }
        return response()->json(['data' => $data]);
    }

    public function getBeneficiaryRequestsByStatusForAdmin(Request $request)
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
        $requests = Beneficiary_request::where('status_' . $locale, $translatedStatus)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($request) use ($locale, $fallback) {
                return [
                    'id' => $request->id,
                    'name' => $request->{'name_' . $locale} ?? $request->{'name_' . $fallback},
                    'status' => $request->{'status_' . $locale} ?? $request->{'status_' . $fallback},
                    'created_at' => $request->created_at->toDateTimeString(),
                ];
            });

        return response()->json(['data' => $requests]);
    }

    public function getUnreadBeneficiaryRequests()
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $locale = app()->getLocale();
        $fallback = $locale === 'ar' ? 'en' : 'ar';

        $requests = Beneficiary_request::where('is_read_by_admin', false)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($request) use ($locale, $fallback) {
                return [
                    'id' => $request->id,
                    'name' => $request->{'name_' . $locale} ?? $request->{'name_' . $fallback},
                    'created_at' => $request->created_at->toDateTimeString(),
                ];
            });

        return response()->json(['data' => $requests]);
    }

    public function updateBeneficiaryRequestStatus(Request $request, $id)
    {
        $admin = auth()->guard('admin')->user();
        if (!$admin) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $requestData = Beneficiary_request::findOrFail($id);

        $statusMap = [
            'accepted' => ['ar' => 'مقبول', 'en' => 'accepted'],
            'rejected' => ['ar' => 'مرفوض', 'en' => 'rejected'],
        ];

        // التحقق من المدخلات
        $validated = $request->validate([
            'status' => 'required|in:accepted,rejected',

            // عند القبول
            'priority_ar' => 'required_if:status,accepted|in:عالية,متوسطة,منخفضة',
            'priority_en' => 'required_if:status,accepted|in:high,medium,low',

            // عند الرفض
            'reason_of_rejection_ar' => 'required_if:status,rejected',
            'reason_of_rejection_en' => 'required_if:status,rejected',
        ]);

        $status = $validated['status'];

        $updateData = [
            'status_ar' => $statusMap[$status]['ar'],
            'status_en' => $statusMap[$status]['en'],
        ];

        if ($status === 'rejected') {
            $updateData['reason_of_rejection_ar'] = $validated['reason_of_rejection_ar'];
            $updateData['reason_of_rejection_en'] = $validated['reason_of_rejection_en'];
        }

        // تحديث الطلب
        $requestData->update($updateData);

        $response = ['message' => 'Request status updated successfully'];

        if ($status === 'accepted') {
            // التحقق إن كان المستفيد موجود مسبقًا
            $existing = Beneficiary::where('beneficiary_request_id', $requestData->id)->first();

            if (!$existing) {
                $beneficiary= Beneficiary::create([
                    'user_id' => $requestData->user_id,
                    'beneficiary_request_id' => $requestData->id,
                    'priority_ar' => $validated['priority_ar'],
                    'priority_en' => $validated['priority_en'],
                    'is_sorted' => false, // يمكنك تعديله حسب الحاجة
                ]);
                $response['beneficiary_id'] = $beneficiary->id;
            }
        }

        return response()->json([$response]);
    }

    public function getBeneficiariesByPriority(Request $request)
    {
        $admin = auth()->guard('admin')->user();
        if (!$admin) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $locale = app()->getLocale();
        $fallback = $locale === 'ar' ? 'en' : 'ar';

        $validated = $request->validate([
            'priority' => 'required',
        ]);

        $priorityColumn = 'priority_' . $locale;

        $beneficiaries = Beneficiary::with('beneficiary_request')
            ->where($priorityColumn, $validated['priority'])
            ->where('is_sorted', false)
            ->get()
            ->map(function ($beneficiary) use ($locale, $fallback) {
                $req = $beneficiary->beneficiary_request;

                return [
                    'beneficiary_id' => $beneficiary->id,
                    'beneficiary_request_id' => $req->id,
                    'full_name' => $req->{'name_' . $locale} ?? $req->{'name_' . $fallback},
                    'main_category' => $req->{'main_category_' . $locale} ?? $req->{'main_category_' . $fallback},
                    'sub_category' => $req->{'sub_category_' . $locale} ?? $req->{'sub_category_' . $fallback},
                    'priority' => $beneficiary->{'priority_' . $locale} ?? $beneficiary->{'priority_' . $fallback},
                ];
            });

        return response()->json(['data' => $beneficiaries]);
    }
/*
    التابعين getBeneficiariesByPriority -getBeneficiariesByCategory مو موجودين عالبوست مان وهنن بيعتمدوا عادخال ال body مو query
  +في ريكويستين عالبوست مان مو مفعلين تبع الفلترة بالكاتيغوري والاولوية لطلبات الاستفادة
+في تابع ماله مكتوب ابدا يلي برجع المتطوعين حسب ايام التطوع وانماط التطوع
*/
    public function getBeneficiariesByCategory(Request $request)
    {
        $admin = auth()->guard('admin')->user();
        if (!$admin) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $locale = app()->getLocale();
        $fallback = $locale === 'ar' ? 'en' : 'ar';

        $validated = $request->validate([
            'main_category' => 'required|string',
            'sub_category' => 'nullable|string',
        ]);

        $mainCol = 'main_category_' . $locale;
        $subCol = 'sub_category_' . $locale;

        $query = \App\Models\Beneficiary::with('beneficiary_request')
            ->where('is_sorted', false)
            ->whereHas('beneficiary_request', function ($q) use ($mainCol, $subCol, $validated) {
                $q->where($mainCol, $validated['main_category']);

                if (!empty($validated['sub_category'])) {
                    $q->where($subCol, $validated['sub_category']);
                }

                $q->where('status_en', 'accepted');
            });

        $beneficiaries = $query->get()->map(function ($beneficiary) use ($locale, $fallback) {
            $req = $beneficiary->beneficiary_request;

            return [
                'beneficiary_id' => $beneficiary->id,
                'beneficiary_request_id' => $req->id,
                'full_name' => $req->{'name_' . $locale} ?? $req->{'name_' . $fallback},
                'main_category' => $req->{'main_category_' . $locale} ?? $req->{'main_category_' . $fallback},
                'sub_category' => $req->{'sub_category_' . $locale} ?? $req->{'sub_category_' . $fallback},
                'priority' => $beneficiary->{'priority_' . $locale} ?? $beneficiary->{'priority_' . $fallback},
            ];
        });

        return response()->json(['data' => $beneficiaries]);
    }



    /*
        public function updateBeneficiaryRequestStatus(Request $request, $id)
        {
            $admin = auth()->guard('admin')->user();
            if (!$admin) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
            $requestData = Beneficiary_request::findOrFail($id);

            $locale = app()->getLocale(); // ar أو en

            $statusMap = [
                'accepted' => ['ar' => 'مقبول', 'en' => 'accepted'],
                'rejected' => ['ar' => 'مرفوض', 'en' => 'rejected'],
            ];

            // تحقق من المدخلات
            $validated = $request->validate([
                'status' => 'required|in:accepted,rejected',

                // عند القبول
                'priority_ar' => ['required_if:status,accepted', 'in:عالية,متوسطة,منخفضة'],
                'priority_en' => ['required_if:status,accepted', 'in:high,medium,low'],

                // عند الرفض
                'reason_of_rejection_ar' => 'required_if:status,rejected',
                'reason_of_rejection_en' => 'required_if:status,rejected',
            ]);

            $status = $validated['status'];

            $updateData = [
                'status_ar' => $statusMap[$status]['ar'],
                'status_en' => $statusMap[$status]['en'],
            ];

            if ($status === 'accepted') {
                $updateData['priority_ar'] = $validated['priority_ar'];
                $updateData['priority_en'] = $validated['priority_en'];
            }

            if ($status === 'rejected') {
                $updateData['reason_of_rejection_ar'] = $validated['reason_of_rejection_ar'];
                $updateData['reason_of_rejection_en'] = $validated['reason_of_rejection_en'];
            }

            $requestData->update($updateData);

            return response()->json(['message' => 'Request status updated successfully']);
        }
    /*
        public function getBeneficiaryRequestsByPriority(Request $request)
        {
            $admin = auth()->guard('admin')->user();
            if (!$admin) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $locale = app()->getLocale(); // ar أو en
            $fallback = $locale === 'ar' ? 'en' : 'ar';

            $priority = $request->query('priority');

            // القيم المسموح بها
            $allowedPriorities = [
                'ar' => ['عالية', 'متوسطة', 'منخفضة'],
                'en' => ['high', 'medium', 'low'],
            ];

            if (!in_array($priority, $allowedPriorities[$locale])) {
                return response()->json(['message' => 'Invalid priority value'], 400);
            }

            // اسم العمود المناسب للغة
            $priorityColumn = 'priority_' . $locale;
            $statusColumn = 'status_' . $locale;

            $requests = Beneficiary_request::where($statusColumn, $locale === 'ar' ? 'مقبول' : 'accepted')
                ->where($priorityColumn, $priority)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($request) use ($locale, $fallback) {
                    return [
                        'id' => $request->id,
                        'name' => $request->{'name_' . $locale} ?? $request->{'name_' . $fallback},
                        'priority' => $request->{'priority_' . $locale} ?? $request->{'priority_' . $fallback},
                        'created_at' => $request->created_at->toDateTimeString(),
                    ];
                });

            return response()->json(['data' => $requests]);
        }

        public function getBeneficiaryRequestsByCategory(Request $request)
        {
            $admin = auth()->guard('admin')->user();
            if (!$admin) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $locale = app()->getLocale(); // ar أو en
            $fallback = $locale === 'ar' ? 'en' : 'ar';

            $mainCategory = $request->query('main_category');
            $subCategory = $request->query('sub_category');

            $statusColumn = 'status_' . $locale;
            $mainCategoryColumn = 'main_category_' . $locale;
            $subCategoryColumn = 'sub_category_' . $locale;

            $query = Beneficiary_request::where($statusColumn, $locale === 'ar' ? 'مقبول' : 'accepted');

            if ($mainCategory) {
                $query->where($mainCategoryColumn, $mainCategory);
            }

            if ($subCategory) {
                $query->where($subCategoryColumn, $subCategory);
            }

            $requests = $query->orderBy('created_at', 'desc')->get()->map(function ($request) use ($locale, $fallback) {
                return [
                    'id' => $request->id,
                    'name' => $request->{'name_' . $locale} ?? $request->{'name_' . $fallback},
                    'main_category' => $request->{'main_category_' . $locale} ?? $request->{'main_category_' . $fallback},
                    'sub_category' => $request->{'sub_category_' . $locale} ?? $request->{'sub_category_' . $fallback},
                    'created_at' => $request->created_at->toDateTimeString(),
                ];
            });

            return response()->json(['data' => $requests]);
        }
    */
}
