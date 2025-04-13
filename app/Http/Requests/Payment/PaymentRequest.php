<?php

namespace App\Http\Requests\Payment;

use App\Enums\General\CurrencyType;
use App\Enums\Payment\PaymentMethod;
use App\Enums\Payment\PaymentMethodType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentRequest extends FormRequest
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
            'tokens'=>'required|numeric|min:1',
            'currency' => ['required', 'string', Rule::in(CurrencyType::getAll())],
            'payment_method' => ['required', 'string', Rule::in(PaymentMethod::getAll())],
            'payment_method_type' => ['required', 'string', Rule::in(PaymentMethodType::getAll())],
        ];
    }
}
