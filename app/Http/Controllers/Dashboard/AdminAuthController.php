<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Mail\SendCodeResetPassword;
use App\Models\Admin;
use App\Models\ResetPasswordForAdmin;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
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

    public function forgotPassword(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|exists:admins',
        ]);

        // Delete all old code that the user sent before.
        ResetPasswordForAdmin::query()->where('email', $request->email)->delete();

        // Generate random code
        $data['code'] = mt_rand(100000, 999999);

        // Create a new code
        $codeData = ResetPasswordForAdmin::query()->create($data);

        // Send email to user
        Mail::to($request->email)->send(new SendCodeResetPassword($codeData->code));

        return response()->json([
            'message' => trans('code.sent'),
            'code'=>$data['code']
        ],200);
    }

    public function checkCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string|exists:reset_password_for_admins',
        ]);

        // find the code
        $passwordReset = ResetPasswordForAdmin::query()-> firstWhere('code', $request->code);

        //Check if it has not expired: the time is one hour
        if ($passwordReset->created_at > now()->addHour()) {
            $passwordReset->delete();
            return response()->json(['message' => trans('passwords.code_is_expire')], 422);
        }

        return response([
            'code' => $passwordReset->code,
            'message' => trans('code_is_valid')
        ], 200);
    }

    public function resetPassword(Request $request)
    {
        $input= $request->validate([
            'code' => 'required|string|exists:reset_password_for_admins',
            'password' => ['required','string','confirmed',]
        ]);

        // find the code
        $passwordReset = ResetPasswordForAdmin::query()->firstWhere('code', $request['code']);

        //Check if it has not expired: the time is one hour
        if ($passwordReset['created_at'] > now()->addHour()) {
            $passwordReset->delete();
            return response(['message' => trans('passwords.code_is_expire')], 422);
        }

        // find user's email
        $admin = Admin::query()->firstWhere('email', $passwordReset['email']);

        // update user password
        $input['password']=bcrypt( $input['password']);
        $admin->update(['password' => $input['password']]);

        // delete current code
        $passwordReset->delete();

        return response([
            'message' =>'password has been successfully reset',
        ], 200);
    }
}
