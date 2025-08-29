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
/*
    public function index()
    {
        return auth()->user()->notifications;
    }
*/

    public function index()
    {
        $locale = app()->getLocale();

        return auth()->user()->notifications->map(function ($notification) use ($locale) {
            return [
                'id'         => $notification->id,
                'title'      => $locale === 'ar' ? $notification->title_ar : $notification->title_en,
                'body'       => $locale === 'ar' ? $notification->body_ar  : $notification->body_en,
                'created_at' => $notification->created_at->format('Y-m-d H:i'),
            ];
        });
    }

    /////
/*

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
        $credentialsFilePath = storage_path('app/firebase/chairty-app-3dd34-firebase-adminsdk-fbsvc-2746f9ae5b.json');
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
            curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/v1/projects/charity-app-12345/messages:send");
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

*/
    ///////////////////////////////////
    public function sendFcmNotification(Request $request)
    {
        $request->validate([
            'user_id'   => 'required|exists:users,id',
            'title_en'  => 'required|string',
            'title_ar'  => 'required|string',
            'body_en'   => 'required|string',
            'body_ar'   => 'required|string',
        ]);

        $user   = \App\Models\User::findOrFail($request->user_id);
        $locale = $user->preferred_language ?? app()->getLocale();
        $title  = $locale === 'ar' ? $request->title_ar : $request->title_en;
        $body   = $locale === 'ar' ? $request->body_ar  : $request->body_en;

        $devices = \App\Models\UserDevice::where('user_id', $user->id)->get();
        if ($devices->isEmpty()) {
            return response()->json(['message' => 'User does not have any device tokens'], 400);
        }

        // Firebase
        $client = new \Google\Client();
        $client->setAuthConfig(storage_path(env('FIREBASE_CREDENTIALS')));
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->refreshTokenWithAssertion();
        $token = $client->getAccessToken();

        // خزّني الإشعار أول شي
        $notification = Notification::create([
            'user_id'   => $user->id,
            'title_en'  => $request->title_en,
            'title_ar'  => $request->title_ar,
            'body_en'   => $request->body_en,
            'body_ar'   => $request->body_ar,
        ]);

        // جهزي الـ payload (رح نرجع أول واحد)
        $data = [
            "message" => [
                "token" => $devices->first()->fcm_token,
                "notification" => [
                    "title" => $title,
                    "body"  => $body,
                ],
                "data" => [
                    "id"         => (string) $notification->id,
                    "created_at" => $notification->created_at->format('Y-m-d H:i'),
                ]
            ]
        ];

        // ابعتي للـ FCM
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/v1/projects/charity-app-12345/messages:send");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer {$token['access_token']}",
            "Content-Type: application/json"
        ]);
        curl_exec($ch);
        curl_close($ch);

        // رجّع نفس الـ structure المطلوب
        return response()->json($data);
    }


    /*
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
      //  $credentialsFilePath = storage_path('app/firebase/chairty-app-3dd34-firebase-adminsdk-fbsvc-2746f9ae5b.json');
        $credentialsFilePath = storage_path(env('FIREBASE_CREDENTIALS'));
        $client = new GoogleClient();
        $client->setAuthConfig($credentialsFilePath);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->refreshTokenWithAssertion();
        $token = $client->getAccessToken();

        // إرسال الإشعارات بأمان
        foreach ($devices as $device) {
            try {
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
                curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/v1/projects/charity-app-12345/messages:send");
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    "Authorization: Bearer {$token['access_token']}",
                    "Content-Type: application/json"
                ]);

                $response = curl_exec($ch);
                if ($response === false) {
                    \Log::error("FCM send failed for user {$user->id} device {$device->id}: " . curl_error($ch));
                }
                curl_close($ch);
            } catch (\Throwable $e) {
                \Log::error("FCM send exception for user {$user->id} device {$device->id}: {$e->getMessage()}");
            }
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
    */

    public function destroy($id): bool
    {
        $notification = auth()->user()->notifications()->findOrFail($id);

        if(isset($notification)) {
            $notification->delete();
            return true;
        }else return false;
    }

}
