<?php

namespace App\Enums\RealEstate;

enum RealEstateCategory
{
    const COMMERCIAL = 'commercial';
    const RESIDENTIAL = 'residential';

    public static function  getAll(): array
    {
        return [
            RealEstateCategory::COMMERCIAL,
            RealEstateCategory::RESIDENTIAL
        ];
    }
}
