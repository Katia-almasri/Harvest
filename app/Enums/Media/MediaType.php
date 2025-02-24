<?php

namespace App\Enums\Media;

enum MediaType
{
    const IMAGE = 'image';

    public static function  getAll(): array
    {
        return [
            MediaType::IMAGE
        ];
    }
}
