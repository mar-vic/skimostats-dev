<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

// Adding HasFactory trait class
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RaceEventEntry extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function participant() {
        return $this->belongsTo('App\RaceEventParticipant', 'raceEventParticipantId');
    }

    public function team() {
        return $this->belongsTo('App\RaceEventTeam', 'raceEventTeamId');
    }

    public function getParticipants() {
        if ($this->participant) {
            return [$this->participant];
        } else if ($this->team) {
            return $this->team->participants;
        }

        return [];
    }
}
