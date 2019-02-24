<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ArtistResource extends Resource
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
            'gender' => $this->gender,
            'age' => $this->age,
            'nationality' => $this->nationality,
            'songs' => new SongCollection($this->whenLoaded('songs'))
        ];
    }
}
