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

        $queryBuilder = DB::table('race_events as events')
            ->select(
                'athletes.id as athleteId',
                'athletes.firstName',
                'athletes.lastName',
                'athletes.image',
                'athletes.slug',
                'athletes.gender',
                'countries.code as countryCode',
                'categories.name as catName',
                'events.startDate as eventStartDate',
                'events.name as eventName'
            )
            ->join('race_event_participants as participants', 'participants.raceEventId', 'events.id')
            ->join('athletes', 'participants.athleteId', 'athletes.id')
            ->join('categories', 'categories.id', 'participants.categoryId')
            ->leftJoin('countries', 'countries.id', 'events.countryId')
            ->leftJoin('race_event_teams as teams', 'teams.id', 'participants.raceEventTeamId')
            ->join('race_event_entries as entries', function($qb) {
                $qb->on('entries.raceEventParticipantId', '=', 'participants.id')
                    ->orOn('entries.raceEventTeamId', '=', 'teams.id');
            })
            ->leftJoin('rankings', function($qb) {
                $qb->on('rankings.participantId', '=', 'participants.id')
                    ->where('rankings.type', 1)
                    ->whereIn('rankings.categoryId', [1, 2]);
            })
            ->where('entries.rank', 1)
            ->whereRaw('rankings.rankingCategoryId in (1, 2, 4, 5, 6, 7, 8, 9, 10, 13)')
            ->groupBy('events.id');

        // dd($builder->get());

        // $queryBuilder = DB::table('athletes')
        //     ->selectRaw('athletes.id as athleteId, athletes.firstName, athletes.lastName, athletes.image, athletes.slug, athletes.gender, countries.code as countryCode, categories.name as catName, race_events.startDate')
        //     ->join('rankings', 'rankings.athleteId', 'athletes.id')
        //     ->join('categories', 'categories.id', 'rankings.categoryId')
        //     ->join('race_events', 'race_events.id', 'rankings.raceEventId')
        //     ->leftJoin('countries', 'countries.id', 'athletes.countryId')
        //     ->where('rankings.rank', 1);

        if ($year) {
            $timespan = Ranking::getRankingYearTimespan($year);
            $queryBuilder = $queryBuilder->whereBetween('events.startDate', $timespan);
        }

        // dd($queryBuilder->get()->filter(function ($value, $key) {
        //     return $value->firstName == 'Rémi' and $value->lastName == "Bonnet";
        // }));

        $groupedByCategories = $queryBuilder->get()->groupBy('catName');

        // dd($groupedByCategories);

        $groupedByAthletes = $groupedByCategories->map(function ($item, $key) {
            return $item->groupBy('athleteId')->map(function ($item, $key) {
                return collect([
                    'athleteId' => $key,
                    'firstName' => $item[0]->firstName,
                    'lastName' => $item[0]->lastName,
                    'gender' => $item[0]->gender,
                    'profilePic' => $item[0]->image,
                    'slug' => $item[0]->slug,
                    'country' => $item[0]->countryCode,
                    'qty' => $item->count(),
                ]);
            })->sortBy([['qty', 'desc']]);
        });

        // dd($groupedByAthletes);

        return $groupedByAthletes;
    }

    public function mostRaceDays(Request $request, $year = null) {

        $queryBuilder = DB::table('athletes')
            ->selectRaw('athletes.id as athleteId, athletes.firstName, athletes.lastName, athletes.image, athletes.slug, athletes.gender, countries.code as countryCode, categories.name as catName, race_events.startDate, race_events.endDate, DATEDIFF(race_events.endDate, race_events.startDate) + 1 as eventDuration')
            ->join('race_event_participants', 'athletes.id', 'race_event_participants.athleteId')
            ->join('race_events', 'race_events.id', 'race_event_participants.raceEventId')
            ->join('race_event_entries', 'race_event_entries.raceEventParticipantId', 'race_event_participants.id' )
            ->join('categories', 'categories.id', 'race_event_entries.categoryId')
            ->join('countries', 'countries.id', 'athletes.countryId')
            ->whereRaw('categories.id in (select distinct rankings.categoryId from rankings inner join categories on categories.id = rankings.categoryId)')
            ->whereRaw('DATEDIFF(race_events.endDate, race_events.startDate) + 1 > 0');

        // dd($queryBuilder->get());

        if ($year) {
            $timespan = Ranking::getRankingYearTimespan($year);
            $queryBuilder = $queryBuilder->whereBetween('race_events.startDate', $timespan);
        }

        // dd($queryBuilder->get());

        $groupedByCategories = $queryBuilder->get()->groupBy('catName');

        // dd($groupedByCategories);

        $groupedByAthletes = $groupedByCategories->map(function ($item, $key) {
            return $item->groupBy('athleteId')->map(function ($item, $key) {

                $raceDays = $item->reduce(function ($carry, $item) {
                    return $carry + $item->eventDuration;
                }, 0);

                // dd($raceDays);

                // if ($raceDays >= 0) {
                    return collect([
                        'athleteId' => $key,
                        'firstName' => $item[0]->firstName,
                        'lastName' => $item[0]->lastName,
                        'gender' => $item[0]->gender,
                        'profilePic' => $item[0]->image,
                        'slug' => $item[0]->slug,
                        'country' => $item[0]->countryCode,
                        'qty' => $raceDays,
                    ]);
                // }
            })->sortBy([['qty', 'desc']]);
        });

        return $groupedByAthletes;
    }

    public function mostVerticalMeters(Request $request, $year = null) {

        $queryBuilder = DB::table('athletes')
                      ->selectRaw('athletes.id as athleteId, athletes.firstName, athletes.lastName, athletes.image, athletes.slug, athletes.gender, countries.code as countryCode, categories.name as catName, race_events.startDate, race_events.elevation')
                      ->join('race_event_participants', 'athletes.id', 'race_event_participants.athleteId')
                      ->join('race_events', 'race_events.id', 'race_event_participants.raceEventId')
                      ->join('race_event_entries', 'race_event_entries.raceEventParticipantId', 'race_event_participants.id' )
                      ->join('categories', 'categories.id', 'race_event_entries.categoryId')
                      ->join('countries', 'countries.id', 'athletes.countryId')
                      ->whereRaw('categories.id in (select distinct rankings.categoryId from rankings inner join categories on categories.id = rankings.categoryId)')
                      ->whereRaw('race_events.elevation is not null');

        // dd($queryBuilder->get());

        if ($year) {
            $timespan = Ranking::getRankingYearTimespan($year);
            $queryBuilder = $queryBuilder->whereBetween('race_events.startDate', $timespan);
        }

        // dd($queryBuilder->get());

        $groupedByCategories = $queryBuilder->get()->groupBy('catName');

        // dd($groupedByCategories);

        $groupedByAthletes = $groupedByCategories->map(function ($item, $key) {
            return $item->groupBy('athleteId')->map(function ($item, $key) {

                $totalElevation = $item->reduce(function ($carry, $item) {
                    return $carry + $item->elevation;
                }, 0);

                // dd($raceDays);

                // if ($raceDays >= 0) {
                    return collect([
                        'athleteId' => $key,
                        'firstName' => $item[0]->firstName,
                        'lastName' => $item[0]->lastName,
                        'gender' => $item[0]->gender,
                        'profilePic' => $item[0]->image,
                        'slug' => $item[0]->slug,
                        'country' => $item[0]->countryCode,
                        'qty' => $totalElevation,
                    ]);
                // }
            })->sortBy([['qty', 'desc']]);
        });

        return $groupedByAthletes;
    }

    public function mostGrandeCourseWins(Request $request, $year = null)
    {
        $queryBuilder = DB::table('athletes')
            ->selectRaw('athletes.id as athleteId, athletes.firstName, athletes.lastName, athletes.image, athletes.slug, athletes.gender, countries.code as countryCode, categories.name as catName, race_events.startDate')
            ->join('rankings', 'rankings.athleteId', 'athletes.id')
            ->join('categories', 'categories.id', 'rankings.categoryId')
            ->join('race_events', 'race_events.id', 'rankings.raceEventId')
            ->leftJoin('countries', 'countries.id', 'athletes.countryId')
            ->where('rankings.rank', 1)
            ->where('rankings.categoryId', 6);

        // dd($queryBuilder->get());

        if ($year) {
            $timespan = Ranking::getRankingYearTimespan($year);
            $queryBuilder = $queryBuilder->whereBetween('race_events.startDate', $timespan);
        }

        $groupedByCategories = $queryBuilder->get()->groupBy('catName');

        // dd($groupedByCategories);

        $groupedByAthletes = $groupedByCategories->map(function ($item, $key) {
            return $item->groupBy('athleteId')->map(function ($item, $key) {

                // dd($item);

                return collect([
                    'athleteId' => $key,
                    'firstName' => $item[0]->firstName,
                    'lastName' => $item[0]->lastName,
                    'gender' => $item[0]->gender,
                    'profilePic' => $item[0]->image,
                    'slug' => $item[0]->slug,
                    'country' => $item[0]->countryCode,
                    'qty' => $item->count(),
                ]);
            })->sortBy([['qty', 'desc']]);
        });

        // dd($groupedByAthletes);

        return $groupedByAthletes;
    }

    public function mostWorldCupWins(Request $request, $year = null)
    {
        $queryBuilder = DB::table('athletes')
            ->selectRaw('athletes.id as athleteId, athletes.firstName, athletes.lastName, athletes.image, athletes.slug, athletes.gender, countries.code as countryCode, categories.name as catName, race_events.startDate')
            ->join('rankings', 'rankings.athleteId', 'athletes.id')
            ->join('categories', 'categories.id', 'rankings.categoryId')
            ->join('race_events', 'race_events.id', 'rankings.raceEventId')
            ->leftJoin('countries', 'countries.id', 'athletes.countryId')
            ->where('rankings.rank', 1)
            ->where('rankings.categoryId', 1);

        // dd($queryBuilder->get());

        if ($year) {
            $timespan = Ranking::getRankingYearTimespan($year);
            $queryBuilder = $queryBuilder->whereBetween('race_events.startDate', $timespan);
        }

        $groupedByCategories = $queryBuilder->get()->groupBy('catName');

        // dd($groupedByCategories);

        $groupedByAthletes = $groupedByCategories->map(function ($item, $key) {
            return $item->groupBy('athleteId')->map(function ($item, $key) {

                // dd($item);

                return collect([
                    'athleteId' => $key,
                    'firstName' => $item[0]->firstName,
                    'lastName' => $item[0]->lastName,
                    'gender' => $item[0]->gender,
                    'profilePic' => $item[0]->image,
                    'slug' => $item[0]->slug,
                    'country' => $item[0]->countryCode,
                    'qty' => $item->count(),
                ]);
            })->sortBy([['qty', 'desc']]);
        });

        // dd($groupedByAthletes);

        return $groupedByAthletes;
    }

    public function mostChocolates(Request $request, $year = null) {

        $queryBuilder = DB::table('athletes')
            ->selectRaw('athletes.id as athleteId, athletes.firstName, athletes.lastName, athletes.image, athletes.slug, athletes.gender, countries.code as countryCode, categories.name as catName, race_events.startDate')
            ->join('rankings', 'rankings.athleteId', 'athletes.id')
            ->join('categories', 'categories.id', 'rankings.categoryId')
            ->join('race_events', 'race_events.id', 'rankings.raceEventId')
            ->leftJoin('countries', 'countries.id', 'athletes.countryId')
            ->where('rankings.rank', 4);

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
                    'gender' => $item[0]->gender,
                    'profilePic' => $item[0]->image,
                    'slug' => $item[0]->slug,
                    'country' => $item[0]->countryCode,
                    'qty' => $item->count(),
                ]);
            })->sortBy([['qty', 'desc']]);
        });

        return $groupedByAthletes;
    }

    public function mostTopTens(Request $request, $year = null) {

        $queryBuilder = DB::table('athletes')
            ->selectRaw('athletes.id as athleteId, athletes.firstName, athletes.lastName, athletes.image, athletes.slug, athletes.gender, countries.code as countryCode, categories.name as catName, race_events.startDate')
            ->join('rankings', 'rankings.athleteId', 'athletes.id')
            ->join('categories', 'categories.id', 'rankings.categoryId')
            ->join('race_events', 'race_events.id', 'rankings.raceEventId')
            ->leftJoin('countries', 'countries.id', 'athletes.countryId')
            ->whereRaw('rankings.rank in (1, 2, 3, 4, 5, 6, 7, 8, 9, 10)');

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
                    'gender' => $item[0]->gender,
                    'profilePic' => $item[0]->image,
                    'slug' => $item[0]->slug,
                    'country' => $item[0]->countryCode,
                    'qty' => $item->count(),
                ]);
            })->sortBy([['qty', 'desc']]);
        });

        return $groupedByAthletes;
    }

    public function activeAthletes(Request $request, $raceCat = null) {

        $queryBuilder = DB::table('athletes')
            ->selectRaw('athletes.id as athleteId, athletes.firstName, athletes.lastName, athletes.image, athletes.slug, athletes.gender, countries.code as countryCode, categories.name as catName, race_events.startDate, race_events.endDate, DATEDIFF(race_events.endDate, race_events.startDate) + 1 as eventDuration')
            ->join('race_event_participants', 'athletes.id', 'race_event_participants.athleteId')
            ->join('race_events', 'race_events.id', 'race_event_participants.raceEventId')
            ->join('race_event_entries', 'race_event_entries.raceEventParticipantId', 'race_event_participants.id' )
            ->join('categories', 'categories.id', 'race_event_entries.categoryId')
            ->join('countries', 'countries.id', 'athletes.countryId')
            ->join('races', 'races.id', 'race_events.raceId')
            ->whereRaw('categories.id in (select distinct rankings.categoryId from rankings inner join categories on categories.id = rankings.categoryId)')
            ->whereRaw('DATEDIFF(race_events.endDate, race_events.startDate) + 1 > 0')
            ->whereRaw('DATEDIFF(CURRENT_DATE(), race_events.startDate) < 420');

        if ($raceCat) {
            if ($raceCat == 'world-cup') {
                $queryBuilder = $queryBuilder->whereRaw('races.rankingCategoryId = 1');
            } else {
                $queryBuilder = $queryBuilder->whereRaw('races.rankingCategoryId in (6, 7)');
            }
        }

        $groupedByCategories = $queryBuilder->get()->groupBy('catName');

        $groupedByAthletes = $groupedByCategories->map(function ($item, $key) {
            return $item->groupBy('athleteId')->map(function ($item, $key) {

                $raceDays = $item->reduce(function ($carry, $item) {
                    return $carry + $item->eventDuration;
                }, 0);

                // dd($raceDays);

                // if ($raceDays >= 0) {
                return collect([
                    'athleteId' => $key,
                    'firstName' => $item[0]->firstName,
                    'lastName' => $item[0]->lastName,
                    'gender' => $item[0]->gender,
                    'profilePic' => $item[0]->image,
                    'slug' => $item[0]->slug,
                    'country' => $item[0]->countryCode,
                    'qty' => $raceDays,
                ]);
                // }
            })->sortBy([['qty', 'desc']]);
        });

        return $groupedByAthletes;
    }

    public function pointsPerMonth(Request $request, $year = null, $month = null) {

      if (!$year) {
        $year = date('Y');
      }

      if (!$month) {
        $month = date('m');
      }

    $queryBuilder = DB::table('athletes')
      ->selectRaw('athletes.id as athleteId, athletes.firstName, athletes.lastName, athletes.image, athletes.slug, athletes.gender, countries.code as countryCode, categories.name as catName, rankings.points')
      ->join('rankings', 'rankings.athleteId', 'athletes.id')
      ->join('race_events', 'race_events.id', 'rankings.raceEventId')
      ->join('categories', 'categories.id', 'rankings.categoryId')
      ->join('countries', 'countries.id', 'athletes.countryId')
      ->whereRaw('rankings.type = 1')
      ->whereRaw("EXTRACT(MONTH FROM race_events.startDate) = $month and EXTRACT(YEAR FROM race_events.startDate) = $year");

    // dd($queryBuilder->get());

    $groupedByCategories = $queryBuilder->get()->groupBy('catName');

    // dd($groupedByCategories);

    $groupedByAthletes = $groupedByCategories->map(function ($item, $key) {
      return $item->groupBy('athleteId')->map(function ($item, $key) {

        $points = $item->reduce(function ($carry, $item) {
          return $carry + $item->points;
        }, 0);

        // dd($points);

        // if ($raceDays >= 0) {
        return collect([
          'athleteId' => $key,
          'firstName' => $item[0]->firstName,
          'lastName' => $item[0]->lastName,
          'gender' => $item[0]->gender,
          'profilePic' => $item[0]->image,
          'slug' => $item[0]->slug,
          'country' => $item[0]->countryCode,
          'qty' => $points,
        ]);
        // }
      })->sortBy([['qty', 'desc']]);
    });

    return $groupedByAthletes;
  }
}
