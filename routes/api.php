<?php

use App\Http\Controllers\Application\UserAuthController;
use App\Http\Controllers\Application\UserController;
use App\Http\Controllers\Dashboard\AdminAuthController;
use App\Http\Controllers\VolunteerRequestController;
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

    Route::middleware(['auth:sanctum', 'checkLanguage'])->group(function () {
        Route::post('/logout', [UserAuthController::class, 'logout']);
        Route::get('/showProfile', [UserController::class, 'showProfile']);
        Route::post('/updateProfile', [UserController::class, 'updateProfile']);
    });
});



Route::middleware(['auth:sanctum', 'checkLanguage'])->group(function () {

    Route::post('/addVolunteerRequest', [VolunteerRequestController::class, 'addVolunteerRequest']);
    Route::get('/getMyVolunteerRequests', [VolunteerRequestController::class, 'getMyVolunteerRequests']);
});


// Routes for admin
Route::prefix('admin')->group(function () {
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/forgotPassword', [AdminAuthController::class, 'forgotPassword']);
    Route::post('/checkCode', [AdminAuthController::class, 'checkCode']);
    Route::post('/resetPassword', [AdminAuthController::class, 'resetPassword']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout']);

    });
});
