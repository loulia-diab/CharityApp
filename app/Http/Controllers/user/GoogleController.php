<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function loginWithGoogle(Request $request)
    {
        $request->validate([
            'access_token' => 'required|string',
            'preferred_language' => 'required|in:en,ar',
        ]);

        try {
            $googleUser = Socialite::driver('google')->stateless()->userFromToken($request->access_token);

            $user = User::where('google_id', $googleUser->id)->first();

            if ($user) {
                Auth::login($user);

                $token = $user->createToken('auth_token')->plainTextToken;

                return response()->json([
                    'message' => 'User logged in with Google successfully',
                    'access_token' => $token,
                    'user' => $user,
                ]);

            } else {
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'password' => encrypt('123456dummy'), // لن يُستخدم فعليًا
                    'preferred_language' => $request->preferred_language,
                ]);
                Auth::login($user);

                $token = $user->createToken('auth_token')->plainTextToken;

                return response()->json([
                    'message' => 'User registered with Google successfully',
                    'access_token' => $token,
                    'user' => $user,
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Invalid Google token or authentication failed',
                'details' => $e->getMessage(),
            ], 401);
        }
    }


    /*
    public function googleLogin()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
       // $locale = app()->getLocale();
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $user = User::where('google_id', $googleUser->id)->first();

            if ($user) {
                Auth::login($user);
                $token = $user->createToken('auth_token')->plainTextToken;
                return response()->json([
                    'message'      => 'User logged in successfully',
                    'access_token' => $token,
                    'user'         => $user,
                ], 201);

            } else {
                $newUser = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'password' => encrypt('123456dummy'),
                    //'preferred_language'=> $locale ,
                ]);
                Auth::login($newUser);
                $token = $newUser->createToken('auth_token')->plainTextToken;
                return response()->json([
                    'message'      => 'User registered successfully',
                    'access_token' => $token,
                    'user'         => $newUser,
                ], 201);

            }
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }
*/
}
