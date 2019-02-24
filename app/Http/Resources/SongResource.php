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
            'genre' => $this->imageUrl,
            'origin' => $this->imageUrl,
            'duration' => $this->imageUrl,
            'artists' => new ArtistCollection($this->whenLoaded('artists')),
            'album' => new AlbumResource($this->whenLoaded('album'))
        ];
    }
}
