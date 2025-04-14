<?php

use App\Http\Controllers\Admin\BlockChain\ContractController;
use App\Http\Controllers\Admin\BlockChain\WalletController;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->group(function (){
    Route::get('token-balance/{spv}/{customer}',[ContractController::class,'getTokenBalance'])->name('admin.token-balance');

    // create spv wallet
    Route::post('real-estates/spv/{spv}/wallets/', [WalletController::class, 'store'])->name('admin.real-estates.spv.wallets.store');

    // deploy spv contract
    Route::post('real-estates/spv/{spv}/contracts/', [ContractController::class, 'store'])->name('admin.real-estates.spv.contracts.store');
});
