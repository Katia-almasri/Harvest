<?php

namespace App\Enums\General\Payment;

enum PaymentMethod: int
{
    const STRIPE='stripe';
    const CRYPTO='crypto';

    public static function  getAll(): array
    {
        return [
            PaymentMethod::STRIPE,
            PaymentMethod::CRYPTO,
        ];
    }
}
