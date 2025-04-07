<?php
namespace App\General\Factories;
use App\General\Interfaces\PaymentInterface;
use App\Services\Payment\StripePayment;

class PaymentFactory
{
    public static function create(string $paymentMethod): PaymentInterface
    {
        return match ($paymentMethod) {
            'stripe' => new StripePayment(),
            default => throw new \Exception("Unsupported payment method: $paymentMethod"),
        };
    }
}
