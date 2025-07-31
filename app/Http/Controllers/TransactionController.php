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

}
