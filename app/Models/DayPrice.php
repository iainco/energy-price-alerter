<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DayPrice extends Model
{
    protected $fillable = [
        'day',
        'price',
    ];
}
