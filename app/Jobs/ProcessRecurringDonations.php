<?php

namespace App\Jobs;


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

/*
هاد الصح قبل الاشعارات

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
*/
// هاد مع اشعارات :

use App\Enums\RecurrenceType;
use App\Models\Box;
use App\Models\Plan;
use App\Models\Transaction;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ProcessRecurringDonations implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public function handle(): void
    {
        $now = Carbon::now();

        // جلب الخطط المفعلة التي انتهى تاريخها أو تساوي اليوم
        $plans = Plan::where('is_activated', true)
            ->whereDate('end_date', '<=', $now->startOfDay())
            ->with('user', 'sponsorship.campaign')
            ->get();

        foreach ($plans as $plan) {
            DB::beginTransaction();

            try {
                // تحقق من تواريخ الخطة قبل أي معالجة
                $this->validatePlanDates($plan);

                $user = $plan->user;
                $isSponsorship = !is_null($plan->sponsorship_id);
                $campaign = $isSponsorship ? $plan->sponsorship->campaign : null;

                // إرسال إشعار قبل الخصم للكفالة (3 أيام)
                if ($isSponsorship && $campaign) {
                    $nextPaymentDate = $plan->start_date;
                    if ($now->diffInDays($nextPaymentDate) === 3) {
                        $this->sendNotification(
                            $user,
                            $campaign,
                            "سوف يتم سحب تبرع الكفالة الخاص بك خلال 3 أيام. يمكنك إيقاف الخطة إذا أردت.",
                            "Your sponsorship donation will be charged in 3 days. You can deactivate the plan if you want."
                        );
                    }
                }

                // إرسال إشعار قبل خصم التبرع الدوري العام حسب التكرار
                if (!$isSponsorship) {
                    $nextPaymentDate = $plan->start_date;
                    $daysBefore = match ($plan->recurrence) {
                        RecurrenceType::Daily->value => 1,
                        RecurrenceType::Weekly->value => 3,
                        RecurrenceType::Monthly->value => 3,
                        default => 3,
                    };

                    if ($now->diffInDays($nextPaymentDate) === $daysBefore) {
                        $this->sendNotification(
                            $user,
                            null,
                            "سيتم خصم تبرعك الدوري خلال {$daysBefore} أيام. يمكنك إيقاف التفعيل إذا أردت.",
                            "Your recurring donation will be charged in {$daysBefore} days. You can deactivate the plan if you want."
                        );
                    }
                }

                // ======================================
                // معالجة الخصم
                // ======================================

                if ($isSponsorship && $campaign && $campaign->status === 'completed') {
                    DB::commit();
                    continue;
                }

                // فشل الدفع (الرصيد غير كافي)
                if ($user->balance < $plan->amount) {
                    $this->sendNotification(
                        $user,
                        $campaign,
                        "فشل خصم التبرع الدوري لعدم كفاية الرصيد في محفظتك.",
                        "Your recurring donation failed due to insufficient balance in your wallet."
                    );

                    DB::commit();
                    continue;
                }

                // نجاح الدفع - خصم المبلغ
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

                // إشعار نجاح الدفع
                $this->sendNotification(
                    $user,
                    $campaign,
                    "تم خصم مبلغ {$plan->amount} من محفظتك للتبرع الدوري بنجاح.",
                    "Your recurring donation of {$plan->amount} has been deducted successfully."
                );

                // تحديث تواريخ البداية والنهاية للخطة القادمة
                $plan->start_date = $plan->end_date->copy()->addDay();
                $plan->end_date = match ($plan->recurrence) {
                    RecurrenceType::Daily => $plan->start_date->copy()->addDay(),
                    RecurrenceType::Weekly => $plan->start_date->copy()->addWeek(),
                    RecurrenceType::Monthly => $plan->start_date->copy()->addMonth(),
                    default => $plan->start_date->copy()->addMonth(),
                };
                $plan->save();

                // تحديث مبلغ الكفالة المجمّع إذا كانت مرتبطة بكفالة
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
            RecurrenceType::Daily->value => 1,
            RecurrenceType::Weekly->value => 7,
            RecurrenceType::Monthly->value => 30,
            default => 30,
        };

        if (abs($diffDays - $expectedDays) > 1) {
            throw new \Exception("Plan ID {$plan->id} date range ({$diffDays} days) does not match recurrence type {$plan->recurrence->value}.");
        }
    }

    private function sendNotification($user, $campaign = null, string $body_ar, string $body_en)
    {
        $title = $campaign
            ? ['ar' => "تنبيه الكفالة", 'en' => "Sponsorship Alert"]
            : ['ar' => "تنبيه التبرع الدوري", 'en' => "Recurring Donation Alert"];

        $notificationService = app()->make(NotificationService::class);
        $notificationService->sendFcmNotification(new Request([
            'user_id' => $user->id,
            'title_en' => $title['en'],
            'title_ar' => $title['ar'],
            'body_en' => $body_en,
            'body_ar' => $body_ar,
        ]));
    }
}



