<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class UserResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id, 
            'name' => $this->name,
            'email' => $this->email,
            'abilities' => new AbilityCollection($this->getAbilities()),
            'albums' => new AlbumCollection($this->whenLoaded('albums'))
        ];
    }
}
