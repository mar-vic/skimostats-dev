<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

// Adding HasFactory trait class
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Team;

class RaceEventTeam extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function team() {
        return $this->belongsTo('App\Team', 'teamId');
    }

    public function participants() {
        return $this->hasMany('App\RaceEventParticipant', 'raceEventTeamId');
    }

    public function raceEventEntry() {
        return $this->hasOne('App\RaceEventEntry', 'raceEventTeamId');
    }
}
