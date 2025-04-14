<?php
namespace App\Services\BlockChainInteraction;
use App\Enums\Contract\NonceStatus;
use App\Models\BusinessLogic\SPV;
use Exception;
use Illuminate\Support\Facades\Log;
use Web3\Contract;
use Web3\Providers\HttpProvider;
use Web3\RequestManagers\HttpRequestManager;
use Web3\Utils;
use Web3\Web3;
use Web3p\EthereumTx\Transaction;


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
        $this->getContractBySpv($spv);
        $spvWalletAddress = $spv->wallet->wallet_address;
        $adminWallet = $data['admin_wallet_address'];

        // Bytecode must be prefixed with "0x"
        $bytecode = '0x' . $this->bytecode;

        $nonce = null;

        // 1. Get Nonce
        $this->web3->eth->getTransactionCount($adminWallet, 'pending', function ($err, $transactionCount) use (&$nonce) {
            if ($err !== null) {
                throw new \Exception("❌ Failed to get nonce: " . $err->getMessage());
            }
            $nonce = Utils::toHex($transactionCount, true);
        });


        // 2. Prepare Deployment Transaction
        $txParams = [
            'nonce'    => $nonce,
            'from'     => $adminWallet,
            'gas'      => Utils::toHex(3000000, true),
            'gasPrice' => Utils::toHex(Utils::toWei('5', 'gwei'), true),
            'data'     => $bytecode,
            'chainId'  => 11155111 // Sepolia
        ];

        // 3. Manually Sign the Transaction
        $transaction = new \Web3p\EthereumTx\Transaction($txParams);
        $signedTx = '0x' . $transaction->sign($spv->wallet->private_key);

        $txHash = null;

        // 4. Send Raw Transaction
        $this->web3->eth->sendRawTransaction($signedTx, function ($err, $hash) use (&$txHash) {
            if ($err !== null) {
                throw new \Exception("❌ Deployment failed: " . $err->getMessage());
            }
            logger()->info("✅ Contract deployment sent! Tx Hash: $hash");
            $txHash = $hash;
        });


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


