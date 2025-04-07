<?php

use App\Http\Controllers\Admin\BlockChain\ContractController;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->group(function (){
    Route::get('token-balance/{spv}/{customer}',[ContractController::class,'getTokenBalance'])->name('admin.token-balance');
});
