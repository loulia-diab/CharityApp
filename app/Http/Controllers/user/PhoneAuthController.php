<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\PhoneOtp;
use App\Models\User;
use App\Services\TwilioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PhoneAuthController extends Controller
{
    public function sendOtp(Request $request, TwilioService $twilio)
    {
        $request->validate(['phone' => 'required|string']);

        // التحقق من عدد المحاولات
        $count = PhoneOtp::where('phone', $request->phone)
            ->where('created_at', '>', now()->subMinutes(5))
            ->count();

        if ($count >= 3) {
            return response()->json(['message' => 'محاولات كثيرة، حاول لاحقًا'], 429);
        }

        // إنشاء رمز عشوائي
        $otp = rand(100000, 999999);

        // حفظه في الداتا بيز
        PhoneOtp::create([
            'phone' => $request->phone,
            'otp' => $otp,
            'expires_at' => now()->addMinutes(5),
        ]);

        // إرساله عبر SMS
        $twilio->sendSms($request->phone, "رمز التحقق هو: $otp");

        return response()->json(['message' => 'تم إرسال الرمز']);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'otp' => 'required|string',
        ]);

        $otpRecord = PhoneOtp::where('phone', $request->phone)
            ->where('otp', $request->otp)
            ->where('expires_at', '>', now())
            ->whereNull('used_at')
            ->latest()
            ->first();

        if (!$otpRecord) {
            return response()->json(['message' => 'رمز غير صالح أو منتهي'], 422);
        }

        // تسجيل الاستخدام
        $otpRecord->update([
            'used_at' => now(),
        ]);

        // تسجيل الدخول أو إنشاء مستخدم
        $user = User::firstOrCreate(['phone' => $request->phone]);

        Auth::login($user);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'تم التحقق وتسجيل الدخول',
            'token' => $token,
            'user' => $user,
        ]);
    }

}
