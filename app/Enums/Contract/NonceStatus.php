<?php
namespace App\Enums\Contract;

enum NonceStatus: string{
    case Pending = 'pending';

    public static function  getAll(): array
    {
        return [
            NonceStatus::Pending,
        ];
    }
}
