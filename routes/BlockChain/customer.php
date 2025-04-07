<?php

use App\Http\Controllers\Customer\BlockChain\ContractController;
use App\Http\Controllers\Customer\PaymentController;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->group(function (){
    Route::get('token-balance/{spv}',[ContractController::class,'getTokenBalance'])->name('customer.token-balance');

    // pay tokens
    Route::post('real-estates/tokens/buy',[PaymentController::class,'pay'])->name('customer.real-estates.tokens.buy');
});
