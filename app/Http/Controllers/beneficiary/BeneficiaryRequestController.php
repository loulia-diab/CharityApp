<?php

namespace App\Http\Controllers\beneficiary;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Models\Beneficiary_request;
use App\Models\AssistanceDetail;
use Illuminate\Http\Request;

class BeneficiaryRequestController extends Controller
{
    public function add(Request $request)
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
            'priority' => 'nullable|string',
            'status'=>'nullable|string',
            'reason_of_rejection'=>'nullable|string',
            'is_sorted'=>'nullable|boolean',
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
                'priority_' . $lang => $validated['priority'] ?? null,
                'is_sorted' => $validated['is_sorted'] ?? false,
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

}
