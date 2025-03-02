<?php

use App\Http\Controllers\General\Auth\AuthController;
use App\Http\Controllers\General\ProfileController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login'])->name('user.login');

Route::post('forget-password', [AuthController::class, 'forgetPassword'])->middleware('throttle:5,1');;

Route::post('verify-otp', [AuthController::class, 'verifyOTP']);

Route::post('reset-password', [AuthController::class, 'resetPassword']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    // upload image
    Route::post("me/profile/upload-image", [ProfileController::class, 'uploadImage'])->name('user.image.upload');
    Route::post("me/profile/delete-image", [ProfileController::class, 'deleteImage'])->name('user.image.delete');

    Route::post("logout", [AuthController::class, 'logout'])->name('user.logout');

    Route::post('change-password', [AuthController::class, 'changePassword']);


});

