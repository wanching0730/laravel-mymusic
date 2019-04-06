<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    protected $fillable = [
        'name', 'creationDate', 'imageUrl', 'user_id'
    ];

    public function songs()
    {
        return $this->hasMany(Song::class);
    }
}