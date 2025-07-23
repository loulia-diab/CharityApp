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

// إنشاء خطة كفالة بدون تفعيل
    public function createPlan($sponsorshipId)
    {
        $user = auth()->user();

        $plan = Plan::create([
            'user_id' => $user->id,
            'sponsorship_id' => $sponsorshipId,
            'amount' => Sponsorship::findOrFail($sponsorshipId)->amount,
            'recurrence' => 'monthly', // الكفالة دائمًا شهريًا
            'is_activated' => false,
        ]);

        return response()->json([
            'message' => 'تم إنشاء خطة كفالة بنجاح',
            'plan_id' => $plan->id,
        ]);
    }
    // تفعيل خطة الكفالة
    public function activatePlan(Request $request, $planId)
    {
        $user = auth()->user();
        $locale = app()->getLocale();

        $plan = Plan::where('id', $planId)
            ->where('user_id', $user->id)
            ->whereNotNull('sponsorship_id') // تأكد أنها كفالة
            ->where('is_activated', false)
            ->first();

        if (!$plan) {
            return response()->json([
                'message' => $locale === 'ar' ? 'الخطة غير موجودة أو مفعلة مسبقًا.' : 'Plan not found or already activated.'
            ], 404);
        }

        DB::beginTransaction();
        try {
            // المعاملة: سحب من المحفظة
            Transaction::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'type' => 'in',
                'amount' => $plan->amount,
                'description' => $locale === 'ar' ? 'سحب من المحفظة لخطة كفالة' : 'Wallet withdrawal for sponsorship plan'
            ]);

            // المعاملة: دفع للكفالة
            Transaction::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'type' => 'out',
                'amount' => $plan->amount,
                'description' => $locale === 'ar' ? 'دفع للكفالة الشهرية' : 'Monthly sponsorship payment'
            ]);

            $now = now();

            $plan->update([
                'is_activated' => true,
                'start_date' => $now,
                'end_date' => $now->copy()->addMonth(),
            ]);

            DB::commit();

            return response()->json([
                'message' => $locale === 'ar' ? 'تم تفعيل خطة الكفالة بنجاح' : 'Sponsorship plan activated successfully',
                'data' => $plan
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $locale === 'ar' ? 'حدث خطأ أثناء التفعيل' : 'Error during activation',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    // إيقاف خطة الكفالة
    public function deactivatePlan($planId)
    {
        $user = auth()->user();
        $locale = app()->getLocale();

        $plan = Plan::where('id', $planId)
            ->where('user_id', $user->id)
            ->whereNotNull('sponsorship_id')
            ->where('is_activated', true)
            ->first();

        if (!$plan) {
            return response()->json([
                'message' => $locale === 'ar' ? 'الخطة غير موجودة أو غير مفعلة' : 'Plan not found or not active',
            ], 404);
        }

        $plan->is_activated = false;
        $plan->end_date = now(); // تسجيل تاريخ الإيقاف
        $plan->save();

        return response()->json([
            'message' => $locale === 'ar' ? 'تم إيقاف الخطة' : 'Plan deactivated successfully',
        ]);
    }
    // إعادة تفعيل خطة كفالة
    public function reactivatePlan($planId)
    {
        $user = auth()->user();
        $locale = app()->getLocale();

        $plan = Plan::where('id', $planId)
            ->where('user_id', $user->id)
            ->whereNotNull('sponsorship_id')
            ->where('is_activated', false)
            ->first();

        if (!$plan) {
            return response()->json([
                'message' => $locale === 'ar' ? 'الخطة غير موجودة أو مفعلة بالفعل' : 'Plan not found or already active',
            ], 404);
        }

        $now = now();

        $plan->update([
            'is_activated' => true,
            'start_date' => $now,
            'end_date' => $now->copy()->addMonth(),
        ]);

        return response()->json([
            'message' => $locale === 'ar' ? 'تم إعادة تفعيل الخطة' : 'Plan reactivated successfully',
        ]);
    }
    public function activateRecurring(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'recurrence' => 'required|in:daily,weekly,monthly',
        ]);

        $user = auth()->user();
        $locale = app()->getLocale();

        DB::beginTransaction();

        try {
            // إنشاء الخطة
            $plan = new Plan();
            $plan->user_id = $user->id;
            $plan->amount = $request->amount;
            $plan->recurrence = $request->recurrence;
            $plan->sponsorship_id = null; // لأنها تبرع عام
            $plan->start_date = now();
            $plan->is_activated = true;

            // تحديد end_date مبدئي حسب نوع التكرار
            switch ($request->recurrence) {
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

            $plan->save();

            // إنشاء المعاملة من wallet
            Transaction::create([
                'user_id' => $user->id,
                'amount' => $request->amount,
                'type' => 'in',
                'description' => $locale === 'ar' ? 'سحب من المحفظة للتبرع العام' : 'Wallet withdrawal for recurring donation',
            ]);

            // إنشاء المعاملة لصالح الجهة (تبرع)
            Transaction::create([
                'user_id' => $user->id,
                'amount' => $request->amount,
                'type' => 'out',
                'description' => $locale === 'ar' ? 'تبرع عام متكرر' : 'Recurring general donation',
            ]);

            DB::commit();

            $recurrenceLabel = RecurrenceType::from($plan->recurrence)->label($locale);

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
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $locale === 'ar' ? 'فشل في تفعيل الخطة' : 'Failed to activate the plan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function deactivateRecurring($planId)
    {
        $user = auth()->user();
        $locale = app()->getLocale();

        $plan = Plan::where('id', $planId)
            ->where('user_id', $user->id)
            ->whereNull('sponsorship_id')
            ->first();

        if (!$plan) {
            return response()->json([
                'message' => $locale === 'ar' ? 'الخطة غير موجودة أو ليست تبرع عام' : 'Plan not found or not a general donation',
            ], 404);
        }

        $plan->is_activated = false;
        $plan->end_date = now(); // وقت الإيقاف
        $plan->save();

        return response()->json([
            'message' => $locale === 'ar' ? 'تم إيقاف خطة التبرع العام' : 'General donation plan deactivated',
        ]);
    }
    public function reactivateRecurring($planId)
    {
        $user = auth()->user();
        $locale = app()->getLocale();

        $plan = Plan::where('id', $planId)
            ->where('user_id', $user->id)
            ->whereNull('sponsorship_id')
            ->first();

        if (!$plan) {
            return response()->json([
                'message' => $locale === 'ar' ? 'الخطة غير موجودة أو ليست تبرع عام' : 'Plan not found or not a general donation plan',
            ], 404);
        }

        $plan->is_activated = true;
        $plan->start_date = now();

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

        $plan->save();

        return response()->json([
            'message' => $locale === 'ar' ? 'تم إعادة تفعيل خطة التبرع العام' : 'General donation plan reactivated',
            'data' => [
                'id' => $plan->id,
                'amount' => $plan->amount,
                'recurrence' => $plan->recurrence,
                'start_date' => $plan->start_date,
                'end_date' => $plan->end_date,
                'is_activated' => $plan->is_activated,
            ]
        ]);
    }

}

