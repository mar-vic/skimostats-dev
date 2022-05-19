<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

// Adding HasFactory trait class
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RaceEventParticipant extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function athlete() {
        return $this->belongsTo('App\Athlete', 'athleteId');
    }

    public function raceEvent() {
        return $this->belongsTo('App\RaceEvent', 'raceEventId');
    }

    public function raceEventEntry() {
        return $this->hasOne('App\RaceEventEntry', 'raceEventParticipantId');
    }

    public function raceEventTeam() {
        return $this->belongsTo('App\RaceEventTeam', 'raceEventTeamId');
    }

    public function category() {
        return $this->belongsTo('App\Category', 'categoryId');
    }
}
