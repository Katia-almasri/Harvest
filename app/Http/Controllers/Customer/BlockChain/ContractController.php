<?php

namespace App\Http\Controllers\Customer\BlockChain;

use App\Enums\General\StatusCodeEnum;
use App\Http\Controllers\General\ApiController;
use App\Http\Requests\Tokens\PostTokensRequest;
use App\Models\BusinessLogic\SPV;
use App\Models\Customer\Customer;
use App\Models\RealEstate\RealEstate;
use App\Services\BlockChainInteraction\ContractService;
use App\Services\Customer\CustomerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Mockery\Exception;
use Web3\Utils;
use Web3p\EthereumTx\Transaction;

class ContractController extends ApiController
{
    public function __construct(private readonly ContractService $contractService)
    {
    }

    public function getTokenBalance(SPV $spv)
    {
        try {
            $this->contractService->getContractBySpv($spv);
            $customer = Customer::where('user_id', auth()->user()->id)->first();

            if($customer->customerWallet()==null)
                throw new Exception(__("customer_wallet_not_configured"));

            $result = $this->contractService->callMethod('balanceOf', $customer->customerWallet->wallet_address);
            return $this->apiResponse((string)$result[0]->value, StatusCodeEnum::STATUS_OK, __("messages.success"));
        }
        catch (\Exception $e) {
            return $this->apiResponse(null, StatusCodeEnum::INTERNAL_SERVER_ERROR, __($e->getMessage()));
        }

    }

    public function mintTokens(PostTokensRequest $request, RealEstate $realEstate){
        $toAddress = "0x23678678b7665a96a14dd15798db0e776d140b7b";
        $fromAddress = "0x23678678b7665a96a14dd15798db0e776d140b7a";
        $amount = bcmul($request->amount, bcpow('10', '18'));
        $privateKey = env('PRIVATE_KEY');
        $contract = $this->contractService->getContractBySpv($realEstate->spv);

        $result = null;
        $data = '0x'.$contract->getData('transfer', $toAddress, $amount); // Amount in smallest unit (18 decimals for ERC20)
        $nonce = null;

        $this->contractService->getWeb3()->eth->getTransactionCount($fromAddress, 'pending', function ($err, $nonce) use (&$transactionCount) {
            if ($err !== null) {
                throw new \Exception('Nonce error: ' . $err->getMessage());
            }
            $transactionCount = $nonce->toString();
        });

        // Build the transaction
        $txParams = [
            'nonce' => Utils::toHex($transactionCount, true),
            'from' => $fromAddress,
            'to' => '0x167dB1A8085Ed49Af6E66ac231Bfc5aB9df6BC83',
            'gas' => Utils::toHex(60000, true),
            'gasPrice' => Utils::toHex(Utils::toWei('7', 'gwei'), true),
            'value' => '0x0',
            'chainId' => 11155111, // Sepolia chain ID
            'data' => $data,
        ];
            // Step 3: Sign and send the transaction
        $transaction = new Transaction($txParams);
        $signedTx = '0x' . $transaction->sign($privateKey);

// Send raw transaction
        $this->contractService->getWeb3()->eth->sendRawTransaction($signedTx, function ($err, $txHash) {
            if ($err !== null) {
                echo "❌ Error sending: " . $err->getMessage() . PHP_EOL;
            } else {
                echo "✅ Sent! Tx Hash: " . $txHash . PHP_EOL;
                echo "🔍 Track: https://sepolia.etherscan.io/tx/" . $txHash . PHP_EOL;
            }
        });
    }



}
