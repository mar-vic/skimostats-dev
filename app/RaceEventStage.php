<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

// Adding HasFactory trait class
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RaceEventStage extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function raceEvent() {
        return $this->belongsTo('App\RaceEvent', 'raceEventId');
    }

    public function raceType() {
        return $this->belongsTo('App\RaceType', 'raceTypeId');
    }

    public function scopeGetByEventAndType($query, RaceEvent $event, RaceType $type) {
        return $query->where('raceEventId', $event->id)->where('raceTypeId', $type->id);
    }
}
