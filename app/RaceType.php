<?php

namespace App;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;

// Adding HasFactory trait class
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RaceType extends Model
{
    use HasFactory, Sluggable;

    protected $guarded = ['id'];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
            ],
        ];
    }

    private static $defaultTypes = [
        [
            'name' => 'Individual race',
            'isTeam' => false,
            'type' => 1,
        ],
        [
            'name' => 'Vertical race',
            'isTeam' => false,
            'type' => 2,
        ],
        [
            'name' => 'Sprint race',
            'isTeam' => false,
            'type' => 3,
        ],
        [
            'name' => 'Team race',
            'isTeam' => true,
            'type' => 4,
        ],
    ];

    public static function getDefaultTypes() {
        return self::$defaultTypes;
    }

    public function isTeamType() {
        return (bool)$this->isTeam;
    }
}
