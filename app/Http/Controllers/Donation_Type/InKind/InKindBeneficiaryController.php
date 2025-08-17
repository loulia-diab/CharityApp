<?php

namespace App\Http\Controllers\Donation_Type\InKind;

use App\Http\Controllers\Controller;
use App\Models\Beneficiary;
use App\Models\InKind;
use Illuminate\Http\Request;

class InKindBeneficiaryController extends Controller
{
    // اضافة مستفيدين
    public function addBeneficiariesToInKind(Request $request, $inKindId) {
        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json([
                'message' => 'Unauthorized - Admin access only',
            ], 401);
        }

        $request->validate([
            'beneficiary_ids' => 'required|array|min:1',
            'beneficiary_ids.*' => 'exists:beneficiaries,id',
        ]);

        $inKind = InKind::find($inKindId);

        if (!$inKind) {
            return response()->json([
                'message' => 'In-kind donation not found',
            ], 404);
        }

        $inKind->beneficiaries()->syncWithoutDetaching($request->beneficiary_ids);

// تحديث is_stored لكل المستفيدين المضافين
        Beneficiary::whereIn('id', $request->beneficiary_ids)
            ->update(['is_sorted' => true]);

// جلب المستفيدين بعد التحديث
        $inKind->load('beneficiaries');

        return response()->json([
            'message' => 'Beneficiaries added to in-kind donation successfully',
            'data' => $inKind->beneficiaries,
        ]);

    }

    // جلب مستفيدين تبرع عيني لحالو
    public function getInKindBeneficiaries($inKindId) {
        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json([
                'message' => 'Unauthorized - Admin access only',
            ], 401);
        }

        $inKind = InKind::with('beneficiaries')->find($inKindId);

        if (!$inKind) {
            return response()->json([
                'message' => 'In-kind donation not found',
            ], 404);
        }

        return response()->json([
            'message' => 'Beneficiaries retrieved successfully',
            'data' => $inKind->beneficiaries->map(function ($beneficiary) {
                return [
                    'id' => $beneficiary->id,
                    'name' => $beneficiary->beneficiary_request->name,
                    // أضف الحقول التي تحتاجها مثل العمر، الجنس، وغيرها
                    // 'age' => $beneficiary->age,
                    // 'gender' => $beneficiary->gender,
                ];
            }),
        ]);
    }


    // غير مستخدمة
    // جلب مستفيدين تبرعات عينية حسب التصنيف المعين
    public function getBeneficiariesByCategory($categoryId) {
        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $locale = app()->getLocale();

        // جلب التبرعات العينية اللي حملاتها مرتبطة بالتصنيف المعطى
        $inKinds = InKind::whereHas('campaign.categories', function($q) use ($categoryId) {
            $q->where('id', $categoryId);
        })->with(['beneficiaries'])->get();

        // جمع كل المستفيدين من التبرعات العينية
        $beneficiaries = $inKinds->flatMap(function($inKind) {
            return $inKind->beneficiaries;
        })->unique('id')->values();

        // ترجع بيانات المستفيدين مع التنسيق
        $result = $beneficiaries->map(function($beneficiary) use ($locale) {
            return [
                'id' => $beneficiary->id,
                'name' => $beneficiary->name,  // إذا في أسماء بلغتين عدل هنا
                // ممكن تضيف حقول أخرى حسب الحاجة
            ];
        });

        return response()->json([
            'message' => 'Beneficiaries filtered by category retrieved successfully',
            'data' => $result,
        ]);
    }


}
