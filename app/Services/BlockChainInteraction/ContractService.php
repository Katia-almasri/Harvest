<?php
namespace App\Services\BlockChainInteraction;
use App\Enums\Contract\NonceStatus;
use App\Models\BusinessLogic\SPV;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
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

    public function __construct()
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
        $privateKey = $spv->wallet->private_key;

        $encodedData = $contract->getData(
            $data['real_estate_name'],    // string name
            $data['symbol'],              // string symbol
            100,                          // uint256 initialSupply
            "0x23678678b7665a96a14dd15798db0e776d140b7a", // address _spvAddress
            1                             // uint256 _propertyId
        );


        // Step 1: Get nonce
        $nonce = null;
        $this->web3->eth->getTransactionCount($adminWallet, 'latest', function ($err, $transactionCount) use (&$nonce) {
            if ($err !== null) {
                throw new \Exception("❌ Failed to get nonce: " . $err->getMessage());
            }
            $nonce = Utils::toHex($transactionCount, true);
            logger()->info("Nonce for transaction: " . $transactionCount);
        });

        // Step 3: Prepare transaction parameters
        $txParams = [
            'nonce'    => $nonce,
            'from'     => "0x23678678b7665a96a14dd15798db0e776d140b7a",
            'gas'      => Utils::toHex(600000, true), // Increased gas limit
            'gasPrice' => Utils::toHex(Utils::toWei('10', 'gwei'), true), // Higher gas price
            'data'     => $encodedData,
            'chainId'  => 11155111 // Sepolia test network
        ];

        // Step 4: Manually sign the transaction
        $transaction = new \Web3p\EthereumTx\Transaction($txParams);
        $signedTx = '0x' . $transaction->sign(env('PRIVATE_KEY'));


        // Step 5: Send the signed transaction
        $this->web3->eth->sendRawTransaction($signedTx, function ($err, $txHash) {
            if ($err !== null) {
                Log::info("❌ Deployment failed: " . $err->getMessage());
                throw new \Exception("❌ Deployment failed: " . $err->getMessage());
            }

            Log::info("✅ Contract deployment sent! Tx Hash: $txHash");

        });
    }
    public function getContractBySpv(SPV $spv): Contract
    {
        if($spv->contract_address==null)
            throw new \Exception("SPV contract address is not configured");

        $contract = new Contract($this->web3->getProvider(), $this->abi);
        $contract->at("0x69C582c9FaAa34C2E1cF2632Dc8779424c98d101");
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

            $transactionCount = $nonce->toString();
            $data = '0x' . $this->contract->getData('transfer', $toAddress, $amount);

            $txParams = [
                'nonce' => Utils::toHex($transactionCount, true),
                'from' => $fromAddress,
                'to' => $contractAddress,
                'gas' => Utils::toHex(env('GAS'), true),
                'gasPrice' => Utils::toHex(Utils::toWei(env('GAS_PRICE'), 'gwei'), true),
                'value' => '0x0',
                'chainId' => env('CHAIN_ID'),
                'data' => $data,
            ];

            Log::info("txParams: " . json_encode($txParams));

            $transaction = new Transaction($txParams);
            $signedTx = '0x' . $transaction->sign(env('PRIVATE_KEY'));

            $this->web3->eth->sendRawTransaction($signedTx, function ($err, $txHash) use ($callback) {
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
                ], null);
            });
        });
    }
}


