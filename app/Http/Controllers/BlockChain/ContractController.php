<?php

namespace App\Http\Controllers\BlockChain;


use App\Enums\General\StatusCodeEnum;
use App\Http\Controllers\General\ApiController;
use App\Services\BlockChainInteraction\BlockChainService;
use Exception;
use Illuminate\Http\Request;

class ContractController extends ApiController
{
    public function __construct(private readonly BlockChainService $blockChainService)
    {
    }

    public function getTokenBalance(Request $request)
    {
        $request->validate([
            'contract_address' => 'required|string',
            'wallet_address' => 'required|string',
        ]);

        $abi = file_get_contents(resource_path('contracts/RealEstateToken.json'));
        $contract = $this->blockChainService->getContract($abi, $request->contract_address);
        $result = null;
        $contract->call('balanceOf', $request->wallet_address, function ($err, $result) {
            if ($err !== null) {
                throw new Exception('Error: ' . $err->getMessage());
            }
            dd($result[0]);
        });


        // Convert the balance to a string and return it
//        return $this->apiResponse((string) $balance, StatusCodeEnum::STATUS_OK, __('messages.success'));
    }
}
