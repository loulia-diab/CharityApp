<?php

namespace App\Http\Controllers\Donation_Type\Sponsorship;

use App\Enums\RecurrenceType;
use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Sponsorship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlanController extends Controller
{


    public function createPlanForSponsorship2(Request $request)
    {
        $request->validate([
            'sponsorship_id' => 'required|exists:sponsorships,id',
            'amount' => 'required|numeric',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $user = auth('user')->user();
        if (!$user) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }

        try {
            $plan = Plan::create([
                'user_id' => $user->id,
                'sponsorships_id' => $request->sponsorship_id,
                'amount' => $request->amount,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'is_activated' => false, // مبدئيًا
            ]);

            return response()->json([
                'message' => 'Plan created successfully for the sponsorship',
                'data' => $plan,
                'status' => 201,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating plan',
                'error' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }
    public function createPlanForSponsorship(Request $request, $sponsorshipId)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1'
        ]);

        $user = auth()->user();

        $sponsorship = Sponsorship::findOrFail($sponsorshipId);
        $campaign = $sponsorship->campaign;

        // احسب المبلغ المتبقي
        $collected = $campaign->collected_amount;
        $goal = $campaign->goal_amount;

        // احسب مجموع الخطط الموجودة (مفعلة أو لا)
        $planned = Plan::where('sponsorship_id', $sponsorshipId)
            ->whereIn('is_activated', [true, false])
            ->sum('amount');

        $remaining = $goal - $collected - $planned;

        if ($remaining <= 0) {
            return response()->json([
                'message' => 'هذه الكفالة تم تغطيتها بالكامل بالفعل.'
            ], 400);
        }

        if ($request->amount > $remaining) {
            return response()->json([
                'message' => 'المبلغ المدخل يتجاوز المبلغ المتبقي المطلوب للكفالة.',
                'remaining' => $remaining
            ], 400);
        }

        // أنشئ الخطة
        $plan = Plan::create([
            'user_id' => $user->id,
            'sponsorship_id' => $sponsorshipId,
            'amount' => $request->amount,
            'start_date' => now(),
            'end_date' => now()->addMonth(), // أو حسب المنطق المطلوب
            'is_activated' => false,
        ]);

        return response()->json([
            'message' => 'تم إنشاء خطة الكفالة بنجاح، بانتظار التفعيل.',
            'data' => $plan
        ]);
    }
    public function activatePlan($planId)
    {
        $user = auth()->user();

        $plan = Plan::where('id', $planId)
            ->where('user_id', $user->id)
            ->where('is_activated', false)
            ->first();

        if (!$plan) {
            return response()->json([
                'message' => 'الخطة غير موجودة أو مفعّلة مسبقًا.'
            ], 404);
        }

        $campaign = $plan->sponsorship->campaign;

        if ($user->wallet_balance < $plan->amount) {
            return response()->json([
                'message' => 'الرصيد غير كافٍ لتفعيل الخطة. يرجى شحن المحفظة أولاً.',
                'wallet_balance' => $user->wallet_balance,
                'required' => $plan->amount
            ], 400);
        }

        DB::beginTransaction();

        try {
            // خصم المبلغ من المحفظة
            $user->wallet_balance -= $plan->amount;
            $user->save();

            // تسجل العملية كمصروف (out)
            Transaction::create([
                'user_id' => $user->id,
                'campaign_id' => $campaign->id,
                'amount' => $plan->amount,
                'status' => 'success',
                'type' => 'out',
                'description' => 'First sponsorship payment (activation)',
            ]);

            // تسجيل العملية كإيراد للحملة (in)
            Transaction::create([
                'user_id' => $user->id,
                'campaign_id' => $campaign->id,
                'amount' => $plan->amount,
                'status' => 'success',
                'type' => 'in',
                'description' => 'Received first sponsorship payment (activation)',
            ]);

            // تحديث مبلغ الحملة المجمع
            $campaign->collected_amount += $plan->amount;
            $campaign->save();

            // تفعيل الخطة
            $plan->is_activated = true;
            $plan->start_date = now();
            $plan->end_date = now()->addMonth();
            $plan->save();


            DB::commit();

            return response()->json([
                'message' => 'تم تفعيل الخطة بنجاح، وتم خصم أول دفعة.',
                'data' => $plan
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'حدث خطأ أثناء تفعيل الخطة.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function deactivatePlan($id)
    {
        $plan = Plan::findOrFail($id);
        $plan->is_activated = false;
        $plan->save();

        return response()->json([
            'message' => 'Plan deactivated successfully',
            'data' => $plan,
            'status' => 200
        ]);
    }

    // كفالاتي
    public function getPlansForUser($id)
    {

    }

// تبرع دوري تفعيل والغاء
    public function createPlanForRecurringDonation(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'recurrence' => 'required|in:daily,weekly,monthly'
        ]);

        $user = auth()->user();

        $plan = Plan::create([
            'user_id' => $user->id,
            'amount' => $request->amount,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'recurrence' => $request->recurrence,
            'is_activated' => false,
            'sponsorship_id' => null, // لأنها عامة
        ]);

        return response()->json([
            'message' => 'تم إنشاء خطة التبرع العام بنجاح',
            'data' => $plan
        ]);
    }

    public function cancelPlanForRecurringDonation($id)
    {
        $user = auth()->user();
        $plan = Plan::where('id', $id)
            ->where('user_id', $user->id)
            ->whereNull('sponsorship_id') // لازم تكون عامة
            ->first();

        if (!$plan) {
            return response()->json([
                'message' => 'الخطة غير موجودة أو ليست خطة عامة.'
            ], 404);
        }

        $plan->is_activated = false;
        $plan->end_date = now(); // يعتبر تم إنهاؤها
        $plan->save();

        return response()->json([
            'message' => 'تم إلغاء خطة التبرع العام بنجاح.',
            'data' => $plan
        ]);
    }
    public function activatePlanForRecurringDonation($id)
    {
        $user = auth()->user();
        $locale = app()->getLocale();

        $plan = Plan::where('id', $id)
            ->where('user_id', $user->id)
            ->whereNull('sponsorship_id') // تأكد أنها تبرع عام
            ->first();

        if (!$plan) {
            return response()->json([
                'message' => $locale === 'ar' ? 'الخطة غير موجودة أو ليست خطة تبرع عام.' : 'Plan not found or not a general donation plan.',
            ], 404);
        }

        // نفعّل الخطة
        $plan->is_activated = true;
        $plan->save();

        // ترجمة نوع التكرار
        $recurrenceEnum = RecurrenceType::from($plan->recurrence);
        $recurrenceLabel = $recurrenceEnum->label($locale);

        switch ($plan->recurrence) {
            case RecurrenceType::Daily->value:
                $plan->end_date = now()->addDay();
                break;
            case RecurrenceType::Weekly->value:
                $plan->end_date = now()->addWeek();
                break;
            case RecurrenceType::Monthly->value:
            default:
                $plan->end_date = now()->addMonth();
        }

        return response()->json([
            'message' => $locale === 'ar' ? 'تم تفعيل خطة التبرع العام بنجاح' : 'General donation plan activated successfully',
            'data' => [
                'id' => $plan->id,
                'amount' => $plan->amount,
                'recurrence' => $plan->recurrence,
                'recurrence_label' => $recurrenceLabel,
                'start_date' => $plan->start_date,
                'end_date' => $plan->end_date,
                'is_activated' => $plan->is_activated,
            ]
        ]);
    }

}

