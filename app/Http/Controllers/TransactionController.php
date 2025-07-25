<?php

namespace App\Http\Controllers;

use App\Models\Box;
use App\Models\Gift;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            'amount'  => 'required|numeric|min:1',
        ]);

        return DB::transaction(function () use ($admin, $request) {
            $user = User::findOrFail($request->user_id);

            $user->increment('balance', $request->amount);

            $transaction = Transaction::create([
                'user_id'   => $user->id,
                'admin_id'  => $admin->id,
                'type'      => 'recharge',
                'direction' => 'in',
                'amount'    => $request->amount,
            ]);

            return response()->json([
                'message'     => 'تم شحن الرصيد بنجاح.',
                'transaction' => $transaction,
            ]);
        });
    }


    public function donateAsGift(Request $request)
    {
        $user = auth('user')->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'amount'          => 'required|numeric|min:1',
            'recipient_name'  => 'required|string|max:255',
            'recipient_phone' => 'required|string|max:20',
            'message'         => 'nullable|string',
            'is_hide'         => 'nullable|boolean',
        ]);

        if ($user->balance < $validated['amount']) {
            return response()->json(['message' => 'الرصيد غير كافٍ لإتمام التبرع.'], 422);
        }

        $boxId = 8;
        $box = Box::findOrFail($boxId); // تأكد أن الصندوق موجود

        return DB::transaction(function () use ($user, $validated, $box) {
            // 1. خصم الرصيد من المستخدم
            $user->decrement('balance', $validated['amount']);

            // 2. إضافة الرصيد إلى الصندوق
            $box->increment('balance', $validated['amount']);

            // 3. إنشاء عملية التبرع
            $transaction = Transaction::create([
                'user_id'   => $user->id,
                'admin_id'  => null,
                'campaign_id' => null,
                'box_id'    => $box->id,
                'type'      => 'donation',
                'direction' => 'out',
                'amount'    => $validated['amount'],
                'pdf_url'   => null,
            ]);

            // 4. إنشاء الهدية المرتبطة بالعملية
            $gift = Gift::create([
                'user_id'         => $user->id,
                'transaction_id'  => $transaction->id,
                'recipient_name'  => $validated['recipient_name'],
                'recipient_phone' => $validated['recipient_phone'],
                'is_hide'         => $validated['is_hide'] ?? false,
                'message'         => $validated['message'] ?? '',
            ]);

            return response()->json([
                'message' => 'تم التبرع كهدية بنجاح.',
                'gift'    => $gift,
            ], 201);
        });
    }


}
