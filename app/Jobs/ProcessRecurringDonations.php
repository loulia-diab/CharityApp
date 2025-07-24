<?php

namespace App\Jobs;

use App\Models\Plan;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;

class ProcessRecurringDonations implements ShouldQueue
{
    use Queueable;
    public function __construct()
    {
    }

    public function handle(): void
    {
        $now = Carbon::now();

        // جلب الخطط المفعلة التي انتهت مواعيدها أو اليوم (لتفادي التأخير)
        $plans = Plan::where('is_activated', true)
            ->whereDate('end_date', '<=', $now->startOfDay())
            ->with('user', 'sponsorship.campaign')
            ->get();

        foreach ($plans as $plan) {
            DB::beginTransaction();

            try {
                $user = $plan->user;
                $isSponsorship = !is_null($plan->sponsorship_id);
                $campaign = $isSponsorship ? $plan->sponsorship->campaign : null;

                // إذا الكفالة مكتملة ما نكمل (توقف التكرار)
                if ($isSponsorship && $campaign && $campaign->status === 'completed') {
                    DB::commit();
                    continue;
                }

                // تحقق من رصيد المستخدم
                if ($user->balance < $plan->amount) {
                    DB::commit(); // نتخطى الخطة بدون خطأ
                    continue;
                }

                // خصم الرصيد من المحفظة
                $user->balance -= $plan->amount;
                $user->save();

                // إنشاء معاملة واحدة فقط (direction = in)
                $transactionData = [
                    'user_id' => $user->id,
                    'amount' => $plan->amount,
                    'type' => 'donation',
                    'direction' => 'in',
                ];

                if ($isSponsorship) {
                    $transactionData['campaign_id'] = $campaign->id ?? null;
                } else {
                    $generalDonationBoxId = 1;
                    $transactionData['box_id'] = $generalDonationBoxId;
                }

                $transaction = Transaction::create($transactionData);

                // تحديث تاريخ بداية ونهاية الخطة
                $nextDate = match ($plan->recurrence) {
                    'daily' => $now->copy()->addDay(),
                    'weekly' => $now->copy()->addWeek(),
                    'monthly' => $now->copy()->addMonth(),
                    default => $now->copy()->addMonth(),
                };

                $plan->start_date ??= $now;
                $plan->end_date = $nextDate;
                $plan->save();

                // إذا هي كفالة، نحدث المبلغ المجمّع والحالة
                if ($isSponsorship && $campaign) {
                    $totalCollected = Plan::where('sponsorship_id', $plan->sponsorship_id)
                        ->where('is_activated', true)
                        ->sum('amount');

                    $campaign->collected_amount = $totalCollected;

                    if ($totalCollected >= $campaign->goal_amount) {
                        $campaign->status = 'completed';
                        $campaign->completed_at = now();
                    }

                    $campaign->save();
                }

                DB::commit();

                // ممكن تستخدم روابط الـ PDF هنا لو بدك
                // $pdfUrl = $transaction->pdf_url;

            } catch (\Throwable $e) {
                DB::rollBack();
                \Log::error("Recurring donation failed for plan ID {$plan->id}: " . $e->getMessage());
            }
        }
    }


}
