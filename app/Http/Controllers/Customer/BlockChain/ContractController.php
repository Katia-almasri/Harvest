<?php

namespace App\Http\Controllers\Customer\BlockChain;

use App\Enums\General\StatusCodeEnum;
use App\Http\Controllers\General\ApiController;
use App\Models\BusinessLogic\SPV;
use App\Models\Customer\Customer;
use App\Services\BlockChainInteraction\ContractService;
use App\Services\Customer\CustomerService;
use Illuminate\Http\Request;
use Mockery\Exception;

class ContractController extends ApiController
{
    public function __construct(private readonly ContractService $contractService)
    {
    }

    public function getTokenBalance(SPV $spv)
    {
        try {
            $this->contractService->getContractBySpv($spv);
            $customer = Customer::where('user_id', auth()->user()->id)->first();

            if($customer->customerWallet()==null)
                throw new Exception(__("customer_wallet_not_configured"));

            $result = $this->contractService->callMethod('balanceOf', $customer->customerWallet->wallet_address);
            return $this->apiResponse($result[0]->value, StatusCodeEnum::STATUS_OK, __("messages.success"));
        }
        catch (\Exception $e) {
            return $this->apiResponse(null, StatusCodeEnum::INTERNAL_SERVER_ERROR, __($e->getMessage()));
        }

    }


}
