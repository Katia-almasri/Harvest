<?php

namespace App\Http\Requests\RealEstate;

use App\Enums\RealEstate\RealEstateCategory;
use App\Enums\RealEstate\RealEstateStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRealEstateRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'name' => 'required',
            'description' => 'nullable',
            'area' => 'numeric|gt:0',
            'city_id' => 'exists:cities,id',
            'bedroom_number' => 'numeric|gt:0',
            'bathroom_number' => 'numeric|gt:0',
            'status'=>['required', Rule::in(RealEstateStatus::getAll())],
            'category'=>['required', Rule::in(RealEstateCategory::getAll())],
            'purchase_price'=>'numeric|gt:0',
            'has_pool'=>'boolean',
            'has_garden'=>'boolean',
            'has_parking'=>'boolean',
            'close_to_hospital'=>'boolean',
            'close_to_transportation'=>'boolean',
            'close_to_school'=>'boolean',
            'location'=>'required',
            'longitude' => 'required|numeric|between:-180,180',
            'latitude' => 'required|numeric|between:-90,90',

        ];
    }
}
