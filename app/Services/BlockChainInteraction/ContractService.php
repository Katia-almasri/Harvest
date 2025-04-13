<?php
namespace App\Services\BlockChainInteraction;
use App\Models\BusinessLogic\SPV;
use Elliptic\EC;
use Illuminate\Support\Facades\Log;
use kornrunner\Keccak;
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
    protected $eth; // to access the Ethereum function

    public function __construct()
    {
        $requestManager = new HttpRequestManager(env('INFURA_ENDPOINT'), 20); // 20 seconds timeout
        $provider = new HttpProvider($requestManager);
        $this->web3 = new Web3($provider);
        $this->eth = $this->web3->eth;
        $this->abi = file_get_contents(resource_path('contracts/RealEstateToken.json'));

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
