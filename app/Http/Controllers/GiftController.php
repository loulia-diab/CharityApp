<?php

namespace App\Http\Controllers;

use App\Models\Box;
use App\Models\Gift;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class GiftController extends Controller
{
    /*
    public function donateAsGift(Request $request)
    {
        try {
            $user = auth('api')->user();

            if (!$user) {
                return response()->json(['message' => 'غير مصرح'], 401);
            }

            $validated = $request->validate([
                'amount' => 'required|numeric|min:1',
                'recipient_name' => 'required|string|max:255',
                'recipient_phone' => 'required|string|max:20',
                'message' => 'nullable|string',
                'is_hide' => 'nullable|boolean',
            ]);

            if ($user->balance < $validated['amount']) {
                return response()->json(['message' => 'الرصيد غير كافٍ لإتمام التبرع.'], 422);
            }

            $boxId = 8;
            $box = Box::find($boxId);

            if (!$box) {
                return response()->json(['message' => 'الصندوق غير موجود.'], 404);
            }

            return DB::transaction(function () use ($user, $validated, $box) {
                // خصم الرصيد من المستخدم
                $user->decrement('balance', $validated['amount']);

                // إضافة الرصيد إلى الصندوق
                $box->increment('balance', $validated['amount']);

                // إنشاء عملية التبرع
                $transaction = Transaction::create([
                    'user_id' => $user->id,
                    'admin_id' => null,
                    'campaign_id' => null,
                    'box_id' => $box->id,
                    'type' => 'donation',
                    'direction' => 'in',
                    'amount' => $validated['amount'],
                ]);

                // إنشاء الهدية المرتبطة بالعملية
                $gift = Gift::create([
                    'user_id' => $user->id,
                    'transaction_id' => $transaction->id,
                    'recipient_name' => $validated['recipient_name'],
                    'recipient_phone' => $validated['recipient_phone'],
                    'is_hide' => $validated['is_hide'] ?? false,
                    'message' => $validated['message'] ?? null,
                ]);

                return response()->json([
                    'message' => 'تم التبرع كهدية بنجاح.',
                    'gift' => $gift,
                ], 201);
            });

        } catch (ValidationException $e) {
            // أخطاء التحقق من البيانات
            return response()->json([
                'message' => 'البيانات غير صحيحة',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            // أخطاء غير متوقعة
            Log::error('حدث خطأ أثناء إنشاء التبرع كهدية: ' . $e->getMessage());

            return response()->json([
                'message' => 'حدث خطأ أثناء معالجة الطلب.',
                'error' => $e->getMessage(), // احذف هذا في بيئة الإنتاج
            ], 500);
        }
    }
*/
    public function donateAsGift(Request $request)
    {
        try {
            $user = auth('api')->user();

            if (!$user) {
                return response()->json(['message' => 'غير مصرح'], 401);
            }

            $validated = $request->validate([
                'amount' => 'required|numeric|min:1',
                'recipient_name' => 'required|string|max:255',
                'recipient_phone' => 'required|string|max:20',
                'message' => 'nullable|string',
                'is_hide' => 'nullable|boolean',
            ]);

            if ($user->balance < $validated['amount']) {
                return response()->json(['message' => 'الرصيد غير كافٍ لإتمام التبرع.'], 422);
            }

            $boxId = 8;
            $box = Box::find($boxId);

            if (!$box) {
                return response()->json(['message' => 'الصندوق غير موجود.'], 404);
            }

            return DB::transaction(function () use ($user, $validated, $box) {
                // خصم الرصيد من المستخدم
                $user->decrement('balance', $validated['amount']);

                // إضافة الرصيد إلى الصندوق
                $box->increment('balance', $validated['amount']);

                // إنشاء عملية التبرع
                $transaction = Transaction::create([
                    'user_id' => $user->id,
                    'admin_id' => null,
                    'campaign_id' => null,
                    'box_id' => $box->id,
                    'type' => 'donation',
                    'direction' => 'in',
                    'amount' => $validated['amount'],
                ]);

                // إنشاء الهدية المرتبطة بالعملية
                $gift = Gift::create([
                    'user_id' => $user->id,
                    'transaction_id' => $transaction->id,
                    'recipient_name' => $validated['recipient_name'],
                    'recipient_phone' => $validated['recipient_phone'],
                    'is_hide' => $validated['is_hide'] ?? false,
                    'message' => $validated['message'] ?? null,
                ]);
                // 🔔 إرسال إشعار للمستخدم عن التبرع كهدية
                $notificationService = app()->make(\App\Services\NotificationService::class);

                $title = [
                    'en' => "Gift Donation Sent",
                    'ar' => "تم إرسال التبرع كهدية",
                ];

                $body = [
                    'en' => "Thank you for donating as a gift . Your generosity spreads kindness.",
                    'ar' => "شكراً لك على تبرعك كهدية ، كرمك ينشر الخير والمحبة.",
                ];

                $notificationService->sendFcmNotification(new \Illuminate\Http\Request([
                    'user_id'  => $user->id,
                    'title_en' => $title['en'],
                    'title_ar' => $title['ar'],
                    'body_en'  => $body['en'],
                    'body_ar'  => $body['ar'],
                ]));

                return response()->json([
                    'message' => 'تم التبرع كهدية بنجاح.',
                    'gift' => $gift,
                ], 201);
            });

        } catch (ValidationException $e) {
            // أخطاء التحقق من البيانات
            return response()->json([
                'message' => 'البيانات غير صحيحة',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            // أخطاء غير متوقعة
            Log::error('حدث خطأ أثناء إنشاء التبرع كهدية: ' . $e->getMessage());

            return response()->json([
                'message' => 'حدث خطأ أثناء معالجة الطلب.',
                'error' => $e->getMessage(), // احذف هذا في بيئة الإنتاج
            ], 500);
        }
    }
    public function getMyGiftDonations(Request $request)
    {
        $user = auth('api')->user();

        if (!$user) {
            return response()->json(['message' => 'غير مصرح'], 401);
        }

        $gifts = Gift::with('transaction')
            ->where('user_id', $user->id)
            ->latest()
            ->get()
            ->map(function ($gift) {
                return [
                    'recipient_name' => $gift->recipient_name,
                    'amount'         => $gift->transaction->amount ?? 0,
                    'donated_at'     => $gift->transaction->created_at ? $gift->transaction->created_at->toDateTimeString() : null,
                ];
            });

        return response()->json([
            'gifts' => $gifts
        ]);
    }

}
