<?php

use App\Http\Controllers\Customer\AuthController;
use App\Http\Controllers\Customer\ProfileController;
use Illuminate\Support\Facades\Route;

Route::post("register", [AuthController::class, 'register'])->name('customer.register');

Route::middleware('auth:sanctum')->group(function (){
    Route::post("create-profile", [ProfileController::class, 'store'])->name('customer.store');
    Route::get("my-profile", [ProfileController::class, 'show'])->name('customer.show');
    Route::post("complete-profile", [ProfileController::class, 'completeProfile'])->name('customer.profile.complete');

    // Customer Images (residential cards, passport images)
    Route::post("upload-image", [ProfileController::class, 'uploadImage'])->name('customer.image.upload');

    // Update Customer Profile
    Route::post("me/update-profile", [ProfileController::class, 'update'])->name('customer.profile.update');

});
