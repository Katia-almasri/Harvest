<?php

namespace App\Http\Requests\RealEstate;

use Illuminate\Foundation\Http\FormRequest;

class UploadRealEstateDocuments extends FormRequest
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
            'documents' => 'array|required',
            'documents.*' => 'mimes:jpeg,png,jpg,gif,svg,pdf|max:2048|file',
        ];
    }
}
