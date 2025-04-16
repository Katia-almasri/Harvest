<?php

namespace App\Jobs;

use App\Models\Transaction;
use App\Services\BlockChainInteraction\ContractService;
use App\Services\BlockChainInteraction\TransactionManagerService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MonitorPendingTransactions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */

    public function __construct()
    {
    }

    public function handle(ContractService $contractService)
    {
        $transactions = Transaction::where('status', 'pending')->get();
        foreach ($transactions as $_transaction) {
            $contractService->retryTransaction($_transaction);
        }
    }
}
