<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Run extends Model
{
    public $timestamps = false;
    
    protected $fillable = [
        'user_id', 'event_id','duration', 'date', 'start_time', 'end_time', 'start_altitude', 'end_altitude', 'avg_speed', 'distance', 'other_data'
    ];
}