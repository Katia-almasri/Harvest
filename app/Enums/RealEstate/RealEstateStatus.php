<?php

namespace App\Enums\RealEstate;

enum RealEstateStatus
{
    const ACTIVE = 'active';
    const INACTIVE = 'inactive';
    const CLOSED = 'closed';
    const SOLD = 'sold';


    public static function  getAll(): array
    {
        return [
            RealEstateStatus::ACTIVE,
            RealEstateStatus::INACTIVE,
            RealEstateStatus::CLOSED,
            RealEstateStatus::SOLD,
        ];
    }
}
