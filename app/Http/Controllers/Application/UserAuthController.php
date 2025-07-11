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
                'preferred_language' => 'required|in:en,ar',
            ]);

            $user = User::create([
                'name'     => $request->name,
                'phone'    => $request->phone,
                'password' => Hash::make($request->password),
                'preferred_language' => $request->preferred_language,
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

}
