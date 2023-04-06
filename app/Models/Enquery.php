<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enquery extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'from_bhk',
        'from_floor',
        'from_lift',
        'from_location',
        'to_bhk',
        'to_floor',
        'to_lift',
        'to_floor',
        'to_location',
        'date'

    ];
}
