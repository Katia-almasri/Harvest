<?php

namespace App\Enums\General\Payment;

enum PaymentStatus: int
{
    const PAID='paid';
    const UNPAID='unpaid';

    public static function  getAll(): array
    {
        return [
            PaymentStatus::PAID,
            PaymentStatus::UNPAID,
        ];
    }
}
