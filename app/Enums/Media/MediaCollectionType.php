<?php
namespace App\Enums\Media;

enum MediaCollectionType
{
    const PROFILE_IMAGE = 'profile_image';
    const REAL_ESTATE_IMAGE = 'real_estate_image';
    const REAL_ESTATE_DOCUMENT = 'real_estate_document';

    public static function  getAll(): array
    {
        return [
            MediaCollectionType::PROFILE_IMAGE,
            MediaCollectionType::REAL_ESTATE_IMAGE,
            MediaCollectionType::REAL_ESTATE_DOCUMENT
        ];
    }
}
