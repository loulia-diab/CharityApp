<?php

namespace App\Http\Controllers;

use App\Models\Box;
use App\Models\Campaigns\Campaign;
use App\Models\Gift;
use App\Models\Transaction;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller
{
    public function rechargeUserBalance(Request $request)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:1',
        ]);

        try {
            return DB::transaction(function () use ($admin, $request) {
                $user = User::findOrFail($request->user_id);

                $user->increment('balance', $request->amount);

                $transaction = Transaction::create([
                    'user_id' => $user->id,
                    'admin_id' => $admin->id,
                    'type' => 'recharge',
                    'direction' => 'in',
                    'amount' => $request->amount,
                ]);

                return response()->json([
                    'message' => 'تم شحن الرصيد بنجاح.',
                    'transaction' => $transaction,
                ]);
            });

        } catch (Exception $e) {
            return response()->json([
                'message' => 'حدث خطأ أثناء شحن الرصيد.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function donate(Request $request)
    {
        try {
            $user = auth('api')->user();

            if (!$user) {
                return response()->json(['message' => 'غير مصرح'], 401);
            }

            $validated = $request->validate([
                'donations' => 'required|array|min:1',
                'donations.*.amount' => 'required|numeric|min:1',
                'donations.*.campaign_id' => 'nullable|exists:campaigns,id',
                'donations.*.box_id' => 'nullable|exists:boxes,id',
            ]);

            $donations = collect($validated['donations']);

            // تأكد أن كل تبرع يذهب إما لحملة أو صندوق فقط
            foreach ($donations as $donation) {
                if (empty($donation['campaign_id']) && empty($donation['box_id'])) {
                    return response()->json([
                        'message' => 'يجب تحديد حملة أو صندوق لكل تبرع.'
                    ], 422);
                }

                if (!empty($donation['campaign_id']) && !empty($donation['box_id'])) {
                    return response()->json([
                        'message' => 'لا يمكن تحديد حملة وصندوق في نفس التبرع.'
                    ], 422);
                }
            }

            $totalAmount = $donations->sum('amount');

            if ($user->balance < $totalAmount) {
                return response()->json(['message' => 'الرصيد غير كافٍ لإتمام كل التبرعات.'], 422);
            }

            DB::transaction(function () use ($user, $donations) {
                // خصم الرصيد الإجمالي
                $user->decrement('balance', $donations->sum('amount'));

                foreach ($donations as $donation) {
                    $boxId = $donation['box_id'] ?? null;
                    $campaignId = $donation['campaign_id'] ?? null;
                    $amount = $donation['amount'];

                    // إنشاء العملية
                    $transaction = Transaction::create([
                        'user_id' => $user->id,
                        'admin_id' => null,
                        'campaign_id' => $campaignId,
                        'box_id' => $boxId,
                        'type' => 'donation',
                        'direction' => 'in',
                        'amount' => $amount,
                    ]);

                    // تحديث الجهة المستفيدة
                    if ($boxId) {
                        $box = Box::find($boxId);
                        $box->increment('balance', $amount);
                    }

                    if ($campaignId) {
                        $campaign = Campaign::find($campaignId);
                        $campaign->increment('collected_amount', $amount);
                    }
                }
            });

            return response()->json([
                'message' => 'تم تنفيذ جميع التبرعات بنجاح.'
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'البيانات غير صحيحة',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'حدث خطأ أثناء تنفيذ التبرعات.',
                'error' => $e->getMessage(), // احذفه في الإنتاج
            ], 500);
        }
    }

    public function spend(Request $request)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'amount' => 'required|numeric|min:1',
            'campaign_id' => 'nullable|exists:campaigns,id',
            'box_id' => 'nullable|exists:boxes,id',
        ]);

        if (!$request->campaign_id && !$request->box_id) {
            return response()->json([
                'message' => 'يجب تحديد حملة أو صندوق للصرف'
            ], 422);
        }

        return DB::transaction(function () use ($admin, $request) {
            $transactionData = [
                'admin_id'   => $admin->id,
                'type'       => 'exchange',
                'direction'  => 'out',
                'amount'     => $request->amount,
                'campaign_id'=> $request->campaign_id,
                'box_id'     => $request->box_id,
            ];

            // إنشاء عملية الصرف
            $transaction = Transaction::create($transactionData);

            // إذا كان الصرف من صندوق → تعديل الرصيد
            if ($request->box_id) {
                $box = Box::findOrFail($request->box_id);

                if ($box->balance < $request->amount) {
                    throw new \Exception('الرصيد المتاح في الصندوق غير كافٍ');
                }

                $box->decrement('balance', $request->amount);
            }

            return response()->json([
                'message'     => 'تم تسجيل عملية الصرف بنجاح',
                'transaction' => $transaction,
            ]);
        });
    }

    public function getAllExchanges()
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin) {
            abort(403, 'Unauthorized');
        }

        // جلب كل عمليات الصرف
        $exchanges = Transaction::where('type', 'exchange')
            ->where('direction', 'out')
            ->with(['box:id,name_ar,name_en', 'campaign:id,title_ar,title_en'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($transaction) {
                $locale = app()->getLocale();

                $target = null;
                $target_id = null;

                if ($transaction->box_id) {
                    $target_id = $transaction->box->id;
                    $target = $locale === 'ar' ? $transaction->box->name_ar : $transaction->box->name_en;
                } elseif ($transaction->campaign_id) {
                    $target_id = $transaction->campaign->id;
                    $target = $locale === 'ar' ? $transaction->campaign->title_ar : $transaction->campaign->title_en;
                }

                return [
                    'id'         => $transaction->id,
                    'target'     => $target,
                    'target_id'  => $target_id,
                    'amount'     => $transaction->amount,
                    'spent_at'   => $transaction->created_at->toDateTimeString(),
                ];
            });

        return response()->json([
            'exchanges' => $exchanges,
        ]);
    }

    public function getAllDonations()
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin) {
            abort(403, 'Unauthorized');
        }

        $donations = Transaction::where('type', 'donation')
            ->where('direction', 'in')
            ->with([
                'user:id,name',
                'box:id,name_ar,name_en',
                'campaign:id,title_ar,title_en'
            ])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($transaction) {
                $locale = app()->getLocale();

                $target_id = null;
                $target = null;

                if ($transaction->box_id) {
                    $target_id = $transaction->box->id;
                    $target = $locale === 'ar' ? $transaction->box->name_ar : $transaction->box->name_en;
                } elseif ($transaction->campaign_id) {
                    $target_id = $transaction->campaign->id;
                    $target = $locale === 'ar' ? $transaction->campaign->title_ar : $transaction->campaign->title_en;
                }

                return [
                    'donation_id' => $transaction->id,
                    'user_id'     => $transaction->user->id ?? null,
                    'user_name'   => $transaction->user->name ?? null,
                    'target'      => $target,
                    'target_id'   => $target_id,
                    'amount'      => $transaction->amount,
                    'donated_at'  => $transaction->created_at->toDateTimeString(),
                ];
            });

        return response()->json([
            'donations' => $donations,
        ]);
    }

    public function getAllDonors()
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin) {
            abort(403, 'Unauthorized');
        }

        $donations = Transaction::where('type', 'donation')
            ->where('direction', 'in')
            ->with([
                'user:id,name,email,phone',
                'box:id,name_ar,name_en',
                'campaign:id,title_ar,title_en'
            ])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($transaction) {
                $locale = app()->getLocale();

                // تحديد الجهة المتبرع لها (صندوق أو حملة)
                $target = null;
                if ($transaction->box_id) {
                    $target = $locale === 'ar' ? $transaction->box->name_ar : $transaction->box->name_en;
                } elseif ($transaction->campaign_id) {
                    $target = $locale === 'ar' ? $transaction->campaign->title_ar : $transaction->campaign->title_en;
                }

                return [
                    'user_id'    => $transaction->user->id ?? null,
                    'user_name'  => $transaction->user->name ?? null,
                    'contact'    => $transaction->user->email ?? $transaction->user->phone, // واحد بس
                    'target'     => $target,
                    'amount'     => $transaction->amount,
                    'donated_at' => $transaction->created_at->toDateTimeString(),
                ];
            });

        return response()->json([
            'donors' => $donations,
        ]);
    }

    public function getCampaignDonors($campaign_id)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin) {
            abort(403, 'Unauthorized');
        }

        // التحقق من وجود الحملة
        if (!Campaign::where('id', $campaign_id)->exists()) {
            return response()->json([
                'message' => 'الحملة غير موجودة'
            ], 404);
        }


        $donations = Transaction::where('campaign_id', $campaign_id)
            ->where('type', 'donation')
            ->where('direction', 'in')
            ->with('user:id,name,email,phone') // جلب بيانات المستخدم فقط المطلوبة
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($transaction) {
                return [
                    'id'         => $transaction->user->id ?? null,
                    'name'       => $transaction->user->name ?? null,
                    'contact'    => $transaction->user->email ?? $transaction->user->phone,
                    'amount'     => $transaction->amount,
                    'donated_at' => $transaction->created_at->toDateTimeString() ,
                ];
            });

        return response()->json([
            'donors'      => $donations,
        ]);
    }

}
