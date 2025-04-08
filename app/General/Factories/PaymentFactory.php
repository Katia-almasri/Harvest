<?php
namespace App\General\Factories;
use App\General\Interfaces\PaymentInterface;
use App\Services\Payment\PaymentMethod\StripePaymentService;

class PaymentFactory
{
    public static function create(string $paymentMethod): PaymentInterface
    {
        return match ($paymentMethod) {
            'stripe' => new StripePaymentService(),
            default => throw new \Exception("Unsupported payment method: $paymentMethod"),
        };
    }
}
