<?php

use App\Http\Controllers\General\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login'])->name('user.login');

Route::post('forget-password', [AuthController::class, 'forgetPassword'])->middleware('throttle:5,1');;

Route::post('verify-otp', [AuthController::class, 'verifyOTP']);

Route::post('reset-password', [AuthController::class, 'resetPassword']);

Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::post("logout", [AuthController::class, 'logout'])->name('user.logout');

    Route::post('change-password', [AuthController::class, 'changePassword']);


});

