<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Season extends Model
{
    public $timestamps = false;
    
    protected $fillable = [
        'org_id', 'season_name', 'season_description', 'season_start', 'season_end'
    ];
}
