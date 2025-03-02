<?php
namespace App\Enums\Customer;
enum MainFundType{
    const SALARY='salary';
    const INVESTMENT='investment';
    const RENTAL_INCOME='rental_income';
    const AFFILIATE_MARKETING='affiliate_marketing';
    const LOANS='loans';
    const INHERITED_WEALTH='inherited_wealth';
    const SCHOLARSHIPS='scholarships';
    const FAMILY_AID='real family aid';
    const OTHER='other';

    public static function  getAll(): array
    {
        return [
            MainFundType::SALARY,
            MainFundType::INVESTMENT,
            MainFundType::RENTAL_INCOME,
            MainFundType::AFFILIATE_MARKETING,
            MainFundType::LOANS,
            MainFundType::INHERITED_WEALTH,
            MainFundType::SCHOLARSHIPS,
            MainFundType::FAMILY_AID,
            MainFundType::OTHER,
        ];
    }
}
