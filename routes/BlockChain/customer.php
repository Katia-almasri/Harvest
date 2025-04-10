<?php

use App\Http\Controllers\Customer\BlockChain\ContractController;
use App\Http\Controllers\Customer\PaymentController;
use App\Http\Controllers\Customer\RealEstate\InvestmentController;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->group(function (){
    Route::get('token-balance/{spv}',[ContractController::class,'getTokenBalance'])->name('customer.token-balance');

    // pay tokens
    Route::post('real-estates/tokens/buy/{realEstate}',[PaymentController::class,'pay'])->name('customer.real-estates.tokens.buy');
    Route::post('real-estates/tokens/mint-tokens/{realEstate}', [ContractController::class,'mintTokens'])->name('customer.real-estates.tokens.mint-tokens');

    // investments
    Route::prefix('investments')->group(function (){
        Route::get('/',[InvestmentController::class,'index'])->name('customer.investments.index');
        Route::get('/{investment}',[InvestmentController::class,'show'])->name('customer.investments.show');
    });

});
