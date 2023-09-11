<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

// Adding HasFactory trait class
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ranking extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'obtainedAt' => 'datetime',
        'rank' => 'integer',
    ];

    private static $ismf_points_table = [
        1 => 100,
        2 => 90,
        3 => 81,
        4 => 73,
        5 => 66,
        6 => 60,
        7 => 57,
        8 => 54,
        9 => 51,
        10 => 48,
        11 => 45,
        12 => 43,
        13 => 41,
        14 => 39,
        15 => 37,
        16 => 35,
        17 => 34,
        18 => 33,
        19 => 32,
        20 => 31,
        21 => 30,
        22 => 29,
        23 => 28,
        24 => 27,
        25 => 26,
        26 => 25,
        27 => 24,
        28 => 23,
        29 => 22,
        30 => 21,
        31 => 20,
        32 => 19,
        33 => 18,
        34 => 17,
        35 => 16,
        36 => 15,
        37 => 14,
        38 => 13,
        39 => 12,
        40 => 11,
        41 => 10,
        42 => 9,
        43 => 8,
        44 => 7,
        45 => 6,
        46 => 5,
        47 => 4,
        48 => 3,
        49 => 2,
        50 => 1,
    ];

    public static function mapIsmfPointsToRank($rank) {
        $table = self::$ismf_points_table;
        if (isset($table[$rank])) {
            return $table[$rank];
        }

        return 0;
    }

    public static function getRankingYearTimeSpan($year = null) {
        if (!$year) {
            $year = self::getCurrentRankingYear();
        }

        return [
            ($year - 1) . '-08-01 00:00:00',
            $year . '-07-31 23:59:59'
        ];
    }

    public static function getCurrentRankingYear() {
        $month = date("m");
        $year = date("Y");
        if ($month > 9) {
            $year++;
        }

        return $year;
    }

    public function event() {
        return $this->belongsTo('App\RaceEvent', 'raceEventId');
    }

    public function raceType() {
        return $this->belongsTo('App\RaceType', 'raceTypeId');
    }

    public function category() {
        return $this->belongsTo('App\Category', 'categoryId');
    }
}
