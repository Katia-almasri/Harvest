<?php

namespace App\Http\Requests\Customer;

use App\Enums\Customer\EmploymentStatus;
use App\Enums\Customer\FamilyStatus;
use App\Enums\Customer\Gender;
use App\Enums\Customer\Industry;
use App\Enums\Customer\MainFundType;
use App\Enums\RealEstate\PricesRange;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // From register customer and complete profile
        return [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'father_name' => 'required|string',
            'phone_number' => 'string|min:8|max:14|regex:/^\+\d{1,3}\d{6,15}$/',
            'family_status'=> ['string', Rule::in(FamilyStatus::getAll())],
            'gender'=>['string', Rule::in(Gender::getAll())],
            'longitude' => 'numeric|between:-180,180',
            'latitude' => 'numeric|between:-90,90',
            'employment_status' => ['string', Rule::in(EmploymentStatus::getAll())],
            'industry' => ['string', Rule::in(Industry::getAll())],
            'main_source_of_fund' => ['string', Rule::in(MainFundType::getAll())],
            'minimum_investment_amount' => ['string', Rule::in(PricesRange::getAll())],

        ];
    }
}
