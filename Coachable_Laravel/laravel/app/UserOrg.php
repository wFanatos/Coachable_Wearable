<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserOrg extends Model
{
    protected $fillable = [
        'org_id', 'user_id'
    ];
}
