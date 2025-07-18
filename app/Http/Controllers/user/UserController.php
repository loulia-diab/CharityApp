<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function showProfile()
    {
        $user = auth()->guard('api')->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $data = [
            'name' => $user->name,
            'phone' => $user->phone,
            'profile_image' => $user->profile_image ? asset('storage/' . $user->profile_image) : null,
        ];
        return response()->json([
            'message' => 'User profile',
            'data' => $data,
        ], 200);
    }


    public function updateProfile(Request $request)
    {
        $user = auth()->guard('api')->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // التحقق من البيانات المُرسلة
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'profile_image' => 'sometimes|image|max:2048',
        ]);

        // تحديث الصورة إن وُجدت
        if ($request->hasFile('profile_image')) {
            // حذف الصورة القديمة
            if ($user->profile_image) {
                $relativePath = $user->profile_image;
                if (Storage::disk('public')->exists($relativePath)) {
                    Storage::disk('public')->delete($relativePath);
                    \Log::info("Deleted image successfully: {$relativePath}");
                } else {
                    \Log::warning("Image does not exist: {$relativePath}");
                }
            }
            // تخزين الصورة الجديدة
            $path = $request->file('profile_image')->store('profile_images', 'public');
            $validated['profile_image'] = $path;
        }

        // تحديث البيانات الشخصية
        $updated = $user->update([
            'name' => $validated['name'] ?? $user->name,
            'profile_image' => $validated['profile_image'] ?? $user->profile_image,
        ]);

        if (!$updated) {
            return response()->json([
                'message' => 'Failed to update profile',
            ], 500);
        }

        // تحديث البيانات المرسلة
        $user->refresh();
        $data = [
            'name' => $user->name,
            'profile_image' => $user->profile_image,

        ];

        return response()->json([
            'message' => 'Profile updated successfully',
            'data' => $data,
        ], 200);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->guard('api')->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'كلمة المرور الحالية غير صحيحة'
            ], 403);
        }

        if ($request->current_password === $request->new_password) {
            return response()->json([
                'message' => 'كلمة المرور الجديدة يجب أن تكون مختلفة عن الحالية'
            ], 422);
        }

        $user->update([
            'password' => bcrypt($request->new_password),
        ]);

        return response()->json([
            'message' => 'تم تغيير كلمة المرور بنجاح'
        ]);
    }

}
