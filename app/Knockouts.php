<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Knockouts extends Model
{
    use HasFactory;

    protected $fillable = ['raceEventId', 'categoryId'];

    public function raceEvent() {
        return $this->belongsTo('App\RaceEvent', 'raceEventId');
    }

    public function category() {
        return $this->belongsTo('App\Category', 'categoryId');
    }

    public function rounds() {
        return $this->hasMany('App\KnockoutRound', 'knockoutsId');
    }
}
