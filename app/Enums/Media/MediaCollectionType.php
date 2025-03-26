<?php
namespace App\Enums\Media;

enum MediaCollectionType
{
    const PROFILE_IMAGE = 'profile_image';
    const REAL_ESTATE_IMAGE = 'real_estate_image';
    const CUSTOMER_PASSPORT = 'customer_passport';
    const RESIDENTIAL_CARD = 'residential_card';
    const REAL_ESTATE_DOCUMENT = 'real_estate_document';
    const SPV_LEGAL_DOCUMENT = 'spv_legal_document';

    public static function  getAll(): array
    {
        return [
            MediaCollectionType::PROFILE_IMAGE,
            MediaCollectionType::REAL_ESTATE_IMAGE,
            MediaCollectionType::CUSTOMER_PASSPORT,
            MediaCollectionType::REAL_ESTATE_DOCUMENT,
            MediaCollectionType::RESIDENTIAL_CARD,
            MediaCollectionType::SPV_LEGAL_DOCUMENT,
        ];
    }
}
