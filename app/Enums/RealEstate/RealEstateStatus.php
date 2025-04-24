<?php

namespace App\Enums\RealEstate;

enum RealEstateStatus
{
    const ACTIVE = 'active';
    const INACTIVE = 'inactive';
    const CLOSED = 'closed';
    const PENDING = 'pending'; // this mean it is waiting for some customers to complete payment for example
    const SOLD = 'sold';


    public static function  getAll(): array
    {
        return [
            RealEstateStatus::ACTIVE,
            RealEstateStatus::INACTIVE,
            RealEstateStatus::CLOSED,
            RealEstateStatus::PENDING,
            RealEstateStatus::SOLD,
        ];
    }
}
