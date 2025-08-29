<?php

namespace App\Http\Controllers\Donation_Type\Sponsorship;

use App\Enums\RecurrenceType;
use App\Http\Controllers\Controller;
use App\Models\Box;
use App\Models\Plan;
use App\Models\Sponsorship;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class PlanController extends Controller
{

// إنشاء خطة كفالة بدون تفعيل
    public function createPlanForSponsorship(Request $request, $sponsorshipId)
    {
        $user = auth()->user();
        $locale = app()->getLocale(); // تحديد اللغة الحالية

        // تحقق من وجود الكفالة
        $sponsorship = Sponsorship::findOrFail($sponsorshipId);

        // تحقق من وجود قيمة amount في الطلب
      //  $request->validate([
      //      'amount' => 'required|numeric|min:1',
     //   ]);

        // إنشاء الخطة
        $plan = Plan::create([
            'user_id' => $user->id,
            'sponsorship_id' => $sponsorship->id,
            'amount' => null,
            'recurrence' => 'monthly',
            'is_activated' => false,
            'start_date' => now(),
            'end_date'=> now()->copy()->addMonth(),
        ]);

        $message = $locale === 'ar'
            ? 'تم إنشاء خطة كفالة بنجاح'
            : 'Sponsorship plan created successfully';

        return response()->json([
            'message' => $message,
            'plan_id' => $plan->id,
        ], 201);
    }
    public function activatePlan(Request $request, $planId)
    {
        $user = auth()->user();
        $locale = app()->getLocale();

        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $amountToPay = $request->input('amount');

        $plan = Plan::where('id', $planId)
            ->where('user_id', $user->id)
            ->whereNotNull('sponsorship_id')
            ->where('is_activated', false)
            ->with('sponsorship.campaign')
            ->lockForUpdate()
            ->first();

        if (!$plan) {
            return response()->json([
                'message' => $locale === 'ar' ? 'الخطة غير موجودة أو مفعلة مسبقًا.' : 'Plan not found or already activated.'
            ], 404);
        }

        $campaign = $plan->sponsorship->campaign;

        if ($campaign->status === 'complete' || $campaign->collected_amount >= $campaign->goal_amount) {
            return response()->json([
                'message' => $locale === 'ar' ? 'الكفالة مكتملة ولا يمكن تفعيل خطط جديدة.' : 'Sponsorship is completed, no new plans can be activated.'
            ], 422);
        }

        if ($user->balance < $amountToPay) {
            return response()->json([
                'message' => $locale === 'ar' ? 'رصيد المحفظة غير كافٍ لتفعيل الخطة.' : 'Insufficient wallet balance to activate the plan.',
                'wallet_balance' => $user->balance,
                'required_amount' => $amountToPay
            ], 422);
        }

        $totalActiveAmount = Plan::where('sponsorship_id', $plan->sponsorship_id)
            ->where('is_activated', true)
            ->sum('amount');

        $remaining = $campaign->goal_amount - $totalActiveAmount;

        if ($amountToPay > $remaining) {
            return response()->json([
                'message' => $locale === 'ar'
                    ? "تجاوزت المبلغ المطلوب للكفالة. المتبقي: $remaining"
                    : "Sponsorship goal exceeded. Remaining amount: $remaining",
                'remaining_amount' => $remaining
            ], 422);
        }

        DB::beginTransaction();

        try {
            // خصم الرصيد من المستخدم
            $user->balance -= $amountToPay;
            $user->save();

            // إنشاء ترانزاكشن واحدة: donation/in
            $transaction = Transaction::create([
                'user_id'     => $user->id,
                'amount'      => $amountToPay,
                'type'        => 'donation',
                'direction'   => 'in',
                'campaign_id' => $campaign->id,
            ]);

            // تفعيل الخطة
            $plan->update([
                'is_activated' => true,
                'amount'       => $amountToPay,
                'start_date'   => now(),
                'end_date'     => now()->addMonth(),
            ]);

            // تحديث المبلغ المجموع للحملة
            $campaign->collected_amount += $amountToPay;
            if ($campaign->collected_amount >= $campaign->goal_amount) {
                $campaign->status = 'complete';
                $campaign->completed_at = now();
            }
            $campaign->save();

            DB::commit();

            return response()->json([
                'message' => $locale === 'ar' ? 'تم تفعيل خطة الكفالة بنجاح' : 'Sponsorship plan activated successfully',
                'data' => [
                    'plan' => $plan,
                    'campaign' => [
                        'goal_amount'       => (float) $campaign->goal_amount,
                        'collected_amount'  => (float) $campaign->collected_amount,
                        'remaining_amount'  => max(0, (float) $campaign->goal_amount - (float) $campaign->collected_amount),
                        'status'            => $campaign->status,
                    ],
                    'transaction' => [
                        'id'         => $transaction->id,
                        'amount'     => $transaction->amount,
                        'type'       => $transaction->type,
                        'direction'  => $transaction->direction,
                        'pdf_url'    => $transaction->pdf_url ?? null,
                        'created_at' => $transaction->created_at,
                    ],
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $locale === 'ar' ? 'حدث خطأ أثناء التفعيل' : 'Error during activation',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    // تفعيل خطة الكفالة
    /*
    public function createAndActivatePlanForSponsorship(Request $request, $sponsorshipId)
    {
        $user = auth()->user();
        $locale = app()->getLocale();

        // تحقق من وجود الكفالة
        $sponsorship = Sponsorship::findOrFail($sponsorshipId);
        if(!$sponsorship)
            return response()->json([
                'message' => $locale === 'ar' ? 'الكفالة غير موجودة.' : 'Sponsorship is not existed.'
            ],500);

        // تحقق من صحة المبلغ
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $amountToPay = $request->input('amount');

        $campaign = $sponsorship->campaign;

        if ($campaign->status === 'complete' || $campaign->collected_amount >= $campaign->goal_amount) {
            return response()->json([
                'message' => $locale === 'ar' ? 'الكفالة مكتملة ولا يمكن إنشاء خطط جديدة.' : 'Sponsorship is completed, no new plans can be created.'
            ], 422);
        }

        if ($user->balance < $amountToPay) {
            return response()->json([
                'message' => $locale === 'ar' ? 'رصيد المحفظة غير كافٍ لتفعيل الخطة.' : 'Insufficient wallet balance to activate the plan.',
                'wallet_balance' => $user->balance,
                'required_amount' => $amountToPay
            ], 422);
        }

        $totalActiveAmount = Plan::where('sponsorship_id', $sponsorship->id)
            ->where('is_activated', true)
            ->sum('amount');

        $remaining = $campaign->goal_amount - $totalActiveAmount;

        if ($amountToPay > $remaining) {
            return response()->json([
                'message' => $locale === 'ar'
                    ? "تجاوزت المبلغ المطلوب للكفالة. المتبقي: $remaining"
                    : "Sponsorship goal exceeded. Remaining amount: $remaining",
                'remaining_amount' => $remaining
            ], 422);
        }

        DB::beginTransaction();

        try {
            // إنشاء الخطة مفعلة مباشرة
            $plan = Plan::create([
                'user_id' => $user->id,
                'sponsorship_id' => $sponsorship->id,
                'amount' => $amountToPay,
                'recurrence' => 'monthly',
                'is_activated' => true,
                'start_date' => now(),
                'end_date' => now()->addMonth(),
            ]);

            // خصم الرصيد من المستخدم
            $user->balance -= $amountToPay;
            $user->save();

            // إنشاء معاملة ترانزاكشن (donation / in)
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'amount' => $amountToPay,
                'type' => 'donation',
                'direction' => 'in',
                'campaign_id' => $campaign->id,
            ]);

            // تحديث مبلغ الحملة المجموع
            $campaign->collected_amount += $amountToPay;
            if ($campaign->collected_amount >= $campaign->goal_amount) {
                $campaign->status = 'complete';
                $campaign->completed_at = now();
            }
            $campaign->save();

            DB::commit();

            return response()->json([
                'message' => $locale === 'ar' ? 'تم إنشاء وتفعيل خطة الكفالة بنجاح' : 'Sponsorship plan created and activated successfully',
                'data' => [
                    'plan' => [
                        'id' => $plan->id,
                        'user_id' => $plan->user_id,
                        'sponsorship_id' => $plan->sponsorship_id,
                        'amount' => $plan->amount,
                        'recurrence' => $plan->recurrence,
                        'is_activated' => $plan->is_activated,
                        'start_date' => $plan->start_date->format('Y-m-d H:i:s'),
                        'end_date' => $plan->end_date->format('Y-m-d H:i:s'),
                        'created_at' => $plan->created_at,
                        'updated_at' => $plan->updated_at,
                        'recurrence_label' => $plan->recurrence_label,
                    ],

                    'campaign' => [
                        'goal_amount' => (float) $campaign->goal_amount,
                        'collected_amount' => (float) $campaign->collected_amount,
                        'remaining_amount' => max(0, (float) $campaign->goal_amount - (float) $campaign->collected_amount),
                        'status' => $campaign->status,
                    ],
                    'transaction' => [
                        'id' => $transaction->id,
                        'amount' => $transaction->amount,
                        'type' => $transaction->type,
                        'direction' => $transaction->direction,
                        'pdf_url' => $transaction->pdf_url ?? null,
                        'created_at' => $transaction->created_at->format('Y-m-d H:i:s'),
                    ],
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $locale === 'ar' ? 'حدث خطأ أثناء إنشاء الخطة' : 'Error during plan creation',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    */

    public function createAndActivatePlanForSponsorship(Request $request, $sponsorshipId)
    {
        $user = auth()->user();
        $locale = app()->getLocale();

        // تحقق من وجود الكفالة
        $sponsorship = Sponsorship::findOrFail($sponsorshipId);

        // تحقق من صحة المبلغ
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $amountToPay = $request->input('amount');
        $campaign = $sponsorship->campaign;

        if ($campaign->status === 'complete' || $campaign->collected_amount >= $campaign->goal_amount) {
            return response()->json([
                'message' => $locale === 'ar' ? 'الكفالة مكتملة ولا يمكن إنشاء خطط جديدة.' : 'Sponsorship is completed, no new plans can be created.'
            ], 422);
        }

        if ($user->balance < $amountToPay) {
            return response()->json([
                'message' => $locale === 'ar' ? 'رصيد المحفظة غير كافٍ لتفعيل الخطة.' : 'Insufficient wallet balance to activate the plan.',
                'wallet_balance' => $user->balance,
                'required_amount' => $amountToPay
            ], 422);
        }

        $totalActiveAmount = Plan::where('sponsorship_id', $sponsorship->id)
            ->where('is_activated', true)
            ->sum('amount');

        $remaining = $campaign->goal_amount - $totalActiveAmount;

        if ($amountToPay > $remaining) {
            return response()->json([
                'message' => $locale === 'ar'
                    ? "تجاوزت المبلغ المطلوب للكفالة. المتبقي: $remaining"
                    : "Sponsorship goal exceeded. Remaining amount: $remaining",
                'remaining_amount' => $remaining
            ], 422);
        }

        DB::beginTransaction();

        try {
            // إنشاء الخطة مفعلة مباشرة
            $plan = Plan::create([
                'user_id' => $user->id,
                'sponsorship_id' => $sponsorship->id,
                'amount' => $amountToPay,
                'recurrence' => 'monthly',
                'is_activated' => true,
                'start_date' => now(),
                'end_date' => now()->addMonth(),
            ]);

            // خصم الرصيد من المستخدم
            $user->balance -= $amountToPay;
            $user->save();

            // إنشاء معاملة
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'amount' => $amountToPay,
                'type' => 'donation',
                'direction' => 'in',
                'campaign_id' => $campaign->id,
            ]);

            // تحديث مبلغ الحملة المجموع
            $campaign->collected_amount += $amountToPay;
            if ($campaign->collected_amount >= $campaign->goal_amount) {
                $campaign->status = 'complete';
                $campaign->completed_at = now();
            }
            $campaign->save();

            DB::commit();

            // إرسال إشعار آمن
            try {
                $notificationService = app()->make(\App\Services\NotificationService::class);
                $title = [
                    'en' => "Sponsorship Plan Activated",
                    'ar' => "تم تفعيل خطة الكفالة",
                ];
                $body = [
                    'en' => "Your monthly sponsorship plan has been activated. The donation will be withdrawn from your wallet every month unless you cancel.",
                    'ar' => "تم تفعيل خطة الكفالة الشهرية الخاصة بك. سيتم خصم التبرع من محفظتك شهريًا إلا إذا قمت بالإلغاء.",
                ];
                $notificationService->sendFcmNotification(new \Illuminate\Http\Request([
                    'user_id' => $user->id,
                    'title_en' => $title['en'],
                    'title_ar' => $title['ar'],
                    'body_en' => $body['en'],
                    'body_ar' => $body['ar'],
                ]));
            } catch (\Exception $e) {
                \Log::error("Failed to send sponsorship notification: " . $e->getMessage());
            }

            return response()->json([
                'message' => $locale === 'ar' ? 'تم إنشاء وتفعيل خطة الكفالة بنجاح' : 'Sponsorship plan created and activated successfully',
                'data' => [
                    'plan' => [
                        'id' => $plan->id,
                        'user_id' => $plan->user_id,
                        'sponsorship_id' => $plan->sponsorship_id,
                        'amount' => $plan->amount,
                        'recurrence' => $plan->recurrence,
                        'is_activated' => $plan->is_activated,
                        'start_date' => $plan->start_date->format('Y-m-d H:i:s'),
                        'end_date' => $plan->end_date->format('Y-m-d H:i:s'),
                        'created_at' => $plan->created_at,
                        'updated_at' => $plan->updated_at,
                        'recurrence_label' => $plan->recurrence_label,
                    ],

                    'campaign' => [
                        'goal_amount' => (float) $campaign->goal_amount,
                        'collected_amount' => (float) $campaign->collected_amount,
                        'remaining_amount' => max(0, (float) $campaign->goal_amount - (float) $campaign->collected_amount),
                        'status' => $campaign->status,
                    ],
                    'transaction' => [
                        'id' => $transaction->id,
                        'amount' => $transaction->amount,
                        'type' => $transaction->type,
                        'direction' => $transaction->direction,
                        'pdf_url' => $transaction->pdf_url ?? null,
                        'created_at' => $transaction->created_at->format('Y-m-d H:i:s'),
                    ],
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $locale === 'ar' ? 'حدث خطأ أثناء إنشاء الخطة' : 'Error during plan creation',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // إيقاف خطة الكفالة
    /*
    public function deactivatePlan($planId)
    {
        $user = auth()->user();
        $locale = app()->getLocale();

        $plan = Plan::where('id', $planId)
            ->where('user_id', $user->id)
            ->whereNotNull('sponsorship_id')
            ->where('is_activated', true)
            ->with('sponsorship.campaign')
            ->lockForUpdate()
            ->first();

        if (!$plan) {
            return response()->json([
                'message' => $locale === 'ar' ? 'الخطة غير موجودة أو غير مفعّلة' : 'Plan not found or not active',
            ], 404);
        }

        $plan->update([
            'is_activated' => false,
            'end_date' => now(),
        ]);

        $campaign = $plan->sponsorship->campaign;

        return response()->json([
            'message' => $locale === 'ar' ? 'تم إيقاف الخطة بنجاح' : 'Plan deactivated successfully',
            'data' => [
                'plan' => [
                    'id' => $plan->id,
                    'sponsorship_id' => $plan->sponsorship_id,
                    'amount' => $plan->amount,
                    'is_activated' => $plan->is_activated,
                    'end_date' => $plan->end_date,
                ],
                'campaign' => [
                    'goal_amount' => (float) $campaign->goal_amount,
                    'collected_amount' => (float) $campaign->collected_amount,
                    'remaining_amount' => max(0, $campaign->goal_amount - $campaign->collected_amount),
                    'status' => $campaign->status,
                ]
            ]
        ]);
    }
*/
    public function deactivatePlan($planId)
    {
        $user = auth()->user();
        $locale = app()->getLocale();

        $plan = Plan::where('id', $planId)
            ->where('user_id', $user->id)
            ->whereNotNull('sponsorship_id')
            ->where('is_activated', true)
            ->with('sponsorship.campaign')
            ->lockForUpdate()
            ->first();

        if (!$plan) {
            return response()->json([
                'message' => $locale === 'ar' ? 'الخطة غير موجودة أو غير مفعّلة' : 'Plan not found or not active',
            ], 404);
        }

        $plan->update([
            'is_activated' => false,
            'end_date' => now(),
        ]);

        $campaign = $plan->sponsorship->campaign;

        // إرسال إشعار آمن
        try {
            $notificationService = app()->make(\App\Services\NotificationService::class);

            $title = [
                'en' => "Sponsorship Plan Deactivated",
                'ar' => "تم إيقاف خطة الكفالة",
            ];

            $body = [
                'en' => "Your sponsorship plan for '{$campaign->title_en}' has been deactivated. You can reactivate it anytime if you wish.",
                'ar' => "تم إيقاف خطة الكفالة الخاصة بك '{$campaign->title_ar}'، يمكنك إعادة تفعيلها إذا رغبت.",
            ];

            $notificationService->sendFcmNotification(new \Illuminate\Http\Request([
                'user_id' => $user->id,
                'title_en' => $title['en'],
                'title_ar' => $title['ar'],
                'body_en' => $body['en'],
                'body_ar' => $body['ar'],
            ]));
        } catch (\Exception $e) {
            \Log::error("Failed to send sponsorship deactivation notification: " . $e->getMessage());
        }

        return response()->json([
            'message' => $locale === 'ar' ? 'تم إيقاف الخطة بنجاح' : 'Plan deactivated successfully',
            'data' => [
                'plan' => [
                    'id' => $plan->id,
                    'sponsorship_id' => $plan->sponsorship_id,
                    'amount' => $plan->amount,
                    'is_activated' => $plan->is_activated,
                    'end_date' => $plan->end_date,
                ],
                'campaign' => [
                    'goal_amount' => (float) $campaign->goal_amount,
                    'collected_amount' => (float) $campaign->collected_amount,
                    'remaining_amount' => max(0, $campaign->goal_amount - $campaign->collected_amount),
                    'status' => $campaign->status,
                ]
            ]
        ]);
    }


    // إعادة تفعيل خطة كفالة
    /*
    public function reactivatePlan($planId)
    {
        $user = auth()->user();
        $locale = app()->getLocale();

        $plan = Plan::where('id', $planId)
            ->where('user_id', $user->id)
            ->whereNotNull('sponsorship_id')
            ->where('is_activated', false)
            ->with('sponsorship.campaign')
            ->lockForUpdate()
            ->first();

        if (!$plan) {
            return response()->json([
                'message' => $locale === 'ar' ? 'الخطة غير موجودة أو مفعّلة مسبقًا' : 'Plan not found or already active',
            ], 404);
        }

        $campaign = $plan->sponsorship->campaign;

        // لا يمكن إعادة تفعيل الخطة إذا كانت الكفالة مكتملة
        if ($campaign->status === 'complete') {
            return response()->json([
                'message' => $locale === 'ar' ? 'الكفالة مكتملة ولا يمكن تفعيل الخطط.' : 'Sponsorship is completed, cannot reactivate plan.',
            ], 422);
        }

        // إعادة التفعيل دون خصم رصيد جديد أو ترانزاكشن
        $now = now();
        $plan->update([
            'is_activated' => true,
            'start_date' => $now,
            'end_date' => $now->copy()->addMonth(),
        ]);

        return response()->json([
            'message' => $locale === 'ar' ? 'تم إعادة تفعيل الخطة بنجاح' : 'Plan reactivated successfully',
            'data' => [
                'plan' => [
                    'id' => $plan->id,
                    'amount' => $plan->amount,
                    'start_date' => $plan->start_date,
                    'end_date' => $plan->end_date,
                    'is_activated' => $plan->is_activated,
                ],
                'campaign' => [
                    'goal_amount' => (float) $campaign->goal_amount,
                    'collected_amount' => (float) $campaign->collected_amount,
                    'remaining_amount' => max(0, $campaign->goal_amount - $campaign->collected_amount),
                    'status' => $campaign->status,
                ]
            ]
        ]);
    }
*/
    public function reactivatePlan($planId)
    {
        $user = auth()->user();
        $locale = app()->getLocale();

        $plan = Plan::where('id', $planId)
            ->where('user_id', $user->id)
            ->whereNotNull('sponsorship_id')
            ->where('is_activated', false)
            ->with('sponsorship.campaign')
            ->lockForUpdate()
            ->first();

        if (!$plan) {
            return response()->json([
                'message' => $locale === 'ar' ? 'الخطة غير موجودة أو مفعّلة مسبقًا' : 'Plan not found or already active',
            ], 404);
        }

        $campaign = $plan->sponsorship->campaign;

        if ($campaign->status === 'complete') {
            return response()->json([
                'message' => $locale === 'ar' ? 'الكفالة مكتملة ولا يمكن تفعيل الخطط.' : 'Sponsorship is completed, cannot reactivate plan.',
            ], 422);
        }

        $now = now();
        $plan->update([
            'is_activated' => true,
            'start_date' => $now,
            'end_date' => $now->copy()->addMonth(),
        ]);

        // إشعار آمن
        try {
            $notificationService = app()->make(\App\Services\NotificationService::class);

            $title = [
                'en' => 'Sponsorship Plan Reactivated',
                'ar' => 'إعادة تفعيل خطة الكفالة',
            ];

            $body = [
                'en' => "Your sponsorship plan for '{$campaign->title_en}' has been reactivated. You can stop it anytime if you wish.",
                'ar' => "تم إعادة تفعيل خطة الكفالة الخاصة بك '{$campaign->title_ar}'، يمكنك إيقافها في أي وقت إذا رغبت.",
            ];

            $notificationService->sendFcmNotification(new \Illuminate\Http\Request([
                'user_id' => $user->id,
                'title_en' => $title['en'],
                'title_ar' => $title['ar'],
                'body_en' => $body['en'],
                'body_ar' => $body['ar'],
            ]));
        } catch (\Exception $e) {
            \Log::error("Failed to send sponsorship reactivation notification: " . $e->getMessage());
        }

        return response()->json([
            'message' => $locale === 'ar' ? 'تم إعادة تفعيل الخطة بنجاح' : 'Plan reactivated successfully',
            'data' => [
                'plan' => [
                    'id' => $plan->id,
                    'amount' => $plan->amount,
                    'start_date' => $plan->start_date,
                    'end_date' => $plan->end_date,
                    'is_activated' => $plan->is_activated,
                ],
                'campaign' => [
                    'goal_amount' => (float) $campaign->goal_amount,
                    'collected_amount' => (float) $campaign->collected_amount,
                    'remaining_amount' => max(0, $campaign->goal_amount - $campaign->collected_amount),
                    'status' => $campaign->status,
                ]
            ]
        ]);
    }


    // كفالاتي ك متبرع
    public function getSponsorshipPlansForUser()
    {
        $user = auth()->user();
        $locale = app()->getLocale();

        $plans = Plan::where('user_id', $user->id)
            ->whereNotNull('sponsorship_id')
            ->with('sponsorship.campaign')
            ->latest('created_at')
            ->get();

        $data = $plans->map(function ($plan) use ($locale) {
            $transactions = collect();

            if ($plan->start_date && $plan->end_date) {
                $transactions = Transaction::where('campaign_id', $plan->sponsorship->campaign_id)
                    ->where('user_id', $plan->user_id)
                    ->whereBetween('created_at', [
                        Carbon::parse($plan->start_date),
                        Carbon::parse($plan->end_date)
                    ])
                    ->where('amount', $plan->amount) // ← فلترة بالقيمة
                    ->orderBy('created_at', 'desc')
                    ->get([
                        'id',
                        'amount',
                        'type',
                        'direction',
                        'pdf_url',
                        'created_at'
                    ]);

            }

            return [
                'id' => $plan->id,
                'amount' => $plan->amount,
                'recurrence' => $plan->recurrence,
                'start_date' => $plan->start_date ? Carbon::parse($plan->start_date)->format('Y-m-d') : null,
                'end_date' => $plan->end_date ? Carbon::parse($plan->end_date)->format('Y-m-d') : null,
                'status' => $locale === 'ar'
                    ? ($plan->is_activated ? 'نشطة' : 'ملغاة')
                    : ($plan->is_activated ? 'active' : 'cancelled'),
                'is_activated'=>$plan->is_activated,
                'sponsorship' => [
                    'id' => $plan->sponsorship->id,
                    'title' => $locale === 'ar'
                        ? $plan->sponsorship->campaign->title_ar
                        : $plan->sponsorship->campaign->title_en,
                    'image'=>$plan->sponsorship->campaign->image ?? null,
                ],
                'transactions' => $transactions->map(function ($t) {
                    return [
                        'id' => $t->id,
                        'amount' => $t->amount,
                        'type' => $t->type,
                        'direction' => $t->direction,
                        'pdf_url' => $t->pdf_url ?? null,
                        'date' => $t->created_at->format('Y-m-d'),
                    ];
                }),
            ];
        });

        return response()->json([
            'message' => $locale === 'ar' ? 'تم جلب الكفالات الخاصة بك' : 'Your sponsorship plans retrieved',
            'data' => $data
        ]);
    }

    // ADMIN
    // عرض الكفلاء
    public function getSponsorshipsDonors()
    {
        $locale = app()->getLocale();
        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $plans = Plan::whereNotNull('sponsorship_id')
            ->with(['user:id,name,email,phone', 'sponsorship.campaign'])
            ->latest('created_at')
            ->get();

        $data = $plans->map(function ($plan) use ($locale) {
            return [
                'sponsorship' => [
                    'id' => $plan->sponsorship->id,
                     'title' => $locale === 'ar' ? $plan->sponsorship->campaign->title_ar : $plan->sponsorship->campaign->title_en,
                ],
                'id' => $plan->id,
                'amount' => $plan->amount,
                'recurrence' => $plan->recurrence,
                'is_activated' => $plan->is_activated,
                'start_date' => $plan->start_date ? Carbon::parse($plan->start_date)->format('Y-m-d') : null,
                'end_date' => $plan->end_date ? Carbon::parse($plan->end_date)->format('Y-m-d') : null,
                'user' => $plan->user,

            ];
        });

        return response()->json([
            'message' => $locale === 'ar' ? 'تم جلب الكفلاء' : 'Sponsorship donors retrieved',
            'data' => $data,
        ]);
    }

    // التبرعات الدورية

    // تفعيل
    /*
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
            $periodicBox = Box::where('name_en', 'Periodic donation')->first();

            if (!$periodicBox) {
                return response()->json([
                    'message' => $locale === 'ar'
                        ? 'صندوق التبرعات العامة غير موجود.'
                        : 'Periodic donation box not found.'
                ], 500);
            }

            // تحقق من الرصيد قبل أي حفظ
            if ($user->balance < $request->amount) {
                return response()->json([
                    'message' => $locale === 'ar'
                        ? 'الرصيد غير كافٍ في المحفظة.'
                        : 'Insufficient wallet balance.',
                ], 400);
            }

            // إنشاء الخطة
            $plan = new Plan();
            $plan->user_id = $user->id;
            $plan->amount = $request->amount;
            $plan->recurrence = $request->recurrence;
            $plan->sponsorship_id = null; // تبرع عام
            $plan->start_date = now();
            $plan->is_activated = true;

            // تعيين end_date حسب التكرار
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

            // خصم الرصيد من المستخدم
            $user->balance -= $request->amount;
            $user->save();

            // زيادة رصيد الصندوق
            $periodicBox->balance += $request->amount;
            $periodicBox->save();

            // إنشاء المعاملة
            Transaction::create([
                'user_id' => $user->id,
                'type' => 'donation',
                'direction' => 'in',
                'amount' => $request->amount,
                'box_id' => $periodicBox->id,
            ]);

            DB::commit();

            $recurrenceLabel = $plan->recurrence->label($locale);

            $transaction = Transaction::where('user_id', $user->id)
                ->where('type', 'donation')
                ->where('box_id', $periodicBox->id)
                ->latest()
                ->first();

            return response()->json([
                'message' => $locale === 'ar'
                    ? 'تم تفعيل خطة التبرع العام بنجاح'
                    : 'Periodic donation plan activated successfully',
                'data' => [
                    'id' => $plan->id,
                    'amount' => $plan->amount,
                    'recurrence' => $plan->recurrence,
                    'recurrence_label' => $recurrenceLabel,
                    'start_date' => $plan->start_date->format('Y-m-d'), // أو أي تنسيق تريده
                    'end_date' => $plan->end_date->format('Y-m-d'),
                    'is_activated' => $plan->is_activated,
                    'receipt_url' => $transaction?->pdf_url
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
*/
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
            $periodicBox = Box::where('name_en', 'Periodic donation')->first();

            if (!$periodicBox) {
                return response()->json([
                    'message' => $locale === 'ar'
                        ? 'صندوق التبرعات العامة غير موجود.'
                        : 'Periodic donation box not found.'
                ], 500);
            }

            if ($user->balance < $request->amount) {
                return response()->json([
                    'message' => $locale === 'ar'
                        ? 'الرصيد غير كافٍ في المحفظة.'
                        : 'Insufficient wallet balance.',
                ], 400);
            }

            $plan = new Plan();
            $plan->user_id = $user->id;
            $plan->amount = $request->amount;
            $plan->recurrence = $request->recurrence;
            $plan->sponsorship_id = null; // تبرع عام
            $plan->start_date = now();
            $plan->is_activated = true;

            switch ($request->recurrence) {
                case 'daily':
                    $plan->end_date = now()->addDay();
                    break;
                case 'weekly':
                    $plan->end_date = now()->addWeek();
                    break;
                case 'monthly':
                default:
                    $plan->end_date = now()->addMonth();
            }

            $plan->save();

            $user->balance -= $request->amount;
            $user->save();

            $periodicBox->balance += $request->amount;
            $periodicBox->save();

            $transaction = Transaction::create([
                'user_id' => $user->id,
                'type' => 'donation',
                'direction' => 'in',
                'amount' => $request->amount,
                'box_id' => $periodicBox->id,
            ]);

            $transaction->refresh();
            DB::commit();

            // إشعار آمن
            try {
                $recurrenceTextEn = match ($plan->recurrence) {
                    \App\Enums\RecurrenceType::Daily => "This donation will be withdrawn daily from your wallet.",
                    \App\Enums\RecurrenceType::Weekly => "This donation will be withdrawn weekly from your wallet.",
                    \App\Enums\RecurrenceType::Monthly => "This donation will be withdrawn monthly from your wallet.",
                };

                $recurrenceTextAr = match ($plan->recurrence) {
                    \App\Enums\RecurrenceType::Daily => "سيتم سحب هذا التبرع يوميًا من محفظتك.",
                    \App\Enums\RecurrenceType::Weekly => "سيتم سحب هذا التبرع أسبوعيًا من محفظتك.",
                    \App\Enums\RecurrenceType::Monthly => "سيتم سحب هذا التبرع شهريًا من محفظتك.",
                };

                $bodyEn = $recurrenceTextEn . " You can stop it anytime if you wish.";
                $bodyAr = $recurrenceTextAr . " يمكنك إيقافه في أي وقت إذا رغبت.";

                $notificationService = app()->make(\App\Services\NotificationService::class);
                $notificationService->sendFcmNotification(new \Illuminate\Http\Request([
                    'user_id' => $user->id,
                    'title_en' => 'Recurring Donation Activated',
                    'title_ar' => 'تفعيل التبرع الدوري',
                    'body_en' => $bodyEn,
                    'body_ar' => $bodyAr,
                ]));
            } catch (\Exception $e) {
                \Log::error("Failed to send recurring donation notification: " . $e->getMessage());
            }

            $recurrenceLabel = $plan->recurrence->label($locale);

            return response()->json([
                'message' => $locale === 'ar'
                    ? 'تم تفعيل خطة التبرع العام بنجاح'
                    : 'Periodic donation plan activated successfully',
                'data' => [
                    'id' => $plan->id,
                    'amount' => $plan->amount,
                    'recurrence' => $plan->recurrence,
                    'recurrence_label' => $recurrenceLabel,
                    'start_date' => $plan->start_date->format('Y-m-d'),
                    'end_date' => $plan->end_date->format('Y-m-d'),
                    'is_activated' => $plan->is_activated,
                    'receipt_url' => $transaction?->pdf_url
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $locale === 'ar' ? 'فشل في تفعيل الخطة' : 'Failed to activate the plan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    // الغاء تفعيل
    /*
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
                'message' => $locale === 'ar'
                    ? 'الخطة غير موجودة أو غير مفعلة'
                    : 'Plan not found or not active',
            ], 404);
        }

        $plan->is_activated = false;
        $plan->end_date = now();
        $plan->save();

        return response()->json([
            'message' => $locale === 'ar' ? 'تم إيقاف خطة التبرع العام' : 'Periodic donation plan deactivated',
            'data' => [
                'id' => $plan->id,
                'end_date' => $plan->end_date->format('Y-m-d'),
                'is_activated' => $plan->is_activated,
            ],
        ]);

    }
*/
    public function deactivateRecurring($planId)
    {
        $user = auth()->user();
        $locale = app()->getLocale();

        $plan = Plan::where('id', $planId)
            ->where('user_id', $user->id)
            ->whereNull('sponsorship_id')
            ->first();

        if (!$plan || !$plan->is_activated) {
            return response()->json([
                'message' => $locale === 'ar'
                    ? 'الخطة غير موجودة أو غير مفعلة'
                    : 'Plan not found or not active',
            ], 404);
        }

        // إيقاف الخطة
        $plan->is_activated = false;
        $plan->end_date = now();
        $plan->save();

        // نص الإشعار حسب التكرار
        $recurrenceTextEn = match ($plan->recurrence)  {
            'daily' => "Your daily recurring donation has been stopped.",
            'weekly' => "Your weekly recurring donation has been stopped.",
            'monthly' => "Your monthly recurring donation has been stopped.",
            default => "Your recurring donation has been stopped."
        };

        $recurrenceTextAr = match ($plan->recurrence) {
            'daily' => "تم إيقاف التبرع الدوري اليومي الخاص بك.",
            'weekly' => "تم إيقاف التبرع الدوري الأسبوعي الخاص بك.",
            'monthly' => "تم إيقاف التبرع الدوري الشهري الخاص بك.",
            default => "تم إيقاف خطة التبرع الدوري الخاصة بك."
        };

        $bodyEn = $recurrenceTextEn . " You can reactivate it anytime if you wish.";
        $bodyAr = $recurrenceTextAr . " يمكنك إعادة تفعيله في أي وقت إذا رغبت.";

        // إرسال إشعار آمن
        try {
            $notificationService = app()->make(\App\Services\NotificationService::class);
            $notificationService->sendFcmNotification(new \Illuminate\Http\Request([
                'user_id' => $user->id,
                'title_en' => 'Recurring Donation Deactivated',
                'title_ar' => 'إيقاف خطة التبرع الدوري',
                'body_en' => $bodyEn,
                'body_ar' => $bodyAr,
            ]));
        } catch (\Exception $e) {
            \Log::error("Failed to send recurring donation deactivation notification: " . $e->getMessage());
        }

        return response()->json([
            'message' => $locale === 'ar' ? 'تم إيقاف خطة التبرع العام' : 'Periodic donation plan deactivated',
            'data' => [
                'id' => $plan->id,
                'end_date' => $plan->end_date->format('Y-m-d'),
                'is_activated' => $plan->is_activated,
                'recurrence' => $plan->recurrence,
            ],
        ]);
    }


    // اعادة تفعيل
    /*
    public function reactivateRecurring($planId)
    {
        $user = auth()->user();
        $locale = app()->getLocale();

        $plan = Plan::where('id', $planId)
            ->where('user_id', $user->id)
            ->whereNull('sponsorship_id')
            ->where('is_activated', false)
            ->first();

        if (!$plan) {
            return response()->json([
                'message' => $locale === 'ar'
                    ? 'الخطة غير موجودة أو مفعّلة بالفعل'
                    : 'Plan not found or already active',
            ], 404);
        }

        $now = now();
        $plan->is_activated = true;
        $plan->start_date = $now;

        switch ($plan->recurrence) {
            case RecurrenceType::Daily->value:
                $plan->end_date = $now->copy()->addDay();
                break;
            case RecurrenceType::Weekly->value:
                $plan->end_date = $now->copy()->addWeek();
                break;
            case RecurrenceType::Monthly->value:
            default:
                $plan->end_date = $now->copy()->addMonth();
        }
        $recurrenceLabel = $plan->recurrence->label($locale);

        $plan->save();

        return response()->json([
            'message' => $locale === 'ar' ? 'تم إعادة تفعيل خطة التبرع العام' : 'Periodic donation plan reactivated',
            'data' => [
                'id' => $plan->id,
                'amount' => $plan->amount,
                'recurrence' => $plan->recurrence,
                'recurrence_label' => $recurrenceLabel,
                'start_date' => $plan->start_date->format('Y-m-d'),
                'end_date' => $plan->end_date->format('Y-m-d'),
                'is_activated' => $plan->is_activated,
            ]
        ]);
    }
    */
    public function reactivateRecurring($planId)
    {
        $user = auth()->user();
        $locale = app()->getLocale();

        $plan = Plan::where('id', $planId)
            ->where('user_id', $user->id)
            ->whereNull('sponsorship_id')
            ->where('is_activated', false)
            ->first();

        if (!$plan) {
            return response()->json([
                'message' => $locale === 'ar'
                    ? 'الخطة غير موجودة أو مفعّلة بالفعل'
                    : 'Plan not found or already active',
            ], 404);
        }

        $now = now();
        $plan->is_activated = true;
        $plan->start_date = $now;

        $recurrenceTextEn = '';
        $recurrenceTextAr = '';

        // تحديد end_date ونص الإشعار حسب التكرار
        switch ($plan->recurrence) {
            case RecurrenceType::Daily:
                $plan->end_date = $now->copy()->addDay();
                $recurrenceTextEn = "Your daily recurring donation has been reactivated.";
                $recurrenceTextAr = "تم إعادة تفعيل التبرع الدوري اليومي الخاص بك.";
                break;

            case RecurrenceType::Weekly:
                $plan->end_date = $now->copy()->addWeek();
                $recurrenceTextEn = "Your weekly recurring donation has been reactivated.";
                $recurrenceTextAr = "تم إعادة تفعيل التبرع الدوري الأسبوعي الخاص بك.";
                break;

            case RecurrenceType::Monthly:
                $plan->end_date = $now->copy()->addMonth();
                $recurrenceTextEn = "Your monthly recurring donation has been reactivated.";
                $recurrenceTextAr = "تم إعادة تفعيل التبرع الدوري الشهري الخاص بك.";
                break;
        }

        $recurrenceLabel = $plan->recurrence->label($locale);
        $plan->save();

        // إرسال إشعار آمن
        try {
            $notificationService = app()->make(\App\Services\NotificationService::class);
            $notificationService->sendFcmNotification(new \Illuminate\Http\Request([
                'user_id' => $user->id,
                'title_en' => 'Recurring Donation Reactivated',
                'title_ar' => 'إعادة تفعيل خطة التبرع الدوري',
                'body_en' => $recurrenceTextEn . " You can deactivate it anytime if you wish.",
                'body_ar' => $recurrenceTextAr . " يمكنك إيقافه في أي وقت إذا رغبت.",
            ]));
        } catch (\Exception $e) {
            \Log::error("Failed to send recurring donation reactivation notification: " . $e->getMessage());
        }

        return response()->json([
            'message' => $locale === 'ar' ? 'تم إعادة تفعيل خطة التبرع العام' : 'Periodic donation plan reactivated',
            'data' => [
                'id' => $plan->id,
                'amount' => $plan->amount,
                'recurrence' => $plan->recurrence->value,
                'recurrence_label' => $recurrenceLabel,
                'start_date' => $plan->start_date->format('Y-m-d H:i:s'),
                'end_date' => $plan->end_date->format('Y-m-d H:i:s'),
                'is_activated' => $plan->is_activated,
            ]
        ]);
    }



    // تبرعي الدوري
    public function getRecurringPlan()
    {
        $user = auth()->user();
        $locale = app()->getLocale();

        $plans = Plan::where('user_id', $user->id)
            ->whereNull('sponsorship_id') // فقط التبرع العام
            ->get();

        if ($plans->isEmpty()) {
            return response()->json([
                'message' => $locale === 'ar'
                    ? 'لا يوجد خطة تبرع دوري حالياً'
                    : 'No recurring donation plans found',
            ]);
        }

        $data = $plans->map(function ($plan) use ($locale, $user) {
            // تحديد الفترة لجلب المعاملات المرتبطة بالخطة
            $transactions = collect();

            if ($plan->start_date && $plan->end_date) {
                $transactions = Transaction::where('user_id', $user->id)
                    ->whereNull('campaign_id') // لأن التبرع عام
                    ->whereBetween('created_at', [
                        Carbon::parse($plan->start_date),
                        Carbon::parse($plan->end_date)
                    ])
                    ->where('amount', $plan->amount)
                    ->orderByDesc('created_at')
                    ->get([
                        'id',
                        'amount',
                        'type',
                        'direction',
                        'pdf_url',
                        'created_at'
                    ]);
            }

            $recurrenceLabel = $plan->recurrence->label($locale);

            return [
                'id' => $plan->id,
                'amount' => $plan->amount,
                'recurrence' => $plan->recurrence,
                'recurrence_label' => $recurrenceLabel,
                'status' => $locale === 'ar'
                    ? ($plan->is_activated ? 'نشطة' : 'ملغاة')
                    : ($plan->is_activated ? 'active' : 'cancelled'),
                'is_activated'=>$plan->is_activated,
                'start_date' => $plan->start_date ,
            // ? Carbon::parse($plan->start_date),
                //->format('Y-m-d') : null,
                'end_date' => $plan->end_date ,
                    //? Carbon::parse($plan->end_date),
                //->format('Y-m-d') : null,
                'transactions' => $transactions->map(function ($t) {
                    return [
                        'id' => $t->id,
                        'amount' => $t->amount,
                        'type' => $t->type,
                        'direction' => $t->direction,
                        'pdf_url' => $t->pdf_url ?? null,
                        'date' => $t->created_at->format('Y-m-d'),
                    ];
                }),
            ];
        });

        return response()->json([
            'message' => $locale === 'ar'
                ? 'تم جلب خطط التبرع الدوري'
                : 'Recurring donation plans retrieved',
            'data' => $data
        ]);
    }

    // جلب خطط التبرع الدوري للأدمن
    public function getRecurringPlansDonors()
    {
        $locale = app()->getLocale();
        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $plans = Plan::whereNull('sponsorship_id') // فقط التبرع العام
        ->with('user:id,name,email,phone') // جلب معلومات اليوزر
        ->latest('created_at')
            ->get();

        return response()->json([
            'message' => $locale === 'ar' ? 'تم جلب خطط التبرع الدوري ' : 'Recurring Periodic donation plans retrieved',
            'data' => $plans->map(function ($plan) {
                return [
                    'id' => $plan->id,
                    'user' => $plan->user,
                    'amount' => $plan->amount,
                    'recurrence' => $plan->recurrence,
                    'is_activated' => $plan->is_activated,
                    'start_date' => $plan->start_date,
                    'end_date' => $plan->end_date,
                ];
            })
        ]);
    }
    //test

    public function checkPlanDates($planId)
    {
        $plan = \App\Models\Plan::find($planId);

        if (!$plan) {
            return response()->json([
                'message' => 'Plan not found',
            ], 404);
        }

        $now = now()->format('Y-m-d H:i:s');
        $endDate = $plan->end_date->format('Y-m-d H:i:s');

        return response()->json([
            'now' => $now,
            'end_date' => $endDate,
            'is_now_gte_end_date' => now()->gte($plan->end_date),
        ]);
    }

}

