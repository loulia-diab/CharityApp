<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ChangeLanguage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    /*
    public function handle(Request $request, Closure $next): Response
    {

        // تحقق إذا كان المستخدم مسجلاً دخوله
        if (Auth::check()) {
            // تعيين اللغة بناءً على اللغة المفضلة للمستخدم المخزنة في قاعدة البيانات
            $locale = Auth::user()->preferred_language;
            // تعيين اللغة في التطبيق بناءً على اللغة المفضلة
            app()->setLocale($locale);
        } else {
            // تعيين اللغة افتراضيًا إلى الإنجليزية إذا كان المستخدم غير مسجل دخوله
            app()->setLocale('en');
        }

        return $next($request);
    }
    */

    public function handle(Request $request, Closure $next)
    {
        $locale = null;

        // جرب الحصول على المستخدم من الحراس المختلفين
        $guards = ['admin', 'api'];

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $locale = Auth::guard($guard)->user()->preferred_language;
                break;
            }
        }

        // جرب من Accept-Language
        if (!$locale && $request->hasHeader('Accept-Language')) {
            $locale = $request->header('Accept-Language');
        }

        // جرب من ?lang=ar
        if (!$locale && $request->query('lang')) {
            $locale = $request->query('lang');
        }

        // تحقق من صحة اللغة
        if (!in_array($locale, ['en', 'ar'])) {
            $locale = config('app.locale'); // fallback
        }

        App::setLocale($locale);

        return $next($request);
    }

}
