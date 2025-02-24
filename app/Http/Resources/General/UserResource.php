<?php

namespace App\Http\Resources\General;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=> $this->id,
            'name'=> $this->name,
            'email'=> $this->email,
            'status'=> boolval($this->status),
            'role'=> $this->getRole(),
            'image'=> new MediaResource($this->image()),
        ];
    }

    public function getRole(): array
    {
        $role = $this->roles[0];
        return [
            "id" => $role->id,
            "name" => $role->name,
        ];
    }
}
