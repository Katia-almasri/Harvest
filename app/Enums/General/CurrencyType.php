<?php

namespace App\Enums\General;

enum CurrencyType: string
{
    const USD='usd';
    const AED='aed';
    const AFN='afn';
    const ALL='all';
    const AMD='amd';

    public static function  getAll(): array
    {
        return [
            CurrencyType::USD,
            CurrencyType::AED,
            CurrencyType::AFN,
            CurrencyType::ALL,
            CurrencyType::AMD,
        ];
    }
}
