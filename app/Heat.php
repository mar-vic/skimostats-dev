<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Heat extends Model
{
    use HasFactory;

    public function entries() {
        return $this->hasMany("App\HeatEntry", "heatId");
    }
}
