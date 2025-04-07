<?php
namespace App\Services\Payment;

use App\Enums\General\CurrencyType;
use App\General\Interfaces\PaymentInterface;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class StripePayment implements PaymentInterface{

    public function processPayment(array $data)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        if($data['currency'] == CurrencyType::USD){
            $data['amount'] = $data['amount'] * 100;
        }
        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $data['amount'],
                'currency' => $data['currency'],
                'payment_method_types' => $data['payment_method_type'],
            ]);

            return $paymentIntent;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
