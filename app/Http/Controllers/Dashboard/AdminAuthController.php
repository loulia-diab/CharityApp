<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AdminAuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email'    => 'required|email',
                'password' => 'required',
            ]);

            $admin = Admin::where('email', $request->email)->firstOrFail();

            if (!Hash::check($request->password, $admin->password)) {
                return response()->json([
                    'message' => 'Invalid email or password'
                ], 401);
            }

            // $token = $admin->createToken('admin-token', ['*'], 'admin')->plainTextToken;

            $token = $admin->createToken('admin_token')->plainTextToken;

            return response()->json([
                'message' => 'Admin logged in successfully',
                'token'   => $token,
                'admin'    => $admin
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors'  => $e->errors(),
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Invalid email or password'
            ], 401);
        }
    }

    public function logout(Request $request)
    {
        $admin = auth('admin')->user();

        if ($admin) {
            $admin->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Admin logged out successfully'
            ], 200);
        }

        return response()->json([
            'message' => 'Unauthenticated'
        ], 401);
    }

    public function profile(Request $request)
    {
        // تأكد من استخدام guard admin صراحة إذا لزم الأمر:
        $admin = auth('admin')->user();

        return response()->json($admin);
    }
/*
    public function setAdminLanguage(Request $request)
    {
        // التحقق من المدخلات
        $validated = $request->validate([
            'preferred_language' => 'required|string|in:en,ar', // en أو ar فقط
        ]);

        // الحصول على الإدمن من الـ guard المناسب
        $admin = auth('admin')->user();

        if (!$admin) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $preferredLanguage = $validated['preferred_language'];

        // تحديث اللغة في قاعدة البيانات
        $admin->update([
            'preferred_language' => $preferredLanguage,
        ]);

        // تعيين اللغة للتطبيق الحالي
        app()->setLocale($preferredLanguage);

        return response()->json([
            'message' => 'Language set successfully',
            'preferred_language' => $preferredLanguage,
        ], 200);
    }*/

    /*
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message'=>'admin logged out successfully'
        ],200);
    }
   */

    /*
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();

            return response()->json([
                'message' => 'تم تسجيل الدخول بنجاح',
                'admin'   => Auth::guard('admin')->user(),
            ], 200);
        }

        return response()->json([
            'message' => 'بيانات الدخول غير صحيحة',
        ], 401);
    }


    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'تم تسجيل الخروج بنجاح',
        ], 200);
    }
*/
}
