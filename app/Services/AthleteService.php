<?php

namespace App\Services;

use App\Athlete;
use App\Ranking;
use Illuminate\Support\Facades\DB;

class AthleteService {
    public function getPointsPerSpecialty(Athlete $athlete) {
        $timespan = Ranking::getRankingYearTimeSpan();

        $pointsPerSpecialty = DB::table('rankings')
                ->select(
                    'race_types.id as raceTypeId',
                    'race_types.name as raceTypeName',
                    DB::raw('SUM(rankings.points) as points'),
                    // sum of all of athlete's points
                    DB::raw('(SELECT SUM(rk.points) as pts FROM rankings as rk WHERE rk.type = 1 && rk.categoryId IN (1,2) && rk.athleteId=rankings.athleteId GROUP BY rk.athleteId ORDER BY pts DESC LIMIT 1) as maxPoints')
                )
                ->join('race_types', 'race_types.id', 'rankings.raceTypeId')
                ->where('rankings.type', 1)
                ->where('rankings.athleteId', $athlete->id)
                ->whereIn('rankings.categoryId', [1, 2])
                // ->whereBetween('rankings.obtainedAt', $timespan)
                ->groupBy('rankings.athleteId')
                ->groupBy('rankings.raceTypeId')
                ->get();

        return $pointsPerSpecialty;
    }
}
