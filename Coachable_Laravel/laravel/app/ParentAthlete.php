<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ParentAthlete extends Model
{
    protected $fillable = [
        'parent_id', 'athlete_id'
    ];
}

