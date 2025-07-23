<?php

namespace App\Jobs;

use App\Models\Plan;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;

class ProcessRecurringDonations implements ShouldQueue
{
    use Queueable;
    public function __construct()
    {
        //
    }

    public function handle(): void
    {
        $now = Carbon::now();

        // بنجيب كل الخطط المفعلة يلي لازم تنسحب اليوم أو قبل (بسبب تأخير)
        $plans = Plan::where('is_activated', true)
            ->whereDate('end_date', '<=', $now)
            ->get();

        foreach ($plans as $plan) {
            DB::beginTransaction();

            try {
                $user = $plan->user;

                // تحقق من وجود رصيد كافي
                if ($user->wallet_balance < $plan->amount) {
                    // ممكن لاحقًا تبعتي إشعار للمستخدم بهالحالة
                    DB::commit();
                    continue;
                }

                // سحب من المحفظة
                $user->wallet_balance -= $plan->amount;
                $user->save();

                // سجل العملية المالية (سحب من المحفظة)
                Transaction::create([
                    'user_id' => $user->id,
                    'amount' => $plan->amount,
                    'type' => TransactionType::IN->value, // سحب من المحفظة
                    'description' => 'خصم تلقائي لخطة التبرع',
                ]);

                // سجل العملية المالية (دفع للجمعية أو الكفالة)
                Transaction::create([
                    'user_id' => $user->id,
                    'amount' => $plan->amount,
                    'type' => TransactionType::OUT->value, // دفع للجمعية
                    'description' => $plan->sponsorship_id
                        ? 'دفع تلقائي لخطة كفالة'
                        : 'دفع تلقائي لخطة تبرع عام',
                ]);

                // حدث end_date للخطة حسب نوع التكرار
                $nextDate = match ($plan->recurrence) {
                    'daily' => $now->copy()->addDay(),
                    'weekly' => $now->copy()->addWeek(),
                    'monthly' => $now->copy()->addMonth(),
                    default => $now->copy()->addMonth(),
                };

                $plan->end_date = $nextDate;
                $plan->start_date ??= $now; // لأول مرة فقط
                $plan->save();

                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                \Log::error("Recurring donation failed for plan ID {$plan->id}: " . $e->getMessage());
            }
        }
    }

}
