<?php
namespace App\Enums\Customer;
enum EmploymentStatus{
    const EMPLOYED='employed';
    const BUSINESS_OWNER='business_owner';
    const FREELANCER='freelancer';
    const STUDENT='student';
    const RETIRED='retired';
    const OTHER='other';

    public static function  getAll(): array
    {
        return [
            EmploymentStatus::EMPLOYED,
            EmploymentStatus::BUSINESS_OWNER,
            EmploymentStatus::RETIRED,
            EmploymentStatus::FREELANCER,
            EmploymentStatus::STUDENT,
            EmploymentStatus::OTHER,
        ];
    }
}
