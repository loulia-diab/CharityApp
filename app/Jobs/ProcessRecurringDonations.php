<?php

namespace App\Jobs;

use App\Enums\RecurrenceType;
use App\Models\Box;
use App\Models\Plan;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
/*
class ProcessRecurringDonations implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
    }

    public function handle(): void
    {
        $now = Carbon::now();

        // جلب الخطط المفعلة التي انتهى تاريخها أو يساوي اليوم
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

                // إذا كانت كفالة مكتملة، نتخطى المعالجة
                if ($isSponsorship && $campaign && $campaign->status === 'completed') {
                    DB::commit();
                    continue;
                }

                // تحقق من رصيد المستخدم، إذا غير كافي نتخطى
                if ($user->balance < $plan->amount) {
                    DB::commit();
                    continue;
                }

                // خصم الرصيد من المستخدم
                $user->balance -= $plan->amount;
                $user->save();

                // تجهيز بيانات المعاملة
                $transactionData = [
                    'user_id' => $user->id,
                    'amount' => $plan->amount,
                    'type' => 'donation',
                    'direction' => 'in',
                ];

                if ($isSponsorship) {
                    $transactionData['campaign_id'] = $campaign->id ?? null;
                } else {
                    // إيجاد صندوق التبرعات العامة
                    $generalDonationBox = Box::where('name_en', 'Periodic donation')->first();
                    $transactionData['box_id'] = $generalDonationBox?->id; // إذا ما وجدنا الصندوق يمكن تكون null
                }

                // إنشاء المعاملة
                Transaction::create($transactionData);

                // تحديث تواريخ البداية والنهاية للخطة
                // البداية الجديدة = نهاية الخطة السابقة + 1 يوم (بداية فترة جديدة)
                $plan->start_date = $plan->end_date->copy()->addDay();

                // تحديد نهاية الخطة الجديدة حسب نوع التكرار بدقة
                $plan->end_date = match ($plan->recurrence) {
                    RecurrenceType::Daily => $plan->start_date->copy()->addDay(),
                    RecurrenceType::Weekly => $plan->start_date->copy()->addWeek(),
                    RecurrenceType::Monthly => $plan->start_date->copy()->addMonth(),
                    default => $plan->start_date->copy()->addMonth(),
                };

                $plan->save();

                // تحديث حالة الكفالة والمبلغ المجمّع إذا كان متعلق بكفالة
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

            } catch (\Throwable $e) {
                DB::rollBack();
                \Log::error("Recurring donation failed for plan ID {$plan->id}: " . $e->getMessage());
            }
        }
    }
}
*/
class ProcessRecurringDonations implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
    }

    public function handle(): void
    {
        $now = Carbon::now();

        $plans = Plan::where('is_activated', true)
            ->whereDate('end_date', '<=', $now->startOfDay())
            ->with('user', 'sponsorship.campaign')
            ->get();

        foreach ($plans as $plan) {
            DB::beginTransaction();

            try {
                // تحقق تواريخ الخطة قبل المعالجة
                $this->validatePlanDates($plan);

                $user = $plan->user;
                $isSponsorship = !is_null($plan->sponsorship_id);
                $campaign = $isSponsorship ? $plan->sponsorship->campaign : null;

                if ($isSponsorship && $campaign && $campaign->status === 'completed') {
                    DB::commit();
                    continue;
                }

                if ($user->balance < $plan->amount) {
                    DB::commit();
                    continue;
                }

                $user->balance -= $plan->amount;
                $user->save();

                $transactionData = [
                    'user_id' => $user->id,
                    'amount' => $plan->amount,
                    'type' => 'donation',
                    'direction' => 'in',
                ];

                if ($isSponsorship) {
                    $transactionData['campaign_id'] = $campaign->id ?? null;
                } else {
                    $generalDonationBox = Box::where('name_en', 'Periodic donation')->first();
                    $transactionData['box_id'] = $generalDonationBox?->id;
                }

                Transaction::create($transactionData);

                $plan->start_date = $plan->end_date->copy()->addDay();

                $plan->end_date = match ($plan->recurrence) {
                    RecurrenceType::Daily => $plan->start_date->copy()->addDay(),
                    RecurrenceType::Weekly => $plan->start_date->copy()->addWeek(),
                    RecurrenceType::Monthly => $plan->start_date->copy()->addMonth(),
                    default => $plan->start_date->copy()->addMonth(),
                };

                $plan->save();

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

            } catch (\Throwable $e) {
                DB::rollBack();
                \Log::error("Recurring donation failed for plan ID {$plan->id}: " . $e->getMessage());
            }
        }
    }

    private function validatePlanDates(Plan $plan): void
    {
        $start = $plan->start_date;
        $end = $plan->end_date;

        if (!$start || !$end) {
            throw new \Exception("Plan ID {$plan->id} has invalid start or end date.");
        }

        if ($start->gt($end)) {
            throw new \Exception("Plan ID {$plan->id} start_date is after end_date.");
        }

        $diffDays = $start->diffInDays($end);

        $expectedDays = match ($plan->recurrence) {
            RecurrenceType::Daily => 1,
            RecurrenceType::Weekly => 7,
            RecurrenceType::Monthly => 30,
            default => 30,
        };

        if (abs($diffDays - $expectedDays) > 1) {
            throw new \Exception("Plan ID {$plan->id} date range ({$diffDays} days) does not match recurrence type {$plan->recurrence->value}.");
        }
    }
}
