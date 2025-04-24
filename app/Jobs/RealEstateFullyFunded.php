<?php

namespace App\Jobs;

use App\Enums\Payment\PaymentStatus;
use App\Enums\RealEstate\RealEstateStatus;
use App\Models\BusinessLogic\Investment;
use App\Models\Payment;
use App\Models\RealEstate\RealEstate;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Stripe\Stripe;

class RealEstateFullyFunded implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //2. make sure the payment history is paid
        $pendingPayments = Payment::realEstates()->where('status', PaymentStatus::PENDING)->get();
        if(count($pendingPayments)>0){
            return;
        }

        //2.1 continuing

        foreach ($pendingPayments as $payment) {
            $ageInHours = $payment->created_at->diffInHours(now());

            if ($ageInHours >= 24) {
                // 1. Expire the payment
                $payment->update(['status' => PaymentStatus::EXPIRED]);

                // 2. Reduce investment from real estate
                $realEstate = RealEstate::find($payment->payable_id);
                $investment = Investment::where('payment_id', $payment->id)->first();
                $realEstate->update(['shares_sold'=> $realEstate->shares_solde - $investment->total_tokens,
                    'status'=>RealEstateStatus::PENDING
                ]);

                //3. reduce the tokens from the web3 if it is already has wallet (is_minted)
                // use the contract Service

                //4. send notifications for the customers

                // Assumes you store the wallet address and amount in the payment
//                app(Web3Service::class)->revokeTokens(
//                    $payment->wallet_address,
//                    $payment->token_contract_address,
//                    $payment->tokens
//                );

               // Mail::to($payment->user->email)->send(new \App\Mail\InvestmentExpired($payment));

            } else if ($ageInHours >= 2) {
                // send notifications
            }
        }
        //TODO generate the share certificate for each customer using the scheduled job that is examines the totally funding realEstates
        //TODO let the admin notified to generate the final title-deed
    }
}
