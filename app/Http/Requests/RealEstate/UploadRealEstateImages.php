<?php

namespace App\Http\Requests\RealEstate;

use Illuminate\Foundation\Http\FormRequest;

class UploadRealEstateImages extends FormRequest
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
     * @requestMediaType multipart/form-data
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'images' => 'array|required',
            'images.*' => 'mimes:jpeg,png,jpg,gif,svg|max:2048|file',
        ];
    }
}
