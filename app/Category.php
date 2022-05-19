<?php

namespace App;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use Sluggable;
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
            ],
        ];
    }
    //
    private static $ageTypes = [
        [
            'slug' => 'adult',
        ],
        [
            'slug' => 'junior',
        ],
        [
            'slug' => 'cadet',
        ],
    ];

    private static $genders = [
        'male',
        'female',
        'other',
    ];

    private static $defaultCategories = [
        [
            'name' => 'Men',
            'isU23' => false,
            'gender' => 'male',
            'age' => 'adult',
            'parent' => null,
        ],
        [
            'name' => 'Women',
            'isU23' => false,
            'gender' => 'female',
            'age' => 'adult',
            'parent' => null,
        ],
        [
            'name' => 'Men U23',
            'isU23' => true,
            'gender' => 'male',
            'age' => 'adult',
            'parent' => 1,
        ],
        [
            'name' => 'Women U23',
            'isU23' => true,
            'gender' => 'female',
            'age' => 'adult',
            'parent' => 2,
        ],
        [
            'name' => 'U20 Men',
            'isU23' => false,
            'gender' => 'male',
            'age' => 'junior',
            'parent' => null,
        ],
        [
            'name' => 'U20 Women',
            'isU23' => false,
            'gender' => 'female',
            'age' => 'junior',
            'parent' => null,
        ],

        [
            'name' => 'U18 Men',
            'isU23' => false,
            'gender' => 'male',
            'age' => 'cadet',
            'parent' => null,
        ],

        [
            'name' => 'U18 Women',
            'isU23' => false,
            'gender' => 'female',
            'age' => 'cadet',
            'parent' => null,
        ],

        [
            'name' => 'Master Women',
            'isU23' => false,
            'gender' => 'female',
            'age' => 'master',
            'parent' => null,
        ],

        [
            'name' => 'Master Men',
            'isU23' => false,
            'gender' => 'male',
            'age' => 'master',
            'parent' => null,
        ],
    ];

    public static function getDefaultCategories() {
        return self::$defaultCategories;
    }

    public static function getAgeTypes() {
        return self::$ageTypes;
    }

    public static function getGenders() {
        return self::$genders;
    }

    public function isU23() {
        return (bool)$this->isU23;
    }

    public function raceEvents() {
        return $this->belongsToMany('App\RaceEvent', 'race_event_categories', 'categoryId', 'raceEventId')
            ->orderBy('startDate', 'desc');
    }
}
