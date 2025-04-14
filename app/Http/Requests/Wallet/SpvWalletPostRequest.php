<?php

namespace App\Http\Requests\Wallet;

use App\Enums\Wallet\WalletFundType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SpvWalletPostRequest extends FormRequest
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
            'admin_wallet_address' => 'required|string',
            'amount' => ['required', 'string', Rule::in(WalletFundType::getAll())],
        ];
    }
}
