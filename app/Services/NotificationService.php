<?php

namespace App\Services;

use App\Models\Notification as NotificationModel;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Notification;
class NotificationService
{

    public function index()
    {
        return auth()->user()->notifications;
    }

    public function sendFcmNotification(Request $request)
    {
        $request->validate([
            'user_id'   => 'required|exists:users,id',
            'title_en'  => 'required|string',
            'title_ar'  => 'required|string',
            'body_en'   => 'required|string',
            'body_ar'   => 'required|string',
        ]);

        $user = \App\Models\User::findOrFail($request->user_id);

        // اختيار اللغة حسب المستخدم أو التطبيق
        $locale = $user->preferred_language ?? app()->getLocale(); // 'ar' أو 'en'

        $title = $locale === 'ar' ? $request->title_ar : $request->title_en;
        $body  = $locale === 'ar' ? $request->body_ar  : $request->body_en;

        // جلب الأجهزة
        $devices = \App\Models\UserDevice::where('user_id', $user->id)->get();

        if ($devices->isEmpty()) {
            return response()->json(['message' => 'User does not have any device tokens'], 400);
        }

        // Firebase credentials
        $credentialsFilePath = Storage::path('app/firebase/boffee-7fa4c-firebase-adminsdk-xp4k4-5bf998dd8d.json');
        $client = new GoogleClient();
        $client->setAuthConfig($credentialsFilePath);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->refreshTokenWithAssertion();
        $token = $client->getAccessToken();

        // إرسال الإشعارات
        foreach ($devices as $device) {
            $data = [
                "message" => [
                    "token" => $device->fcm_token,
                    "notification" => [
                        "title" => $title,
                        "body"  => $body,
                    ],
                ]
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/v1/projects/boffee-7fa4c/messages:send");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_exec($ch);
            curl_close($ch);
        }

        // تخزين الإشعار في قاعدة البيانات
        Notification::create([
            'user_id'   => $user->id,
            'title_en'  => $request->title_en,
            'title_ar'  => $request->title_ar,
            'body_en'   => $request->body_en,
            'body_ar'   => $request->body_ar,
        ]);

        return response()->json(['message' => 'Notifications have been sent']);
    }


    public function destroy($id): bool
    {
        $notification = auth()->user()->notifications()->findOrFail($id);

        if(isset($notification)) {
            $notification->delete();
            return true;
        }else return false;
    }

}
