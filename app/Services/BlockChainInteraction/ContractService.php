<?php
namespace App\Services\BlockChainInteraction;
use App\Enums\Contract\TransactionStatus;
use App\Http\Resources\RealEstate\RealEstateResource;
use App\Jobs\StoreTransactionRecord;
use App\Models\BusinessLogic\SPV;
use Illuminate\Support\Facades\Log;
use Web3\Contract;
use Web3\Utils;


class ContractService {


    public function __construct(private readonly TransactionManagerService $transactionManagerService,
                                 private readonly Web3TransactionService $web3TransactionService
    )
    {
    }

        public function getContractVySpv(SPV $spv){
        return $this->web3TransactionService->getContractBySpv($spv);
    }

    public function store(SPV $spv, array $data)
    {
        $web3 = Web3TransactionService::getWeb3();
        $abi = Web3TransactionService::getAbi();
        $bytecode = Web3TransactionService::getBytecode();

        $contract = new Contract($web3->provider, $abi);
        $contract->bytecode('0x' . $bytecode);
        $adminWallet = $data['admin_wallet_address'];

        $encodedData = $contract->getData(
            $data['real_estate_name'],
            $data['symbol'],
            (int)$data['initial_supply'],
            $spv->wallet->wallet_address,
            (int)$spv->realEstate->id
        );
        $dataPayload = '0x'. $encodedData;
        $nonce = null;
        // Step 1: Get nonce
        $this->web3TransactionService->getNonce($adminWallet, function ($result, $err) use(&$nonce){
            if ($err !== null)
                return null;
            $nonce = $result;
        });

        $gasPrice = $this->web3TransactionService->getGasPrice();
        $gas = $this->web3TransactionService->getGasEstimate($adminWallet, null, $encodedData);
        Log::info("gas:".$gasPrice);
        // Step 3: Prepare transaction parameters
        $txParams = [
            'nonce'    => Utils::toHex($nonce, true),
            'from'     => $adminWallet,
            'gas'      => Utils::toHex($gas, true),
            'gasPrice' => Utils::toHex($gasPrice, true),
            'data'     => $dataPayload,
            'chainId'  => env('CHAIN_ID')
        ];


        // Step 4: sign the transaction
        $signedTx = $this->web3TransactionService->signTransaction($txParams);

        $txHash = $this->web3TransactionService->broadcastTransaction($signedTx);
        $data = [
            'tx_hash'      => $txHash,
            'from_address' => $adminWallet,
            'to_address'   => null,
            'nonce'        => $nonce,
            'gas_limit'    => $gas,
            'gas_price'    => $gasPrice,
            'payload'      => $encodedData,
            'status'       =>TransactionStatus::PENDING->value
        ];
        $this->transactionManagerService->store($data);
        return $txHash;
    }

    public function balanceOf($walletAddress, SPV $spv){
        $contract = $this->web3TransactionService->getContractBySpv($spv);
        return $this->web3TransactionService->callMethod('balanceOf', $walletAddress);
    }

    public function transferTokens($fromAddress, $toAddress, $contractAddress, $status, $amount, $realEstate)
    {
        $this->getContractVySpv($realEstate->spv);
        $this->web3TransactionService->transferTokens($fromAddress,
            $toAddress,
            $contractAddress,
            $status,
            $amount,
            $realEstate,
            function ($data, $err) use (&$transactionHash, &$transactionUrl, $fromAddress, $toAddress) {
            if ($err) {
                Log::error("Transaction failed: " . $err->getMessage());
                throw new \Exception("Transaction failed: " . $err->getMessage());

            } else {
                // Success!
                $transactionHash = $data['transaction_hash'];
                $transactionUrl = $data['transaction_url'];

                // save the transaction into the DB
                $data = [
                    'tx_hash' => $transactionHash,
                    'from_address' => $fromAddress,
                    'to_address' => $toAddress,
                    'nonce' => $data['nonce'],
                    'gas_limit' => $data['gas'],
                    'gas_price' => $data['gas_price'],
                    'payload' => null,
                    'status' => TransactionStatus::PENDING->value
                ];
                StoreTransactionRecord::dispatch($data);

            }
        });

        $returnedResource = [
            'transaction_hash' => $transactionHash,
            'transaction_path' => $transactionUrl,
            'real_estate' => new RealEstateResource($realEstate),
            'tokens' => $amount,
            'wallet_address' => $fromAddress,
            'contract_address' => $contractAddress,
        ];
        return $returnedResource;
    }

    public function retryTransaction(\App\Models\Transaction $transaction): string
    {
        // 1. Bump the gas price
        $oldGasPrice = hexdec($transaction->gas_price);
        $newGasPrice = (int) ($oldGasPrice * 1.2); // +20%

        $txParams = [
            'nonce'     => $transaction->nonce,
            'from'      => $transaction->from_address,
            'to'        => $transaction->to_address,
            'gas'       => $transaction->gas_limit,
            'gasPrice'  => Utils::toHex($newGasPrice, true),
            'data'      => $transaction->payload,
            'value'     => '0x0',
            'chainId'   => env('CHAIN_ID'),
        ];

        // 4. Sign and broadcast
        $signedTx = $this->web3TransactionService->signTransaction($txParams);
        $txHash = $this->web3TransactionService->broadcastTransaction($signedTx);

        // update the transaction status
        $this->transactionManagerService->update($transaction, [
            'retries'=>$transaction->retries + 1,
            'status'=> TransactionStatus::RETRIED
        ]);
        // 5. Done!
        Log::info("✅ Retried tx sent: $txHash");
        return $txHash;
    }


}


