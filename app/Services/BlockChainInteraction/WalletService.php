<?php
namespace App\Services\BlockChainInteraction;
use App\Models\Customer\Wallet;
use Illuminate\Database\Eloquent\Model;

class WalletService{
    public function index(){}

    public function store(array $data, Model $walletable){
        $wallet = new Wallet();
        $wallet->fill($data);
        $walletable->wallet()->save($wallet);
        return $wallet;
    }

    public function show(){}

    public function showByWalletAddress($walletAddress){
        return Wallet::where('wallet_address', 'like', $walletAddress)->first();
    }
    public function update(){}
    public function destroy(){}
}
