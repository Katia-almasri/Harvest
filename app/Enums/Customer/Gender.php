<?php
namespace App\Enums\Customer;
enum Gender{
    const MALE='male';
    const FEMALE='female';

    public static function  getAll(): array
    {
        return [
            Gender::FEMALE,
            Gender::MALE
        ];
    }
}
