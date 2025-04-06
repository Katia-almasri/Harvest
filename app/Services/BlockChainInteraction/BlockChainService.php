<?php
namespace App\Services\BlockChainInteraction;
use App\Enums\Media\MediaCollectionType;
use App\General\MediaInterface;
use App\Helpers\MediaHelper;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Web3\Contract;
use Web3\Providers\HttpProvider;
use Web3\RequestManagers\HttpRequestManager;
use Web3\Web3;

class BlockChainService {
    protected Web3 $web3;
    protected Contract $contract;

    public function __construct()
    {
        $requestManager = new HttpRequestManager(env('INFURA_ENDPOINT'), 20); // 20 seconds timeout
        $provider = new HttpProvider($requestManager);

        $this->web3 = new Web3($provider);

    }


    public function getContract(string $abi, string $contractAddress): Contract
    {
        $contract = new Contract($this->web3->getProvider(), $abi);
        $contract->at($contractAddress);
        return $contract;
    }

    public function callMethod(string $method, array $params = []): mixed
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
