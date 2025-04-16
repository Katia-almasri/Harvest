<?php

namespace App\Jobs;

use App\Services\BlockChainInteraction\TransactionManagerService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class StoreTransactionRecord implements ShouldQueue
{
    use Queueable;
    protected $data;
    protected TransactionManagerService $transactionManagerService;
    public function __construct(array $data)
    {
        $this->transactionManagerService = new TransactionManagerService();
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->transactionManagerService->store($this->data);
    }
}
