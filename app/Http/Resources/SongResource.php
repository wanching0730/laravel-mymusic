<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class SongResource extends Resource
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
            'creationDate' => $this->creationDate,
            'genre' => $this->genre,
            'origin' => $this->origin,
            'duration' => $this->duration,
            'artist' => new ArtistResource($this->whenLoaded('artist')),
            'albums' => new AlbumCollection($this->whenLoaded('albums'))
        ];
    }
}
