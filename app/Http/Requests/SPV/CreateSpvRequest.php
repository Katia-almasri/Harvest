<?php

namespace App\Http\Requests\SPV;

use Illuminate\Foundation\Http\FormRequest;

class CreateSpvRequest extends FormRequest
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
            'name'=>'required',
            'registration_number'=>'required|string|unique:spv,registration_number',
            'legal_document'=>'required|file|mimes:pdf',
        ];
    }
}
