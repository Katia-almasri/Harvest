<?php
namespace App\Enums\General;
enum InvestmentStatus: string
{
    const PENDING_PAYMENT='pending_payment';
    const SUCCEEDED='succeeded';
    const FAILED='failed';

    public static function  getAll(): array
    {
        return [
            InvestmentStatus::PENDING_PAYMENT,
            InvestmentStatus::SUCCEEDED,
            InvestmentStatus::FAILED,
        ];
    }
}
