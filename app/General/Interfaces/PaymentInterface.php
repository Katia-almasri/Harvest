<?php

namespace App\General\Interfaces;


interface PaymentInterface
{
    public function processPayment(array $data);
}
