<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\UserDevice;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function registerDevice(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
        ]);

        $user = auth()->user();

        // حفظ أو تحديث الـ token
        UserDevice::updateOrCreate(
            [
                'user_id' => $user->id,
                'fcm_token' => $request->fcm_token
            ],
            ['last_used_at' => now()]
        );

        return response()->json(['message' => 'Device registered successfully']);
    }

}
