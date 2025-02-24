<?php

namespace App\Http\Requests\General\Profile;

use App\Rules\General\Auth\MatchCurrentPassword;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmailRequest extends FormRequest
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
            'email'=> Rule::unique('users', 'email')->ignore(auth()->id()),
            'password' => ['required', new MatchCurrentPassword()],
        ];
    }
}
