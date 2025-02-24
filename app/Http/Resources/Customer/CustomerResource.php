<?php

namespace App\Http\Resources\Customer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->id,
            'first_name'=>$this->first_name,
            'last_name'=>$this->last_name,
            'father_name'=>$this->father_name,
            'phone_number'=>$this->phone_number??null,
            'family_status'=>$this->family_status??null,
            'gender'=>$this->gender??null,
            'longitude'=>$this->longitude??null,
            'latitude'=>$this->latitude??null,
        ];
    }
}
