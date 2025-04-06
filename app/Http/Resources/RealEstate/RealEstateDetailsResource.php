<?php

namespace App\Http\Resources\RealEstate;

use App\Http\Resources\General\CityResource;
use App\Http\Resources\General\MediaResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RealEstateDetailsResource extends JsonResource
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
            'description'=>$this->description??"",
            'area'=>$this->area,
            'city'=>new CityResource($this->city),
            'bedroom_number'=>boolval($this->bedroom_number),
            'bathroom_number'=>boolval($this->bathroom_number),
            'status'=>$this->status,
            'category'=>$this->category,
            'purchase_price'=>$this->purchase_price,
            'created_by'=> $this->admin->name,
            'has_pool'=>boolval($this->has_pool),
            'has_garden'=>boolval($this->has_garden),
            'has_parking'=>boolval($this->has_parking),
            'close_to_transportation'=>boolval($this->close_to_transportation),
            'close_to_hospital'=>boolval($this->close_to_hospital),
            'close_to_school'=> boolval($this->close_to_school),
            'location'=>$this->location??"",
            'longitude'=>$this->longitude,
            'latitude'=>$this->latitude,
            'images'=> MediaResource::collection($this->images()->get()),
            'documents'=> MediaResource::collection($this->documents()->get()),
            'has_spv'=> $this->spv()?true:false,

        ];
    }
}
