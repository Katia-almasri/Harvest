<?php

namespace App\Enums\Payment;

enum Payable: int
{
    // the type of object or things we can pay for in this platform
    const REALESTATE ='realEstate';

    public static function  getAll(): array
    {
        return [
            Payable::REALESTATE,
        ];
    }
}
