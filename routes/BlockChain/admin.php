<?php

use App\Http\Controllers\BlockChain\ContractController;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->group(function (){
    Route::get('token-balance',[ContractController::class,'getTokenBalance'])->name('token-balance');
});
