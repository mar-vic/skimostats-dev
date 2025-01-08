<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeatEntry extends Model
{
    use HasFactory;

    protected $fillable = ['heatId', 'athleteName', 'nationality', 'timeRaw', 'time', 'rank'];
}
