<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


/*
Route::prefix('admin')->middleware('web')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:admin')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});
});
*/
