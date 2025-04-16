<?php

namespace App\Http\Controllers\Customer\BlockChain;

use App\Enums\Contract\NonceStatus;
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
use App\Services\Customer\CustomerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Mockery\Exception;
use Web3\Utils;
use Web3p\EthereumTx\Transaction;

class ContractController extends ApiController
{
    public function __construct(private readonly ContractService $contractService,
    private readonly CustomerService $customerService)
    {
    }

    public function getTokenBalance(SPV $spv)
    {
        try {
            $this->contractService->getContractBySpv($spv);
            $customer = Customer::where('user_id', auth()->user()->id)->first();
            if($customer->wallet()==null)
                throw new Exception(__("customer_wallet_not_configured"));

            $result = $this->contractService->callMethod('balanceOf', $customer->wallet->wallet_address);
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

            $transactionHash = null;
            $transactionUrl = null;
            $this->contractService->getTransactionCount($spv->wallet->wallet_address, $fromAddress, $toAddress, $contractAddress, NonceStatus::PENDING->value, $request->amount, $realEstate, function ($data, $err) use(&$transactionHash, &$transactionUrl, $fromAddress, $toAddress){
                if ($err) {
                    // Handle the error
                    Log::error("Transaction failed: " . $err->getMessage());
                } else {
                    // Success!
                    $transactionHash = $data['transaction_hash'];
                    $transactionUrl = $data['transaction_url'];

                    // save the transaction into the DB
                    $data = [
                        'tx_hash'      => $transactionHash,
                        'from_address' => $fromAddress,
                        'to_address'   => $toAddress,
                        'nonce'        => $data['nonce'],
                        'gas_limit'    => $data['gas'],
                        'gas_price'    => $data['gas_price'],
                        'payload'      => null,
                        'status'       =>TransactionStatus::PENDING->value
                    ];
                    StoreTransactionRecord::dispatch($data);

                }
            });



            $returnedResource = [
                'transaction_hash'=> $transactionHash,
                'transaction_path' => $transactionUrl,
                'real_estate'=> new RealEstateResource($realEstate),
                'tokens'=> $request->amount,
                'wallet_address' => $fromAddress,
                'contract_address' => $contractAddress,
            ];

            return $this->apiResponse($returnedResource, StatusCodeEnum::STATUS_OK, __("messages.success"));
        }catch (\Exception $exception){
            Log::error($exception->getMessage());
            return $this->apiResponse(null, StatusCodeEnum::INTERNAL_SERVER_ERROR, __($exception->getMessage()));
        }
    }


}
