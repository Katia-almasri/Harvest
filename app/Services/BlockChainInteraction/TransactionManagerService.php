<?php
namespace App\Services\BlockChainInteraction;
use App\Http\Requests\General\Auth\LoginRequest;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;

class TransactionManagerService
{
    public function store(array $data){
        $transaction = new Transaction();
        $transaction->create($data);
        return $transaction;
    }

    public function update(Transaction $transaction, array $data){
        $transaction->update($data);
        return $transaction->fresh();
    }
}
