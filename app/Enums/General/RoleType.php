<?php

namespace App\Enums\General;

enum RoleType
{
    const ADMIN='admin';
    const CUSTOMER='customer';

    public static function  getAll(): array
    {
        return [
            RoleType::ADMIN,
            RoleType::CUSTOMER
        ];
    }
}
