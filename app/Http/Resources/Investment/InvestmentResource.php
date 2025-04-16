<?php

namespace App\Http\Resources\Investment;

use App\Http\Resources\RealEstate\RealEstateResource;
use App\Models\Customer\Customer;
use App\Models\Customer\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvestmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $customerWallet = $this->wallet();
        return [
            'id'=>$this->id,
            'real_estate'=> new RealEstateResource($this->realEstate),
            'total_tokens'=>$this->total_tokens,
            'total_price'=> $this->total_price,
            'token_price'=> $this->token_price,
            'is_minted'=>(boolean)$this->is_minted,
            'customer_wallet'=> $customerWallet?$customerWallet->wallet_address:null,
            'created_at' => Carbon::parse($this->created_at)->toDateTimeString(),

            // boolean variable detects if the customer has digital wallet in his payment setting or not
            'has_digital_wallet'=> $this->hasDigitalWallet(),
        ];
    }

    public function customerHasWallet(){
        $customer = Customer::where('user_id', auth()->user()->id)->first();
        return Wallet::where('customer_id', $customer?->id)->first();
    }

    public function hasDigitalWallet(){
        $customer = Customer::where('user_id', auth()->user->id)->first();
        return true;
        //TODO if the customer`s payment setting has digit wallet
    }
}
