<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\Gift;


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
            'email' => $user->email,
            'balance' => $user->balance,
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

    public function getAllUsers(Request $request)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $users = User::select('id', 'name', 'balance', 'email', 'phone')
            ->get()
            ->map(function ($user) {
                return [
                    'id'      => $user->id,
                    'name'    => $user->name,
                    'contact' => $user->email ?? $user->phone,
                    'balance' => $user->balance,
                ];
            });

        return response()->json([
            'message' => 'قائمة المستخدمين',
            'users'   => $users,
        ]);
    }

    public function getMyRecharges(Request $request)
    {
        $user = auth('api')->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $recharges = $user->recharges()
            ->select('amount', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($trx) {
                return [
                    'amount' => $trx->amount,
                    'date'   => $trx->created_at->toDateTimeString(),
                ];
            });

        return response()->json([
            'message' => 'سجل عمليات الشحن',
            'data'    => $recharges,
        ]);
    }

    public function getMyDonations(Request $request)
    {
        $user = auth()->guard('api')->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $locale = app()->getLocale();

        try {
            $donations = Transaction::query()
                ->where('user_id', $user->id)
                ->where('type', 'donation')
                ->where('direction', 'in')
                ->where(function ($query) {
                    $query->where('box_id', '!=', 14)
                        ->orWhereNull('box_id');
                })
                ->whereDoesntHave('campaign.category', function ($query) {
                    $query->whereIn('main_category', ['Sponsorship', 'InKind']);
                })
                ->with([
                    'campaign.category',
                    'box'
                ])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($transaction) use ($locale) {
                    if ($transaction->campaign) {
                        return [
                            'title'       => $transaction->campaign->{'title_' . $locale},
                            'image'       => $transaction->campaign->image,
                            'amount'      => $transaction->amount,
                            'date'        => $transaction->created_at->toDateTimeString(),
                        ];
                    } elseif ($transaction->box) {
                        return [
                            'title'       => $transaction->box->{'name_' . $locale},
                            'image'       => $transaction->box->image,
                            'amount'      => $transaction->amount,
                            'date'        => $transaction->created_at->toDateTimeString(),
                        ];
                    }
                    return null;
                })
                ->filter()
                ->values();

            return response()->json([
                'message'   => 'تم جلب التبرعات بنجاح',
                'donations' => $donations
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'حصل خطأ أثناء جلب البيانات',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

}
