<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    public static function getImagePath($country) {
        return '/images/flags/flags-mini/'.strtolower($country->code).'.png';
    }

    public function getAthleteCount() {
        return Athlete::where('countryId', $this->id)->count();
    }
}
