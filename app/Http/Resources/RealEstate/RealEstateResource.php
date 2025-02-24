<?php

namespace App\Http\Resources\RealEstate;

use App\Http\Resources\General\MediaResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RealEstateResource extends JsonResource
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
            'status'=>$this->status,
            'category'=>$this->category,
            'purchase_price'=>$this->purchase_price,
            'images'=> MediaResource::collection($this->images()->get()),
        ];
    }
}
