<?php

namespace App\Http\Requests\General\Media;

use App\Enums\Media\MediaCollectionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreImageRequest extends FormRequest
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
            'image' =>  ['required', 'file', 'mimes:jpg,jpeg,png,gif,svg'],
            'media_collection_type'=> ['string', Rule::in(MediaCollectionType::CUSTOMER_PASSPORT, MediaCollectionType::RESIDENTIAL_CARD)]
        ];
    }
}
