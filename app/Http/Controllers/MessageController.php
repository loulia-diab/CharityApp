<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function sendMessage(Request $request)
    {
        $user = auth('api')->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $request->validate([
            'phone' => 'required|string|max:20',
            'message' => 'required|string',
        ]);

        $message = Message::create([

            'user_id' => $user->id,
            'phone' => $request->phone,
            'message' => $request->message,
        ]);

        return response()->json([
            'message' => 'Message sent successfully',
            'data' => $message
        ], 201);
    }

    public function getMessagesFilterByRead(Request $request)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'is_read_by_admin' => 'required|boolean',
        ]);

        $messages = Message::with('user:id,name')
            ->where('is_read_by_admin', $request->is_read_by_admin)
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($message) {
                return [
                    'user_name' => $message->user->name,
                    'phone' => $message->phone,
                    'message' => $message->message,
                    'created_at' => $message->created_at->toDateTimeString(),
                ];
            });

        return response()->json([
            'data' => $messages
        ]);
    }
}
