<?php
namespace App\Services\BlockChainInteraction;
use App\Models\BusinessLogic\SPV;
use Elliptic\EC;
use kornrunner\Keccak;
use Web3\Contract;
use Web3\Providers\HttpProvider;
use Web3\RequestManagers\HttpRequestManager;
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

    // * contract Address is the id when we deployed the contract
    // * from Address the the spv wallet includes tokens
    public function mint(string $fromAddress, string $toAddress, string $contractAddress, string $amount){
        $functionData = $this->contract->getData('mint', $toAddress, $amount);

        // Get nonce (number of transactions sent so far)
        $nonce = hexdec($this->eth->getTransactionCount($fromAddress, 'pending')->toString());

        // Create the transaction payload
        $tx = new Transaction([
            'nonce' => '0x' . dechex($nonce),
            'to' => $toAddress,
            'gas' => env('GAS_LIMIT_HEXA'),
            'gasPrice' => env('GAS_PRICE_HEXA'),
            'value' => '0x0', // no ETH being sent
            'data' => $functionData, // this calls the `mint` function
            'chainId' => env('CHAIN_ID'),
        ]);

        // Sign the transaction using your private key
        $signedTx = '0x' . $tx->sign(env('PRIVATE_KEY'));

        // Send the transaction to the blockchain
        $this->eth->sendRawTransaction($signedTx, function ($err, $txHash) {
            if ($err !== null) {
                throw new \Exception("Transaction Failed: " . $err->getMessage());
            }
            logger("✅ Mint TX sent: " . $txHash);
        });
    }

    // 🔑 Convert private key to public address
    public function privateKeyToAddress($privateKey)
    {
        $ec = new EC('secp256k1');
        $key = $ec->keyFromPrivate(ltrim($privateKey, '0x'));
        $pubKey = $key->getPublic(false, 'hex');
        $pubKey = substr($pubKey, 2);
        return '0x' . substr(Keccak::hash(hex2bin($pubKey), 256), 24);
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
}
