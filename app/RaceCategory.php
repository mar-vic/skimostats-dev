<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RaceCategory extends Model
{
    protected $guarded = ['id'];

    public function race() {
        return $this->belongsTo('App\Race', 'raceId');
    }

    public function raceType() {
        return $this->belongsTo('App\RaceType', 'raceTypeId');
    }
}
