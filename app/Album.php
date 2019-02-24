<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    protected $fillable = [
        'name', 'creationDate', 'imageUrl'
    ];

    public function songs()
    {
        return $this->hasMany(Song::class);
    }
}
