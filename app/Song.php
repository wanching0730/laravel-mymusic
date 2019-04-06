<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    protected $fillable = [
        'name', 'creationDate', 'genre', 'origin', 'duration', 'artist_id'
    ];

    public function artist()
    {
        return $this->belongsTo(Artist::class);
    }

    public function albums() {
        return $this->belongsToMany(Album::class);
    }
}
