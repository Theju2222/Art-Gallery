<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Address extends Model
{
    protected $fillable = [
        'name',
        'mobile',
        'street',
        'landmark',
        'city',
        'state',
        'pin_code',
        'country',
        'type',
        

    ];  
}
