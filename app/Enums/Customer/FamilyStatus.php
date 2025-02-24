<?php
namespace App\Enums\Customer;
enum FamilyStatus{
    const SINGLE='single';
    const MARRIED='married';

    public static function  getAll(): array
    {
        return [
            FamilyStatus::SINGLE,
            FamilyStatus::MARRIED
        ];
    }
}
