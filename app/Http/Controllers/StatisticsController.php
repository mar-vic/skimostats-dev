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

    public function victories(Request $request, string $year = 'current-season') {
        $years = $this->years($request);

        $queryBuilder = DB::table('rankings')
                      ->join('athletes', 'rankings.athleteId', 'athletes.id')
                      ->join('countries', 'athletes.countryId', 'countries.id')
                      ->join('categories', 'rankings.categoryId', 'categories.id')
                      ->join('race_events as events', 'rankings.raceEventId', 'events.id')
                      ->where('rankings.type', 1)
                      ->whereIn('rankings.categoryId', [1, 2, 3, 4, 23, 24, 25, 26])
                      ->where('rankings.rank', 1)
                      ->select(
                          'events.startDate as eventStartDate',
                          'events.name as eventName',
                          'events.slug as eventSlug',
                          'rankings.rank',
                          'categories.slug as categorySlug',
                          'categories.name as categoryName',
                          'countries.name as countryName',
                          'countries.code as countryCode',
                          'athletes.id as athleteId',
                          'athletes.firstName',
                          'athletes.lastName',
                          'athletes.gender',
                          'athletes.image',
                          'athletes.slug as athleteSlug'
                      );

        if ($year != 'all-seasons') {
            $year = ($year == 'current-season' ? $years->first() : (int)$year);

            if (!$years->contains($year)) {
                return abort(404);
            }

            $timespan = Ranking::getRankingYearTimespan($year);
            $queryBuilder = $queryBuilder->whereBetween('events.startDate', $timespan);
        }

        // dd($queryBuilder->get()->where('firstName', 'Rémi')->where('lastName', 'Bonnet'));

        $groupedByCategories = $queryBuilder->get()->groupBy('categoryName');

        $groupedByAthletes = $groupedByCategories->map(function ($item, $key) {
            return $item->groupBy('athleteId')->map(function ($item, $key) {
                return collect([
                    'athleteId' => $key,
                    'firstName' => $item[0]->firstName,
                    'lastName' => $item[0]->lastName,
                    'gender' => $item[0]->gender,
                    'profilePic' => $item[0]->image,
                    'slug' => $item[0]->athleteSlug,
                    'country' => $item[0]->countryCode,
                    'qty' => $item->count(),
                ]);
            })->sortBy([['qty', 'desc']]);
        });

        $categories = collect(['Men', 'Women', 'Men U23', 'Women U23', 'U20 Men', 'U20 Women', 'U18 Men', 'U18 Women'])
                    ->filter(function ($item, $key) use($groupedByAthletes) {
                        return $groupedByAthletes->keys()->contains($item);
        });

        return view('front.statistics.athletes_seasonal', [
            'statsCategorySlug' => 'victories',
            'data' => $groupedByAthletes->map(function ($item) {
                return $item->slice(0, 30);
            }),
            'year' => $year,
            'years' =>  $years,
            'categories' => $categories,
            'metric' => ['wins', '']
        ]);
    }
}
