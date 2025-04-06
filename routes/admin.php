<?php

use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\RealEstate\RealEstateController;
use App\Http\Controllers\Admin\RealEstate\SpvController;
use App\Http\Middleware\Admin\CustomerManagement;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->group(function (){

    // admin profile
    Route::get("me/profile", [ProfileController::class, 'show'])->name('admin.profile.show');
    Route::post("me/profile", [ProfileController::class, 'update'])->name('admin.profile.update');

    // update email
    Route::post('me/profile/update-email', [ProfileController::class, 'updateEmail'])->name('admin.email.update');
    Route::post('me/profile/verify-email', [ProfileController::class, 'verifyEmail'])->name('admin.email.verify');
    Route::post('me/profile/reset-email', [ProfileController::class, 'resetEmail'])->name('admin.email.reset..');
    Route::group(['prefix' => "real-estates"], function () {

        // Real Estate Management:
        Route::post('/', [RealEstateController::class, 'store'])->name('admin.real-estate.store');

        // Upload Real Estate Images
        Route::post('/upload-images/{realEstate}', [RealEstateController::class, 'uploadImages'])->name('admin.real-estate.images.upload');

        // Upload Real Estate Documents
        Route::post('/upload-documents/{realEstate}', [RealEstateController::class, 'uploadDocuments'])->name('admin.real-estate.documents.upload');

        Route::post('/delete-media/{realEstate}/{media}', [RealEstateController::class, 'deleteMedia'])->name('admin.real-estate.medias.delete');
        Route::get('/', [RealEstateController::class, 'index'])->name('admin.real-estate.index');
        Route::get('/{realEstate}', [RealEstateController::class, 'show'])->name('admin.real-estate.show');
        Route::patch('/{realEstate}', [RealEstateController::class, 'update'])->name('admin.real-estate.update');
        Route::post('/change-status/{realEstate}', [RealEstateController::class, 'changeStatus'])->name('admin.real-estate.status');
        Route::post('/delete/{realEstate}', [RealEstateController::class, 'destroy'])->name('admin.real-estate.delete');
    });

    Route::prefix('customers')->middleware(CustomerManagement::class)->group(function (){
        Route::get('/', [CustomerController::class, 'index'])->name('admin.customer.index');
    });

    Route::group(['prefix' => "spv"], function () {
        Route::post('/real-estates/{realEstate}', [SpvController::class, 'store'])->name('.spv.store');
        Route::get('/{spv}', [SpvController::class, 'show'])->name('.spv.show');
        Route::delete('/{spv}', [SpvController::class, 'destroy'])->name('.spv.destroy');
    });


});
