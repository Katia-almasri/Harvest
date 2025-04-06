<?php

namespace App\Http\Resources\Spv;

use App\Http\Resources\General\MediaResource;
use App\Http\Resources\RealEstate\RealEstateResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SpvResource extends JsonResource
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
            'name'=>$this->name,
            'registration_number'=>$this->registration_number,
            'legal_document'=>new MediaResource($this->legalDocument),
        ];
    }
}
