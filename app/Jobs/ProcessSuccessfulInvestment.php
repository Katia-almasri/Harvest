<?php

namespace App\Jobs;

use App\Enums\General\InvestmentStatus;
use App\Enums\General\Payment\PaymentStatus;
use App\Models\BusinessLogic\Investment;
use App\Models\Payment;
use App\Models\RealEstate\RealEstate;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessSuccessfulInvestment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private $paymentIntentId,
        private Payment $payment,
        private Investment $investment,
        private RealEstate $realEstate)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //1. update the payment status
        $this->payment->status = PAymentStatus::SUCCEEDED;
        $this->payment->save();

        //2. update the investment status
        $this->investment->status = InvestmentStatus::SUCCEEDED;
        $this->investment->save();

        //3. update the real estate requirements
        $this->realEstate->update([
            'shares_sold'=> $this->investment->total_tokens,
        ]);


        //TODO send email to the customer and to the Admin
        //TODO send notification
        //TODO continue the web3 related tasks
    }
}
