<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LanguageController extends Controller
{
    public function setLanguage(Request $request)
    {
        $request->validate([
            'preferred_language' => 'required|in:en,ar',
        ]);

        // جرّب كل الحراسات حسب الترتيب
        $guards = ['admin', 'api'];
        $user = null;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();
                break;
            }
        }

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $user->preferred_language = $request->preferred_language;
        $user->save();

        return response()->json(['message' => 'Language updated successfully']);
    }
}
