<?php

namespace App\Enums\General\Payment;

/**
 * This method types lists the available types we can pay for a specific payment method i.e: Strip
 * Stripe for example enables payment using cards (Master Card), Banks ..
 */
enum PaymentMethodType: int
{
    const CARD='card';

    public static function  getAll(): array
    {
        return [
            PaymentMethodType::CARD
        ];
    }
}
