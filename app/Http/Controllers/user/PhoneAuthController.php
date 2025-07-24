<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\PhoneOtp;
use App\Models\User;
use App\Services\TwilioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PhoneAuthController extends Controller
{
    // ✅ إرسال رمز لتسجيل الدخول
    public function sendLoginOtp(Request $request, TwilioService $twilio)
    {
        $request->validate(['phone' => 'required|string']);

        if ($this->isRateLimited($request->phone)) {
            return response()->json(['message' => 'محاولات كثيرة، حاول لاحقًا'], 429);
        }

        $otp = rand(100000, 999999);

        PhoneOtp::create([
            'phone' => $request->phone,
            'otp' => $otp,
            'expires_at' => now()->addMinutes(5),
            'purpose' => 'login',
        ]);

        $twilio->sendSms($request->phone, "رمز الدخول: $otp");

        return response()->json(['message' => 'تم إرسال رمز الدخول']);
    }

    // ✅ إرسال رمز لإعادة تعيين كلمة المرور
    public function sendPasswordResetOtp(Request $request, TwilioService $twilio)
    {
        $request->validate(['phone' => 'required|string']);

        if (!User::where('phone', $request->phone)->exists()) {
            return response()->json(['message' => 'رقم الهاتف غير مسجل'], 404);
        }

        if ($this->isRateLimited($request->phone)) {
            return response()->json(['message' => 'محاولات كثيرة، حاول لاحقًا'], 429);
        }

        $otp = rand(100000, 999999);

        PhoneOtp::create([
            'phone' => $request->phone,
            'otp' => $otp,
            'expires_at' => now()->addMinutes(5),
            'purpose' => 'reset_password',
        ]);

        $twilio->sendSms($request->phone, "رمز استعادة كلمة المرور: $otp");

        return response()->json(['message' => 'تم إرسال رمز الاستعادة']);
    }

    // ✅ التحقق من OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'otp' => 'required|string',
            'purpose' => 'required|string|in:login,reset_password'
        ]);

        $otpRecord = PhoneOtp::where('phone', $request->phone)
            ->where('otp', $request->otp)
            ->where('purpose', $request->purpose)
            ->where('expires_at', '>', now())
            ->whereNull('used_at')
            ->latest()
            ->first();

        if (!$otpRecord) {
            return response()->json(['message' => 'رمز غير صحيح أو منتهي'], 422);
        }

        $otpRecord->update(['used_at' => now()]);

        if ($request->purpose === 'login') {
            $user = User::firstOrCreate(['phone' => $request->phone]);
            Auth::login($user);
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'تم تسجيل الدخول',
                'token' => $token,
                'user' => $user,
            ]);
        }

        return response()->json(['message' => 'تم التحقق بنجاح']);
    }

    // ✅ إعادة تعيين كلمة المرور
    public function resetPassword(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'otp' => 'required|string',
            'password' => 'required|string|min:6|confirmed'
        ]);

        $otpRecord = PhoneOtp::where('phone', $request->phone)
            ->where('otp', $request->otp)
            ->where('purpose', 'reset_password')
            ->where('expires_at', '>', now())
            ->whereNull('used_at')
            ->latest()
            ->first();

        if (!$otpRecord) {
            return response()->json(['message' => 'رمز غير صحيح أو منتهي'], 422);
        }

        $user = User::where('phone', $request->phone)->firstOrFail();
        $user->password = Hash::make($request->password);
        $user->save();

        $otpRecord->update(['used_at' => now()]);

        return response()->json(['message' => 'تم تغيير كلمة السر بنجاح']);
    }

    // ✅ حماية من الإرسال المتكرر خلال 5 دقائق
    private function isRateLimited($phone)
    {
        $count = PhoneOtp::where('phone', $phone)
            ->where('created_at', '>', now()->subMinutes(5))
            ->count();

        return $count >= 3;
    }

}
