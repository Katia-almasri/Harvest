<?php

namespace App\Enums\Payment;

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
