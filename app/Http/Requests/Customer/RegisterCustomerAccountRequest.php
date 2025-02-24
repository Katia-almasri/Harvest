<?php

namespace App\Http\Requests\Customer;

use App\Enums\Customer\FamilyStatus;
use App\Enums\Customer\Gender;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterCustomerAccountRequest extends FormRequest
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
        return [
            'first_name' => 'required',
            'last_name' => 'required',
            'father_name' => 'required',
            'phone_number' => 'string|min:8|max:14|regex:/^\+\d{1,3}\d{6,15}$/',
            'family_status'=> ['string', Rule::in(FamilyStatus::getAll())],
            'gender'=> ['string', Gender::getAll()],
        ];
    }
}
