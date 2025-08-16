<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\NotificationService;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    // عرض جميع إشعارات المستخدم
    public function index()
    {
        return response()->json($this->notificationService->index());
    }

    // إرسال إشعار
    public function send(Request $request)
    {
        return $this->notificationService->sendFcmNotification($request);
    }

    // حذف إشعار
    public function destroy($id)
    {
        $deleted = $this->notificationService->destroy($id);
        return response()->json(['deleted' => $deleted]);
    }
}
