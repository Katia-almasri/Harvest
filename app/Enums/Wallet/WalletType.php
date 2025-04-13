<?php
namespace App\Enums\Wallet;
enum WalletType: string
{
    const WEB3_AUTH = 'web3_auth';

    public static function  getAll(): array
    {
        return [
            WalletType::WEB3_AUTH,
        ];
    }
}
