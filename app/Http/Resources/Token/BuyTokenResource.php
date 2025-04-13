<?php

namespace App\Http\Resources\Token;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BuyTokenResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>
        ];
    }
}
