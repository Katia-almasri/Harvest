<?php
namespace App\Services\BlockChainInteraction;
use App\Models\BusinessLogic\SPV;
use Web3\Contract;
use Web3\Providers\HttpProvider;
use Web3\RequestManagers\HttpRequestManager;
use Web3\Web3;

class ContractService {
    protected Web3 $web3;
    protected Contract $contract;
    protected string $abi; // represents the web3 platform application ID

    public function __construct()
    {
        $requestManager = new HttpRequestManager(env('INFURA_ENDPOINT'), 20); // 20 seconds timeout
        $provider = new HttpProvider($requestManager);
        $this->web3 = new Web3($provider);
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
}
