<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Artist extends Model
{
    protected $fillable = [
        'name', 'gender', 'age', 'nationality'
    ];

    public function songs() {
        return $this->belongsToMany(Song::class);
    }
}
