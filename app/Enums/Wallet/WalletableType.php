<?php
namespace App\Enums\Wallet;
enum WalletableType: string{
    case ADMIN = 'user';
    case SPV = 'spv';
    case CUSTOMER = 'customer';

    public static function  getAll(): array
    {
        return [
            WalletableType::ADMIN,
            WalletableType::SPV,
            WalletableType::CUSTOMER
        ];
    }
}
