<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'team_id', 'event_name', 'event_date', 'start_time', 'end_time'
    ];
}
