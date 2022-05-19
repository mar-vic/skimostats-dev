<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

// Adding HasFactory trait class
use Illuminate\Database\Eloquent\Factories\HasFactory;


class RaceEventCategory extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
}
