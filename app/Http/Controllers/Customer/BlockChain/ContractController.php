<?php

namespace App\Http\Controllers\Customer\BlockChain;

use App\Enums\Contract\TransactionStatus;
use App\Enums\General\StatusCodeEnum;
use App\Http\Controllers\General\ApiController;
use App\Http\Requests\Tokens\PostTokensRequest;
use App\Http\Resources\RealEstate\RealEstateResource;
use App\Jobs\StoreTransactionRecord;
use App\Models\BusinessLogic\SPV;
use App\Models\Customer\Customer;
use App\Models\RealEstate\RealEstate;
use App\Services\BlockChainInteraction\ContractService;
use App\Services\BlockChainInteraction\TransactionManagerService;
use App\Services\BlockChainInteraction\WalletService;
use App\Services\BlockChainInteraction\Web3TransactionService;
use App\Services\Customer\CustomerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Mockery\Exception;
use Symfony\Contracts\Service\Attribute\SubscribedService;
use Web3\Utils;
use Web3p\EthereumTx\Transaction;

class ContractController extends ApiController
{
    private ContractService $contractService;
    public function __construct(
    private readonly CustomerService $customerService)
    {
        $this->contractService = new ContractService(new TransactionManagerService(), new Web3TransactionService(new WalletService()));
    }

    public function getTokenBalance(SPV $spv)
    {
        try {
            $customer = Customer::where('user_id', auth()->user()->id)->first();
            if($customer->wallet()==null)
                throw new Exception(__("customer_wallet_not_configured"));
            $result = $this->contractService->balanceOf($customer->wallet->wallet_address, $spv);
            return $this->apiResponse((string)$result[0]->value, StatusCodeEnum::STATUS_OK, __("messages.success"));
        }
        catch (\Exception $e) {
            return $this->apiResponse(null, StatusCodeEnum::INTERNAL_SERVER_ERROR, __($e->getMessage()));
        }

    }

    public function mintTokens(PostTokensRequest $request, RealEstate $realEstate)
    {
        try {
            $customer = $this->customerService->showByUser(auth()->user());
            // the customer wallet
            $toAddress = $customer->wallet->wallet_address;
            // the related contract address spv
            $spv = $realEstate->spv;
            $fromAddress = $spv->wallet_address;
            $contractAddress = $spv->contract_address;
            $returnedResource = $this->contractService->transferTokens($fromAddress, $toAddress, $contractAddress, TransactionStatus::LATEST->value, $request->amount, $realEstate);
            return $this->apiResponse($returnedResource, StatusCodeEnum::STATUS_OK, __("messages.success"));
        }catch (\Exception $exception){
            Log::error($exception->getMessage());
            return $this->apiResponse(null, StatusCodeEnum::INTERNAL_SERVER_ERROR, __($exception->getMessage()));
        }
    }


}
