<?php
namespace App\Services\BlockChainInteraction;
use App\Enums\Contract\TransactionStatus;
use App\Models\BusinessLogic\SPV;
use App\Models\Customer\Wallet;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Web3\Contract;
use Web3\Providers\HttpProvider;
use Web3\RequestManagers\HttpRequestManager;
use Web3\Utils;
use Web3\Web3;
use Web3p\EthereumTx\Transaction;

/**
 * THIS SERVICE IS RESPONSIBLE FOR THE BASIC(CORE) FUNCTIONALITIES IN THE WEB3 CONTRACTS
 * FROM INSTANTIATING THE REQUIRED PARAMETERS TO SIGN AND BROADCAST TRANSACTIONS AND GET NONCE, GAS AND GAS PRICE
 */
class Web3TransactionService
{
    protected static Web3 $web3;
    protected static Contract $contract;
    protected static string $abi; // represents the web3 platform application ID
    protected static string $bytecode; // represents the smart contract bytecode
    protected static $eth; // to access the Ethereum function

    public function __construct(private readonly WalletService $walletService)
    {
        $requestManager = new HttpRequestManager(env('INFURA_ENDPOINT'), env('REQUEST_TIMEOUT'));
        $provider = new HttpProvider($requestManager);
        self::$web3 = new Web3($provider);
        self::$eth = self::$web3->eth;
        self::$abi = file_get_contents(resource_path('contracts/RealEstateToken.json'));
        self::$bytecode = trim(file_get_contents(resource_path('contracts/RealEstateToken.bin')));
    }

    // Singleton function (entry point)
    public function getContractBySpv(SPV $spv): Contract
    {
        if($spv->contract_address==null)
            throw new \Exception("SPV contract address is not configured");

            $contract = new Contract(self::$web3->getProvider(), self::$abi);
            $contract->at($spv->contract_address);
            self::$contract = $contract;
            return $contract;
    }

    /**
     * GENERIC FUNCTION TO CALL THE GET METHOD OF A CONTRACT I.E: BALANCE OF
     * @param string $method
     * @param $params
     * @return mixed
     */
    public function callMethod(string $method, $params): mixed
    {
        $result = null;
        self::$contract->call($method, $params, function ($err, $res) use (&$result) {
            if ($err !== null) {
                throw new \Exception($err->getMessage());
            }
            $result = $res;
        });

        return $result;
    }

    /**
     * TO MINT (TRANSFER) TOKENS (POST METHODS OR OPERATIONS DIRECTLY ON THE CONTRACT)
     * @param $fromAddress
     * @param $toAddress
     * @param $contractAddress
     * @param $status
     * @param $amount
     * @param $realEstate
     * @param callable $callback
     * @return void
     * @throws \Exception
     */
    public function transferTokens($fromAddress, $toAddress, $contractAddress, $status, $amount, $realEstate, callable $callback)
    {
        self::$web3->eth->getTransactionCount($fromAddress, $status, function ($err, $nonce) use (
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
            });
            $dataPayload = self::$contract->getData('transfer', $toAddress, $amount);

            $encodedData = '0x' . $dataPayload;
            $gasPrice = $this->getGasPrice();
            $gas = '0x' . Utils::toHex(env('GAS'), true);
            Log::info("Gas Price: " . $gasPrice);
            Log::info("Gas Limit: " . $gas);


            $txParams = [
                'nonce' => Utils::toHex($nonce, true),
                'from' => $fromAddress,
                'to' => $contractAddress,
                'gas' => $gas,
                'gasPrice' => Utils::toHex($gasPrice, true),
                'value' => '0x0',
                'chainId' => env('CHAIN_ID'),
                'data' => $encodedData,
            ];

            Log::info("txParams: " . json_encode($txParams));

            $transaction = new Transaction($txParams);
            $privateKey = $this->walletService->showByWalletAddress($fromAddress)->private_key;
            $signedTx = '0x' . $transaction->sign(Crypt::decryptString($privateKey));

            self::$web3->eth->sendRawTransaction($signedTx, function ($err, $txHash) use ($callback, $gas, $gasPrice, $nonce) {
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

        self::$web3->eth->gasPrice(function ($err, $gasPrice) use (&$price) {
            if ($err !== null) throw new \Exception($err->getMessage());
            $price = bcmul((string) $gasPrice->toString(), '1.25'); // bump 25%
        });

        return $price;
    }

    public function getGasEstimate(string $fromAddress, $toAddress, string $data): string
    {
        $gas = null;
        self::$web3->eth->estimateGas([
            'from' => $fromAddress,
            'to'   => $toAddress,
            'data' => '0x' . $data,
        ], function ($err, $gasEstimate) use (&$gas) {
            if ($err !== null)
                throw new \Exception("Gas estimation failed: " . $err->getMessage());

            $gas = $gasEstimate;
        });

        if (!$gas)
            throw new \Exception("Failed to get gas estimate.");
        return $gas;
    }
    public function getNonce(string $wallet, callable $callback)
    {
        self::$web3->eth->getTransactionCount($wallet, TransactionStatus::LATEST->value, function ($err, $count) use ($callback) {
            if ($err !== null)
                return $callback(null, $err);
            return $callback($count, null);
        });
    }

    public function signTransaction(Wallet $adminWallet, array $txParams): string
    {
        $privateKey = $adminWallet->private_key;
        $tx = new Transaction($txParams);
        $signed = $tx->sign($privateKey);
        return '0x' . $signed;
    }

    public function broadcastTransaction(string $signedTx): string
    {
        $txHash = null;
        // Adding logging to check the transaction
        Log::info("Attempting to broadcast transaction: " . $signedTx);

        self::$web3->eth->sendRawTransaction($signedTx, function ($err, $result) use (&$txHash) {
            if ($err !== null)
                throw new \Exception("Broadcast failed: " . $err->getMessage());
            $txHash = $result;
        });

        // If txHash is still null, throw an error indicating the transaction wasn't broadcasted
        if (!$txHash) {
            Log::error("Transaction broadcast failed. No transaction hash returned.");
            throw new \Exception("Transaction broadcast failed.");
        }
        return $txHash;
    }

    public static function getWeb3(): Web3
    {
        return self::$web3;
    }

    public static function getContract(): Contract
    {
        return self::$contract;
    }

    public static function getAbi(): string
    {
        return self::$abi;
    }

    public static function getBytecode(): string
    {
        return self::$bytecode;
    }

    public static function getEth(): \Web3\Eth
    {
        return self::$eth;
    }



}
