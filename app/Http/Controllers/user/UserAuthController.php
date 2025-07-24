<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserAuthController extends Controller
{
    public function register(Request $request)
    {
        $otp = '123456'; // كود التحقق الثابت

        try {
            $request->validate([
                'name'     => 'required|string|max:255',
                'phone'    => 'required|string|unique:users,phone|digits:10',
                'password' => 'required|string|min:8|confirmed',
                'preferred_language' => 'required|in:en,ar',
                'otp' => 'required|string',
            ]);

            if ($request->otp !== $otp) {
                return response()->json(['message' => 'رمز التحقق غير صحيح'], 422);
            }

            $user = User::create([
                'name'     => $request->name,
                'phone'    => $request->phone,
                'password' => Hash::make($request->password),
                'preferred_language' => $request->preferred_language,
                'phone_verified_at' => now(),
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message'      => 'User registered successfully',
                'access_token' => $token,
                'user'         => $user,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors'  => $e->errors(),
            ], 422);
        }
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'phone'    => 'required|string|digits:10',
                'password' => 'required|string',
            ]);

            $user = User::where('phone', $request->phone)->firstOrFail();

            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'message' => 'Invalid phone or password'
                ], 401);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'User logged in successfully',
                'token'   => $token,
                'user'    => $user
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors'  => $e->errors(),
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Invalid phone or password'
            ], 401);
        }
    }

    public function logout(Request $request)
    {
        $user = auth()->guard('api')->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message'=>'user logged out successfully'
        ],200);
    }

    public function resetPassword(Request $request)
    {
        $otp = '123456';

        $request->validate([
            'phone' => 'required|string|exists:users,phone',
            'otp' => 'required|string',
            'new_password' => 'required|string|confirmed|min:8',
        ]);

        if ($request->otp !== $otp) {
            return response()->json(['message' => 'رمز التحقق غير صحيح'], 422);
        }

        $user = User::where('phone', $request->phone)->first();
        $user->password = Hash::make($request->new_password);
        $user->save();

        // تسجيل الدخول مباشرة بعد تغيير كلمة السر
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'تم تغيير كلمة المرور وتسجيل الدخول بنجاح',
            'token' => $token,
            'user' => $user,
        ]);
    }

}
