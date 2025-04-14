<?php

namespace App\Http\Controllers\Admin\BlockChain;


use App\Enums\General\StatusCodeEnum;
use App\Http\Controllers\General\ApiController;
use App\Http\Requests\Contract\ContractPostRequest;
use App\Http\Requests\Wallet\WalletPostRequest;
use App\Models\BusinessLogic\SPV;
use App\Models\Customer\Customer;
use App\Services\BlockChainInteraction\ContractService;
use App\Services\Customer\CustomerService;
use Mockery\Exception;

class ContractController extends ApiController
{
    public function __construct(private readonly ContractService $contractService, private readonly CustomerService $customerService)
    {
    }

    public function store(SPV $spv, ContractPostRequest $request){
        $contractAddress= $this->contractService->store($spv, $request->all());
        return $contractAddress;
    }

    public function getTokenBalance(SPV $spv, Customer $customer)
    {
        try {
            $this->contractService->getContractBySpv($spv);
            $customer = $this->customerService->show($customer);

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
