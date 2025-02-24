<?php
namespace App\Enums\Customer;
enum Industry{
    const TECHNOLOGY='technology';
    const FINANCE='finance';
    const HEALTHCARE='healthcare';
    const BANKING='banking';
    const MANUFACTURING='manufacturing';
    const ENGINEERING='engineering';
    const TRADING='trading';
    const REAL_ESTATE='real estate';

    public static function  getAll(): array
    {
        return [
            Industry::TECHNOLOGY,
            Industry::FINANCE,
            Industry::HEALTHCARE,
            Industry::BANKING,
            Industry::MANUFACTURING,
            Industry::ENGINEERING,
            Industry::TRADING,
            Industry::REAL_ESTATE,
        ];
    }
}
