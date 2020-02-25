<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    public $timestamps = false;


    protected $fillable = [
        'device_name'
    ];
}
