<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    protected $fillable = [
        'name', 'creationDate', 'genre', 'origin', 'duration', 'album_id'
    ];

    public function album()
    {
        return $this->belongsTo(Album::class);
    }

    public function artists() {
        return $this->belongsToMany(Artist::class);
    }
}
