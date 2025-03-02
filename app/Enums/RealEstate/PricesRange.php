<?php

namespace App\Enums\RealEstate;

enum PricesRange
{
    const NORMAL = 1000;
    const MEDIUM = 2000;
    const HIGH = 3000;

    public static function  getAll(): array
    {
        return [
            PricesRange::NORMAL,
            PricesRange::MEDIUM,
            PricesRange::HIGH
        ];
    }
}
