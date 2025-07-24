<?php

namespace App\Http\Controllers;

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

}
