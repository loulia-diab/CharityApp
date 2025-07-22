<?php

namespace App\Jobs;

use App\Enums\RecurrenceType;
use App\Models\Plan;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;

class ProcessMonthlySponsorships implements ShouldQueue
{
    use Queueable;
    public function __construct()
    {

    }

    public function handle(): void
    {
        $plans = Plan::where('is_activated', true)
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->where('recurrence', RecurrenceType::Monthly->value)
            ->get();

        foreach ($plans as $plan) {
            DB::beginTransaction();
            try {
                $user = $plan->user;
                $campaign = $plan->sponsorship->campaign;

                if ($user->wallet_balance < $plan->amount) {
                    // ممكن هنا تبعت إشعار للمستخدم عن نقص الرصيد
                    DB::rollBack();
                    continue;
                }

                $user->wallet_balance -= $plan->amount;
                $user->save();

                Transaction::create([
                    'user_id' => $user->id,
                    'campaign_id' => $campaign->id,
                    'amount' => $plan->amount,
                    'status' => 'success',
                    'type' => 'out',
                    'description' => 'Monthly sponsorship payment',
                ]);

                $campaign->collected_amount += $plan->amount;
                $campaign->save();

                Transaction::create([
                    'user_id' => $user->id,
                    'campaign_id' => $campaign->id,
                    'amount' => $plan->amount,
                    'status' => 'success',
                    'type' => 'in',
                    'description' => 'Received sponsorship payment',
                ]);

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                // ممكن تسجل الخطأ أو ترسل إشعار بالخطأ
            }
        }
    }


}
