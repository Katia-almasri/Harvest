<?php

use App\Http\Controllers\Customer\AuthController;
use App\Http\Controllers\Customer\ProfileController;
use Illuminate\Support\Facades\Route;

Route::post("register", [AuthController::class, 'register'])->name('customer.register');
Route::post("create-profile", [ProfileController::class, 'store'])->name('customer.store');
