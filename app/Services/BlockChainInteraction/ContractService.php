<?php
namespace App\Services\BlockChainInteraction;
use App\Enums\Contract\NonceStatus;
use App\Enums\Contract\TransactionStatus;
use App\Models\BusinessLogic\SPV;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use phpseclib\Math\BigInteger;
use Web3\Contract;
use Web3\Providers\HttpProvider;
use Web3\RequestManagers\HttpRequestManager;
use Web3\Utils;
use Web3\Web3;
use Web3p\EthereumTx\Transaction;
use Web3p\EthereumUtil\Util;


class ContractService {
    protected Web3 $web3;
    protected Contract $contract;
    protected string $abi; // represents the web3 platform application ID
    protected string $bytecode; // represents the smart contract bytecode
    protected $eth; // to access the Ethereum function

    public function __construct(private readonly TransactionManagerService $transactionManagerService)
    {
        $requestManager = new HttpRequestManager(env('INFURA_ENDPOINT'), 20); // 20 seconds timeout
        $provider = new HttpProvider($requestManager);
        $this->web3 = new Web3($provider);
        $this->eth = $this->web3->eth;
        $this->abi = file_get_contents(resource_path('contracts/RealEstateToken.json'));
        $this->bytecode = trim(file_get_contents(resource_path('contracts/RealEstateToken.bin')));

    }

    public function store(SPV $spv, array $data)
    {
        $contract = new Contract($this->web3->provider, $this->abi);
        $contract->bytecode('0x' . $this->bytecode);
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
        $this->getNonce($adminWallet, function ($result, $err) use(&$nonce){
            if ($err !== null) {
                Log::error("❌ Nonce fetch failed: " . $err->getMessage());
                return;
            }

            Log::info("✅ Correct nonce is: " . $result);
            $nonce = $result;
            // Now you can continue your logic here using $nonce
        });

        $gasPrice = $this->getGasPrice();
        $gas = $this->getGasEstimate($adminWallet, null, $encodedData);

        // Step 3: Prepare transaction parameters
        $txParams = [
            'nonce'    => Utils::toHex($nonce, true),
            'from'     => $adminWallet,
            'gas'      => Utils::toHex($gas, true),
            'gasPrice' => $gasPrice,
            'data'     => $dataPayload,
            'chainId'  => env('CHAIN_ID')
        ];


        // Step 4: sign the transaction
        $signedTx = $this->signTransaction($txParams);

        $txHash = $this->broadcastTransaction($signedTx);
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
        $transaction = $this->transactionManagerService->store($data);
        return $txHash;
    }
    public function getContractBySpv(SPV $spv): Contract
    {
        if($spv->contract_address==null)
            throw new \Exception("SPV contract address is not configured");

        $contract = new Contract($this->web3->getProvider(), $this->abi);
        $contract->at($spv->contract_address);
        $this->contract = $contract;

        return $contract;
    }

    public function callMethod(string $method, $params): mixed
    {
        $result = null;
        $this->contract->call($method, $params, function ($err, $res) use (&$result) {
            if ($err !== null) {
                throw new \Exception($err->getMessage());
            }
            $result = $res;
        });

        return $result;
    }

    public function getWeb3(){
        return $this->web3;
    }

    public function getContract(){
        return $this->contract;
    }

    public function getEth(){
        return $this->eth;
    }

    public function getTransactionCount($fromAddress, $toAddress, $contractAddress, $status, $amount, $realEstate, callable $callback)
    {
        $this->getContractBySpv($realEstate->spv);
        $this->web3->eth->getTransactionCount($fromAddress, $status, function ($err, $nonce) use (
            $fromAddress,
            $toAddress,
            $contractAddress,
            $amount,
            $realEstate,
            $callback
        ) {
            if ($err !== null) {
                Log::error("Nonce error: " . $err->getMessage());
                return $callback(null, $err); // pass error
            }

            $this->getNonce($fromAddress,  function ($nonce, $err) {
                if ($err !== null) {
                    Log::error("❌ Nonce fetch failed: " . $err->getMessage());
                    return;
                }

                Log::info("✅ Correct nonce is: " . $nonce);

                // Now you can continue your logic here using $nonce
            });
            Log::info("origingal nonce: ".$nonce);
            $data = $this->contract->getData('transfer', $toAddress, $amount);
            $encodedData = '0x'.$data;
            $gasPrice = $this->getGasPrice();
            $gas = $this->getGasEstimate($fromAddress, $contractAddress, $data);

            $txParams = [
                'nonce' => Utils::toHex($nonce, true),
                'from' => $fromAddress,
                'to' => $contractAddress,
                'gas' => $gas,
                'gasPrice' => $gasPrice,
                'value' => '0x0',
                'chainId' => env('CHAIN_ID'),
                'data' => $encodedData,
            ];

            Log::info("txParams: " . json_encode($txParams));

            $transaction = new Transaction($txParams);
            $signedTx = '0x' . $transaction->sign(env('PRIVATE_KEY'));

            $this->web3->eth->sendRawTransaction($signedTx, function ($err, $txHash) use ($callback, $gas, $gasPrice, $nonce) {
                if ($err !== null) {
                    Log::error("Error sending: " . $err->getMessage());
                    return $callback(null, $err);
                }

                $txUrl = "https://sepolia.etherscan.io/tx/" . $txHash;
                $transactionHash = $txHash;
                Log::info("Sent! Tx Hash: " . $txHash);
                Log::info("Track: " . $txUrl);

                return $callback([
                    'transaction_hash' => $transactionHash,
                    'transaction_url'=> $txUrl,
                    'gas'=> $gas,
                    'gas_price'=>$gasPrice,
                    'nonce'=>$nonce,
                ], null);
            });
        });
    }

    public function getGasPrice(): string
    {
        $price = null;

        $this->web3->eth->gasPrice(function ($err, $gasPrice) use (&$price) {
            if ($err !== null) throw new \Exception($err->getMessage());
            // Add +5 gwei to boost it
            $price = bcmul((string) $gasPrice->toString(), '1.25'); // bump 25%
        });

        return Utils::toHex($price, true);
    }

    public function getGasEstimate(string $fromAddress, $toAddress, string $data): string
    {
        $gas = null;
        $this->web3->eth->estimateGas([
            'from' => $fromAddress,
            'to'   => $toAddress,
            'data' => '0x' . $data,
        ], function ($err, $gasEstimate) use (&$gas) {
            if ($err !== null) {
                throw new \Exception("⛽️ Gas estimation failed: " . $err->getMessage());
            }

//            $buffer = new BigInteger(50000);
//            $buffered = $gasEstimate->add($buffer);
//
//            Log::info("⛽️ Raw gas estimate: " . $gasEstimate->toString());
//            Log::info("⛽️ Buffered gas: " . $buffered->toString());

            $gas = $gasEstimate;
        });

        if (!$gas) {
            throw new \Exception("Failed to get gas estimate.");
        }

        return $gas;
    }
    public function getNonce(string $wallet, callable $callback)
    {
        $this->web3->eth->getTransactionCount($wallet, 'latest', function ($err, $count) use ($callback) {
            if ($err !== null) {
                return $callback(null, $err);
            }

            return $callback($count, null);
        });
    }


    public function signTransaction(array $txParams): string
    {
        $privateKey = env('PRIVATE_KEY');

        $tx = new Transaction($txParams);
        $signed = $tx->sign($privateKey);

        return '0x' . $signed;
    }

    public function broadcastTransaction(string $signedTx): string
    {
        $txHash = null;

        // Adding logging to check the transaction
        Log::info("Attempting to broadcast transaction: " . $signedTx);

        $this->web3->eth->sendRawTransaction($signedTx, function ($err, $result) use (&$txHash) {
            if ($err !== null) {
                Log::error("🚨 Broadcast failed: " . $err->getMessage());
                throw new \Exception("🚨 Broadcast failed: " . $err->getMessage());
            }

            Log::info("Transaction successfully broadcasted. Result: " . $result);
            $txHash = $result;
        });

        // If txHash is still null, throw an error indicating the transaction wasn't broadcasted
        if (!$txHash) {
            Log::error("Transaction broadcast failed. No transaction hash returned.");
            throw new \Exception("Transaction broadcast failed.");
        }

        return $txHash;
    }

    public function retryTransaction(\App\Models\Transaction $transaction): string
    {

        // 1. Bump the gas price
        $oldGasPrice = hexdec($transaction->gas_price);
        Log::info("old price".$oldGasPrice);
        $newGasPrice = (int) ($oldGasPrice * 1.2); // +20%

        // 2. Set gas limit (or use estimateGas if needed)

        // 3. Prepare the transaction payload
        $txParams = [
            'nonce'     => $transaction->nonce,
            'from'      => $transaction->from_address,
            'to'        => $transaction->to_address,
            'gas'       => $transaction->gas_limit, true,
            'gasPrice'  => Utils::toHex($newGasPrice, true),
            'data'      => $transaction->payload,
            'value'     => '0x0',
            'chainId'   => env('CHAIN_ID'), // Optional, set in .env
        ];

        // 4. Sign and broadcast
        $signedTx = $this->signTransaction($txParams);
        $txHash = $this->broadcastTransaction($signedTx);
        // update the transaction status
        $this->transactionManagerService->update($transaction, [
            'retries'=>$transaction->retries+1,
            'status'=> TransactionStatus::RETRIED
        ]);
        // 5. Done!
        Log::info("✅ Retried tx sent: $txHash");

        return $txHash;
    }


}


