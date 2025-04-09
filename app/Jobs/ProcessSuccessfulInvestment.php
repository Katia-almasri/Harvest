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
use Illuminate\Support\Facades\Log;

class ProcessSuccessfulInvestment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    private $paymentIntentId;
    private $realEstateId;
    public function __construct(
        $paymentIntentId, $realEstateId
        )
    {
        $this->paymentIntentId = $paymentIntentId;
        $this->realEstateId = $realEstateId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //1. update the payment status
        $payment = Payment::where('payment_intent_id', $this->paymentIntentId)->first();
        $payment->status = PAymentStatus::SUCCEEDED;
        $payment->save();

        //2. update the investment status
        $investment = Investment::where('payment_id', $payment->id)->first();
        $investment->status = InvestmentStatus::SUCCEEDED;
        $investment->save();

        //3. update the real estate requirements
        $realEstate = RealEstate::find($this->realEstateId);
        $realEstate->update([
            'shares_sold'=> $investment->total_tokens,
        ]);




        //TODO send email to the customer and to the Admin
        //TODO send notification
        //TODO continue the web3 related tasks
    }
}
