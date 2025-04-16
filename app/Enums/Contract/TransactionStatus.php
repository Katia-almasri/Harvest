<?php
namespace App\Enums\Contract;

enum TransactionStatus: string{
    case PENDING = 'pending';
    case RETRIED = 'retries';

    public static function  getAll(): array
    {
        return [
            TransactionStatus::PENDING,
            TransactionStatus::RETRIED
        ];
    }
}
