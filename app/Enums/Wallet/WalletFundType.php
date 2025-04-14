<?php
namespace App\Enums\Wallet;
enum WalletFundType: string{
    case SPV_WALLET_CREATION_FUND = '0.01';

    public static function  getAll(): array
    {
        return [
            WalletFundType::SPV_WALLET_CREATION_FUND,
        ];
    }
}
