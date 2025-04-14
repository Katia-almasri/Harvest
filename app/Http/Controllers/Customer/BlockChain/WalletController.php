<?php

namespace App\Http\Controllers\Customer\BlockChain;

use App\Enums\General\StatusCodeEnum;
use App\Http\Controllers\General\ApiController;
use App\Http\Requests\Wallet\WalletPostRequest;
use App\Http\Resources\Wallet\WalletResource;
use App\Services\BlockChainInteraction\WalletService;
use App\Services\Customer\CustomerService;
use Illuminate\Support\Facades\DB;

class WalletController extends ApiController
{
    public function __construct(private WalletService $walletService,
                                private CustomerService $customerService,
    ){}
    public function store(WalletPostRequest $request){
        // save the returned request data into the database customer`s wallet
        try {
            $data = $request->validated();
            $customer = $this->customerService->showByUser(auth()->user());

            DB::beginTransaction();
            $walletCustomer = $this->walletService->store($data, $customer);
            DB::commit();
            return $this->apiResponse(new WalletResource($walletCustomer), StatusCodeEnum::STATUS_OK, __('messages.success.create'));
        }catch (\Exception $e){
            DB::rollBack();
            return $this->apiResponse(null, StatusCodeEnum::INTERNAL_SERVER_ERROR, __($e->getMessage()));

        }
    }
}
