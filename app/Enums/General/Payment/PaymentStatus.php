<?php

namespace App\Enums\General\Payment;

enum PaymentStatus: int
{
    const PAID='paid';
    const UNPAID='unpaid';
    const SUCCEEDED='succeeded';
    const PENDING='pending';

    // for webhook event type status
    const PAYMENT_INTENT_SUCCEEDED='payment_intent.succeeded';
    const PAYMENT_INTENT_PENDING='payment_intent.pending';
    const PAYMENT_INTENT_UNPAID='payment_intent.unpaid';



    public static function  getAll(): array
    {
        return [
            PaymentStatus::PAID,
            PaymentStatus::UNPAID,
            PaymentStatus::SUCCEEDED,
            PaymentStatus::PENDING,
            PaymentStatus::PAYMENT_INTENT_SUCCEEDED,
            PaymentStatus::PAYMENT_INTENT_PENDING,
            PaymentStatus::PAYMENT_INTENT_UNPAID,

        ];
    }
}
