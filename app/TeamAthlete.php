<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

// Adding HasFactory trait class
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TeamAthlete extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function athlete() {
        return $this->belongsTo('App\Athlete', 'athleteId');
    }
}
