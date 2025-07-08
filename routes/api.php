<?php

use App\Http\Controllers\Application\UserAuthController;
use App\Http\Controllers\Dashboard\AdminAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LanguageController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::middleware('auth:sanctum')
    ->post('/setLanguage', [LanguageController::class, 'setLanguage']);


// Routes for user
Route::prefix('user')->group(function () {
    Route::post('/register', [UserAuthController::class, 'register']);
    Route::post('/login', [UserAuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [UserAuthController::class, 'logout']);
    });
});


Route::middleware(['auth:sanctum', 'checkLanguage'])->group(function () {

    // Route::post('/changeLanguage', [AuthController::class, 'changeLanguage']);
  //  Route::post('/setUserLanguage', [UserAuthController::class, 'setUserLanguage']);
});


// Routes for admin
Route::prefix('admin')->group(function () {
    Route::post('/login', [AdminAuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout']);
       // Route::post('/setAdminLanguage', [AdminAuthController::class, 'setAdminLanguage']);
    });
});
