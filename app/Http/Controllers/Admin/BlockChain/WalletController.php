<?php

namespace App\Http\Controllers\Admin\BlockChain;

use App\Enums\General\StatusCodeEnum;
use App\Helpers\KeyHelper;
use App\Http\Controllers\General\ApiController;
use App\Http\Requests\Wallet\SpvWalletPostRequest;
use App\Http\Resources\Wallet\WalletResource;
use App\Models\BusinessLogic\SPV;
use App\Services\BlockChainInteraction\ContractService;
use App\Services\BlockChainInteraction\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use kornrunner\Keccak;
use Web3\Utils;
use Web3p\EthereumTx\Transaction;

class WalletController extends ApiController
{

    public function __construct(private readonly WalletService $walletService,
                                private readonly ContractService $contractService
    )
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SPV $spv)
    {
        try {
            DB::beginTransaction();
            $keys = KeyHelper::generateWalletKeys();
            //save this info in the database
            $spvWallet = $this->walletService->store(['private_key'=> $keys['private_key'],
                'wallet_address' => $keys['public_address'],], $spv);
            DB::commit();
            return $this->apiResponse(new WalletResource($spvWallet), StatusCodeEnum::STATUS_OK, __('messages.success'));
        }catch (\Exception $exception){
            DB::rollBack();
            return $this->apiResponse(null, StatusCodeEnum::INTERNAL_SERVER_ERROR, __($exception->getMessage()));
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
