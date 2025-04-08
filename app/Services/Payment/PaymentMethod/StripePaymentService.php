<?php
namespace App\Services\Payment\PaymentMethod;

use App\Enums\General\CurrencyType;
use App\General\Interfaces\PaymentInterface;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class StripePaymentService implements PaymentInterface{

    public function processPayment(array $data)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $data['amount'],
                'currency' => $data['currency'],
                'payment_method_types' => [$data['payment_method_type']],
                'description' => 'Token Purchase - Real Estate Investment',
                'metadata' => $data['metadata'],
            ]);

            return $paymentIntent;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
