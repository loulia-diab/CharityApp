<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $admin = auth()->guard('admin')->user();

        if (!$admin) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if (!Hash::check($request->current_password, $admin->password)) {
            return response()->json([
                'message' => 'كلمة المرور الحالية غير صحيحة'
            ], 403);
        }

        if ($request->current_password === $request->new_password) {
            return response()->json([
                'message' => 'كلمة المرور الجديدة يجب أن تكون مختلفة عن الحالية'
            ], 422);
        }

        $admin->update([
            'password' => bcrypt($request->new_password),
        ]);

        return response()->json([
            'message' => 'تم تغيير كلمة المرور بنجاح'
        ]);
    }

}
