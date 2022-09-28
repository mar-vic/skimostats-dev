<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Doctrine\DBAL\Query\QueryBuilder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;

use App\Ranking;
use App\Helpers;
use App\Helpers\CollectionHelper;

class StatisticsController extends Controller
{
    public function years(Request $request) {
        $queryBuilder = DB::table('race_events')
                      ->select('race_events.year')
                      ->whereRaw('race_events.startDate < CURRENT_DATE')
                      ->groupBy('race_events.year')
                      ->orderBy('race_events.year', 'desc');
        return $queryBuilder->get()->map(function ($item) { return $item->year; });
    }

    public function mostWins(Request $request, $year = null) {

        $queryBuilder = DB::table('athletes')
            ->selectRaw('athletes.id as athleteId, athletes.firstName, athletes.lastName, athletes.image, athletes.slug, countries.code as countryCode, categories.name as catName, race_events.startDate')
            ->join('rankings', 'rankings.athleteId', 'athletes.id')
            ->join('categories', 'categories.id', 'rankings.categoryId')
            ->join('race_events', 'race_events.id', 'rankings.raceEventId')
            ->leftJoin('countries', 'countries.id', 'athletes.countryId')
            ->where('rankings.rank', 1);

        if ($year) {
            $timespan = Ranking::getRankingYearTimespan($year);
            $queryBuilder = $queryBuilder->whereBetween('race_events.startDate', $timespan);
        }

        $groupedByCategories = $queryBuilder->get()->groupBy('catName');

        $groupedByAthletes = $groupedByCategories->map(function ($item, $key) {
            return $item->groupBy('athleteId')->map(function ($item, $key) {
                return collect([
                    'athleteId' => $key,
                    'firstName' => $item[0]->firstName,
                    'lastName' => $item[0]->lastName,
                    'profilePic' => $item[0]->image,
                    'slug' => $item[0]->slug,
                    'country' => $item[0]->countryCode,
                    'winsCount' => $item->count()
                ]);
            })->sortBy([['winsCount', 'desc']]);
        });

        return $groupedByAthletes;
    }

    public function mostWinsWithPagi(Request $request, $year = null)
    {

        $queryBuilder = DB::table('athletes')
            ->selectRaw('athletes.id as athleteId, athletes.firstName, athletes.lastName, categories.name as catName, race_events.startDate')
            ->join('rankings', 'rankings.athleteId', 'athletes.id')
            ->join('categories', 'categories.id', 'rankings.categoryId')
            ->join('race_events', 'race_events.id', 'rankings.raceEventId')
            ->where('rankings.rank', 1);

        if ($year) {
            $timespan = Ranking::getRankingYearTimespan($year);
            $queryBuilder = $queryBuilder->whereBetween('race_events.startDate', $timespan);
        }

        $groupedByCategories = $queryBuilder->get()->groupBy('catName');

        $groupedByAthletes = $groupedByCategories->map(function ($item, $key) {
            $col =  $item->groupBy('athleteId')->map(function ($item, $key) {
                return collect([
                    'athleteId' => $key,
                    'firstName' => $item[0]->firstName,
                    'lastName' => $item[0]->lastName,
                    'winsCount' => $item->count()
                ]);
            })->sortBy([['winsCount', 'desc']]);

            // dd($col);

            $paginated = CollectionHelper::paginate($col, 50);

            // dd($paginated);

            return $paginated;
        });

        // dd($groupedByAthletes);

        return $groupedByAthletes;
    }
}
