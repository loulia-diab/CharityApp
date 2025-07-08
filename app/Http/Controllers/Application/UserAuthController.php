<?php

namespace App\Http\Controllers\Application;

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
        try {
            $request->validate([
                'name'     => 'required|string|max:255',
                'phone'    => 'required|string|unique:users,phone|digits:10',
                'password' => 'required|string|min:8|confirmed',
            ]);

            $user = User::create([
                'name'     => $request->name,
                'phone'    => $request->phone,
                'password' => Hash::make($request->password),
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message'      => 'User registered successfully',
                'access_token' => $token,
                'token_type'   => 'Bearer',
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
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message'=>'user logged out successfully'
        ],200);
    }
/*
    public function setUserLanguage(Request $request)
    {
        // التحقق من المدخلات
        $validated = $request->validate([
            'preferred_language' => 'required|string|in:en,ar', // تحديد أن اللغة يجب أن تكون واحدة من 'en' أو 'ar'
        ]);
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorised'], 401);
        }
        $preferredLanguage = $validated['preferred_language'];

        $user->update([
            'preferred_language' => $preferredLanguage,
        ]);
        app()->setLocale($preferredLanguage);
        return response()->json([
            'message' => 'Language set successfully',
            'preferred_language' => $preferredLanguage,
        ], 200);
    }*/

    /*
        public function changeLanguage(Request $request)
        {
            // التحقق من المدخلات
            $validated = $request->validate([
                'preferred_language' => 'required|string|in:en,ar',
            ]);
            $user = Auth::user();
            if (!$user) {
                return response()->json(['message' => 'Unauthorised'], 401);
            }
            $preferredLanguage = $validated['preferred_language'];

            $user->update([
                'preferred_language' => $preferredLanguage,
            ]);
            app()->setLocale($preferredLanguage);

            return response()->json([
                'message' => 'Language changed successfully',
                'preferred_language' => $preferredLanguage,
            ], 200);
        }
    */
}
