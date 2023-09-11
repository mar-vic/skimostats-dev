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
                      ->whereIn('rankings.rankingCategoryId', [1, 2, 4, 5, 6, 7, 8, 9, 10, 13])
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

        // Apply the season filter, when appropriate
        if ($year != 'all-seasons') {
            $year = ($year == 'current-season' ? $years->first() : (int)$year);

            if (!$years->contains($year)) {
                return abort(404);
            }

            $timespan = Ranking::getRankingYearTimespan($year);
            $queryBuilder->whereBetween('events.startDate', $timespan);
        }

        // Group the results by categories
        $groupedByCategories = $queryBuilder->get()->groupBy('categoryName');

        // And then by athletes
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

        // Generate available categories
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


    public function raceDays(Request $request, string $year = 'current-season')
    {
        $years = $this->years($request);

        // Selecting all participations of all athletes
        $queryBuilder = DB::table("race_events as events")
        ->selectRaw("race_types.name as raceTypeName, events.name as eventName, rankings.type as rankingType, rankingCats.name as rankingCategory, rankingCats.id, athletes.id as athleteId, athletes.firstName as firstName, athletes.lastName as lastName, athletes.gender as gender, athletes.image as image, athletes.slug as slug, countries.code as countryCode, categories.id as categoryId, categories.name as categoryName, DATEDIFF(events.endDate, events.startDate) + 1 as eventDuration")
        ->join("race_event_participants as participants", "participants.raceEventId", "events.Id")
        ->join('categories', 'categories.id', 'participants.categoryId')
        ->leftJoin("race_event_teams as teams", "teams.id", "participants.raceEventTeamId")
        ->join("race_event_entries as entries", function ($qb) {
            $qb->on("entries.raceEventParticipantId", "=", "participants.id")
            ->orOn("entries.raceEventTeamId", "=", "teams.id");
        })
        ->join("athletes", "participants.athleteId", "athletes.id")
        ->leftJoin("countries", "countries.id", "athletes.countryId")
        ->join("rankings", "rankings.participantId", "participants.id")
        ->leftJoin("ranking_categories as rankingCats", "rankingCats.id", "rankings.rankingCategoryId")
        ->leftJoin("race_types", "race_types.id", "events.type")
        ->where("rankings.type", 1)
        ->whereNot("rankings.rankingCategoryId", 6)
        ->whereIn('categories.id', [1, 2, 3, 4, 23, 24, 25, 26]);


        // Confine to a season
        if ($year != 'all-seasons') {
            $year = ($year == 'current-season' ? $years->first() : (int)$year);

            if (!$years->contains($year)) {
                return abort(404);
            }

            $timespan = Ranking::getRankingYearTimespan($year);
            $queryBuilder->whereBetween('events.startDate', $timespan);
        }

        // dd($queryBuilder->get());

        // $handle = fopen("/home/marcus/Projects/skimostats-dev/backup/damevine_racedays_2023.csv", "w");

        // $queryBuilder->get()->each(fn ($row) => fputcsv($handle, [$row->eventName, $row->categoryName, $row->raceTypeName, $row->rankingCategory]));

        // fclose($handle);

        $groupedByCategories = $queryBuilder->get()->groupBy('categoryName');

        $groupedByAthletes = $groupedByCategories->map(function ($item, $key) {
            return $item->groupBy('athleteId')->map(function ($item, $key) {

                $raceDays = $item->reduce(function ($carry, $item) {
                    return $carry + $item->eventDuration;
                }, 0);

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
            })->sortBy([['qty', 'desc']])->slice(0, 30);
        });

        $categories = collect(['Men', 'Women', 'Men U23', 'Women U23', 'U20 Men', 'U20 Women', 'U18 Men', 'U18 Women'])
            ->filter(function ($item, $key) use ($groupedByAthletes) {
                return $groupedByAthletes->keys()->contains($item);
            });

        return view('front.statistics.athletes_seasonal', [
            'statsCategorySlug' => 'race-days',
            'data' => $groupedByAthletes->map(function ($item) {
                return $item->slice(0, 30);
            }),
            'year' => $year,
            'years' =>  $years,
            'categories' => $categories,
            'metric' => ['race days', '']
        ]);
    }

    public function pointsPerRaceday(Request $request, string $year = 'current-season') {

        $years = $this->years($request);

        $queryBuilder = DB::table('race_events as events')
                      ->selectRaw('athletes.id as athleteId, athletes.firstName, athletes.lastName, athletes.image, athletes.slug, athletes.gender, countries.code as countryCode, categories.name as catName, rankings.points, events.startDate as eventStartDate, events.endDate, DATEDIFF(events.endDate, events.startDate) + 1 as eventDuration')
                      ->join('race_event_participants as participants', 'participants.raceEventId', 'events.id')
                      ->join('athletes', 'participants.athleteId', 'athletes.id')
                      ->join('categories', 'categories.id', 'participants.categoryId')
                      ->leftJoin('countries', 'countries.id', 'athletes.countryId')
                      ->leftJoin('race_event_teams as teams', 'teams.id', 'participants.raceEventTeamId')
                      ->join('race_event_entries as entries', function ($qb) {
                          $qb->on('entries.raceEventParticipantId', '=', 'participants.id')
                             ->orOn('entries.raceEventTeamId', '=', 'teams.id');
                      })
                      ->leftJoin('rankings', function ($qb) {
                          $qb->on('rankings.participantId', '=', 'participants.id')
                             ->where('rankings.type', 1)
                             ->whereIn('rankings.categoryId', [1, 2]);
                      })
                      ->leftJoin('race_types as types', 'types.id', 'events.type')
                      ->whereIn('categories.id', [1, 2]);

        if ($year != 'all-seasons') {
            $year = ($year == 'current-season' ? $years->first() : (int)$year);

            if (!$years->contains($year)) {
                return abort(404);
            }

            $timespan = Ranking::getRankingYearTimespan($year);
            $queryBuilder->whereBetween('events.startDate', $timespan);
        }

        $groupedByCategories = $queryBuilder->get()->groupBy('catName');

        $groupedByAthletes = $groupedByCategories->map(function ($item, $key) {
            return $item->groupBy('athleteId')->map(function ($item, $key) {

                $raceDays = $item->reduce(function ($carry, $item) {
                    return $carry + $item->eventDuration;
                }, 0);

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
            })->sortBy([['qty', 'desc']]);
        });

        $categories = collect(['Men', 'Women', 'Men U23', 'Women U23', 'U20 Men', 'U20 Women', 'U18 Men', 'U18 Women'])
        ->filter(function ($item, $key) use ($groupedByAthletes) {
            return $groupedByAthletes->keys()->contains($item);
        });

        return view('front.statistics.athletes_seasonal', [
            'statsCategorySlug' => 'points-per-raceday',
            'data' => $groupedByAthletes->map(function ($item) {
                return $item->slice(0, 30);
            }),
            'year' => $year,
            'years' =>  $years,
            'categories' => $categories,
            'metric' => ['points / racedays', 'Average number of points scored by athlete in a race day. Only athletes with at least five racedays are considered.']
        ]);
    }

    public function pointsPerAge(Request $request, string $year = 'current-season') {
        $years = $this->years($request)->reject(function (int $value, int $key) { return $value < 2019; });

        $queryBuilder = DB::table('rankings')
                      ->join('athletes', 'athletes.id', 'rankings.athleteId')
                      ->join('race_events as events', 'events.id', 'rankings.raceEventId' )
                      ->leftJoin('countries', 'countries.id', 'athletes.countryId')
                      ->join('categories', 'categories.id', 'rankings.categoryId')
                      ->where('rankings.type', 1)
                      ->whereIn('categories.id',  [1, 2])
                      ->select(
                          'athletes.id as athleteId',
                          'athletes.firstName',
                          'athletes.lastName',
                          'athletes.image',
                          'athletes.slug',
                          'athletes.gender',
                          'athletes.countryId as countryId',
                          'athletes.dateOfBirth',
                          'countries.code as countryCode',
                          'countries.name as countryName',
                          'categories.name as catName',
                          'events.startDate as eventStartDate',
                          'events.endDate',
                          'rankings.points'
                      );


        $year = ($year == 'current-season' ? $years->first() : (int)$year);

        if (!$years->contains($year)) {
            return abort(404);
        }

        $timespan = Ranking::getRankingYearTimespan($year);
        $queryBuilder->whereBetween('events.startDate', $timespan);

        $groupedByCategories = $queryBuilder->get()->groupBy('catName');

        $groupedByAthletes = $groupedByCategories->map(function ($item, $key) {
            return $item->groupBy('athleteId')->map(function ($item, $key) {

                $points = $item->reduce(function ($carry, $item) {
                    return $carry + $item->points;
                }, 0);

                return collect([
                    'athleteId' => $key,
                    'firstName' => $item[0]->firstName,
                    'lastName' => $item[0]->lastName,
                    'gender' => $item[0]->gender,
                    'profilePic' => $item[0]->image,
                    'slug' => $item[0]->slug,
                    'country' => $item[0]->countryCode,
                    'age' =>  date('Y') - date('Y', strtotime($item[0]->dateOfBirth)),
                    'qty' => $points,
                ]);
            })->sortBy([['qty', 'desc']]);
        });

        $ageRanges = [
            '18-25' => [18, 25],
            '26-35' => [26, 35],
            '36-45' => [36, 45],
            '46+' => [46, null]
        ];

        $groupedByAge = $groupedByAthletes->map(function ($item, $key) use ($ageRanges) {

            return $item->groupBy(function ($item) use ($ageRanges) {
                foreach ($ageRanges as $range => [$min, $max]) {
                    if (is_null($max)) {
                        if ($item['age'] >= $min) {
                            return $range;
                        }
                    } elseif ($item['age'] >= $min and $item['age'] <= $max) {
                        return $range;
                    }
                }

                return 'Unkown';
            });
        });

        $groupedByAgeWithStats = $groupedByAge->map(function ($category, $key) {
            return $category->map(function ($ageGroup, $key) {
                $athletesCount = $ageGroup->count();
                $pointsSum = $ageGroup->sum('qty');
                return collect([
                    'athletesCount' => $ageGroup->count(),
                    'pointsSum' => $ageGroup->sum('qty')
                ]);
            });
        });

        $categories = collect(['Men', 'Women', 'Men U23', 'Women U23', 'U20 Men', 'U20 Women', 'U18 Men', 'U18 Women'])
        ->filter(function ($item, $key) use ($groupedByAge) {
            return $groupedByAge->keys()->contains($item);
        });

        // dd($categories);

        return view('front.statistics.athletes_seasonal_barchart', [
            'statsCategorySlug' => 'points-per-age',
            'data' => $groupedByAgeWithStats,
            'year' => $year,
            'years' =>  $years,
            'categories' => $categories,
        ]);
    }

    public function grandCourseVictories(Request $request, string $year = 'current-season') {

        $years = $this->years($request);

        $queryBuilder = DB::table('rankings')
                      ->join('athletes', 'athletes.id', 'rankings.athleteId')
                      ->join('race_events as events', 'events.id', 'rankings.raceEventId' )
                      ->leftJoin('countries', 'countries.id', 'athletes.countryId')
                      ->join('categories', 'categories.id', 'rankings.categoryId')
                      ->where('rankings.type', 1)
                      ->whereIn('categories.id',  [1, 2, 23, 24, 25, 26])
                      ->where('rankings.rank', 1)
                      ->where('rankings.rankingCategoryId', 6) // Grand Courses have category id
                      ->select(
                          'athletes.id as athleteId',
                          'athletes.firstName',
                          'athletes.lastName',
                          'athletes.image',
                          'athletes.slug',
                          'athletes.gender',
                          'athletes.countryId as countryId',
                          'countries.code as countryCode',
                          'countries.name as countryName',
                          'categories.name as catName',
                          'events.startDate as eventStartDate',
                          'events.endDate',
                          "events.name",
                          "rankings.rankingCategoryId"
                      );

        if ($year != 'all-seasons') {
            $year = ($year == 'current-season' ? $years->first() : (int)$year);

            if (!$years->contains($year)) {
                return abort(404);
            }

            $timespan = Ranking::getRankingYearTimespan($year);
            $queryBuilder->whereBetween('events.startDate', $timespan);
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

        $categories = collect(['Men', 'Women', 'Men U23', 'Women U23', 'U20 Men', 'U20 Women', 'U18 Men', 'U18 Women'])
        ->filter(function ($item, $key) use ($groupedByAthletes) {
            return $groupedByAthletes->keys()->contains($item);
        });

        return view('front.statistics.athletes_seasonal', [
            'statsCategorySlug' => 'grand-course-victories',
            'data' => $groupedByAthletes->map(function ($item) {
                return $item->slice(0, 30);
            }),
            'year' => $year,
            'years' =>  $years,
            'categories' => $categories,
            'metric' => ['GC wins', '']
        ]);
    }

    public function worldCupVictories(Request $request, string $year = 'current-season') {

        $years = $this->years($request);

        $queryBuilder = DB::table('rankings')
                      ->join('athletes', 'athletes.id', 'rankings.athleteId')
                      ->join('race_events as events', 'events.id', 'rankings.raceEventId' )
                      ->leftJoin('countries', 'countries.id', 'athletes.countryId')
                      ->join('categories', 'categories.id', 'rankings.categoryId')
                      ->where('rankings.type', 1)
                      ->whereIn('categories.id',  [1, 2, 23, 24, 25, 26])
                      ->where('rankings.rank', 1)
                      ->where('rankings.rankingCategoryId', 1) // World Cup race events have category id 1
                      ->select(
                          'athletes.id as athleteId',
                          'athletes.firstName',
                          'athletes.lastName',
                          'athletes.image',
                          'athletes.slug',
                          'athletes.gender',
                          'athletes.countryId as countryId',
                          'countries.code as countryCode',
                          'countries.name as countryName',
                          'categories.name as catName',
                          'events.startDate as eventStartDate',
                          'events.endDate'
                      );

        if ($year != 'all-seasons') {
            $year = ($year == 'current-season' ? $years->first() : (int)$year);

            if (!$years->contains($year)) {
                return abort(404);
            }

            $timespan = Ranking::getRankingYearTimespan($year);
            $queryBuilder->whereBetween('events.startDate', $timespan);
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

        $categories = collect(['Men', 'Women', 'Men U23', 'Women U23', 'U20 Men', 'U20 Women', 'U18 Men', 'U18 Women'])
        ->filter(function ($item, $key) use ($groupedByAthletes) {
            return $groupedByAthletes->keys()->contains($item);
        });

        return view('front.statistics.athletes_seasonal', [
            'statsCategorySlug' => 'world-cup-victories',
            'data' => $groupedByAthletes->map(function ($item) {
                return $item->slice(0, 30);
            }),
            'year' => $year,
            'years' =>  $years,
            'categories' => $categories,
            'metric' => ['WC wins', '']
        ]);
    }

    public function winsPerCountries(Request $request, string $year = 'current-season') {
        $years = $this->years($request);

        $queryBuilder = DB::table('rankings')
                      ->join('athletes', 'athletes.id', 'rankings.athleteId')
                      ->join('race_events as events', 'events.id', 'rankings.raceEventId' )
                      ->leftJoin('countries', 'countries.id', 'athletes.countryId')
                      ->join('categories', 'categories.id', 'rankings.categoryId')
                      ->where('rankings.type', 1)
                      ->whereIn('categories.id',  [1, 2, 23, 24, 25, 26])
                      ->where('rankings.rank', 1)
                      ->select(
                          'athletes.id as athleteId',
                          'athletes.firstname',
                          'athletes.lastName',
                          'athletes.image',
                          'athletes.slug',
                          'athletes.gender',
                          'athletes.countryId as countryId',
                          'countries.code as countryCode',
                          'countries.name as countryName',
                          'categories.name as catName',
                          'events.startDate as eventStartDate',
                          'events.endDate'
                      );

        if ($year != 'all-seasons') {
            $year = ($year == 'current-season' ? $years->first() : (int)$year);

            if (!$years->contains($year)) {
                return abort(404);
            }

            $timespan = Ranking::getRankingYearTimespan($year);
            $queryBuilder->whereBetween('events.startDate', $timespan);
        }

        $groupedByCategories = $queryBuilder->get()->groupBy('catName');

        $groupedByCountries = $groupedByCategories->map(function ($item, $key) {
            return $item->groupBy('countryId')->map(function ($item, $key) {
                return collect([
                    'countryId' => $key,
                    'countryName' => $item[0]->countryName,
                    'countryCode' => $item[0]->countryCode,
                    'qty' => $item->count(),
                ]);
            })->sortBy([['qty', 'desc']]);
        });

        $categories = collect(['Men', 'Women', 'Men U23', 'Women U23', 'U20 Men', 'U20 Women', 'U18 Men', 'U18 Women'])
        ->filter(function ($item, $key) use ($groupedByCountries) {
            return $groupedByCountries->keys()->contains($item);
        });

        return view('front.statistics.countries_seasonal', [
            'statsCategorySlug' => 'wins-per-countries',
            'data' => $groupedByCountries->map(function ($item) {
                return $item->slice(0, 30);
            }),
            'year' => $year,
            'years' =>  $years,
            'categories' => $categories,
            'metric' => ['victories', 'Number of victories achieved by athletes representing the country.']
        ]);
    }

    public function chocolates(Request $request, string $year = 'current-season') {

        $years = $this->years($request);

        $queryBuilder = DB::table('race_events as events')
                      ->selectRaw('athletes.id as athleteId, athletes.firstName, athletes.lastName, athletes.image, athletes.slug, athletes.gender, countries.code as countryCode, categories.name as catName, rankings.rank')
                      ->join('race_event_participants as participants', 'participants.raceEventId', 'events.id')
                      ->join('athletes', 'participants.athleteId', 'athletes.id')
                      ->join('categories', 'categories.id', 'participants.categoryId')
                      ->leftJoin('countries', 'countries.id', 'athletes.countryId')
                      ->leftJoin('race_event_teams as teams', 'teams.id', 'participants.raceEventTeamId')
                      ->join('race_event_entries as entries', function($qb) {
                          $qb->on('entries.raceEventParticipantId', '=', 'participants.id')
                             ->orOn('entries.raceEventTeamId', '=', 'teams.id');
                      })
                      ->leftJoin('rankings', function($qb) {
                          $qb->on('rankings.participantId', '=', 'participants.id')
                             ->whereIn('rankings.type', [1, 2])
                             ->whereIn('rankings.categoryId', [1, 2]);
                      })
                      ->join('ranking_categories', 'ranking_categories.id', 'events.rankingCategoryId')
                      // ->whereNotIn('ranking_categories.id', [3, 12])
                      ->whereIn('categories.id', [1, 2, 23, 24, 25, 26])
                      ->where('rankings.rank', 4);

        if ($year != 'all-seasons') {
            $year = ($year == 'current-season' ? $years->first() : (int)$year);

            if (!$years->contains($year)) {
                return abort(404);
            }

            $timespan = Ranking::getRankingYearTimespan($year);
            $queryBuilder->whereBetween('events.startDate', $timespan);
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

        // dd($groupedByAthletes);

        $categories = collect(['Men', 'Women', 'Men U23', 'Women U23', 'U20 Men', 'U20 Women', 'U18 Men', 'U18 Women'])
                    ->filter(function ($item, $key) use($groupedByAthletes) {
                        return $groupedByAthletes->keys()->contains($item);
        });

        return view('front.statistics.athletes_seasonal', [
            'statsCategorySlug' => 'chocolates',
            'data' => $groupedByAthletes->map(function ($item) {
                return $item->slice(0, 30);
            }),
            'year' => $year,
            'years' =>  $years,
            'categories' => $categories,
            'metric' => ['chocolates', "A 'chocolate' is a fourth place."]
        ]);
    }

    public function topTens(Request $request, string $year = 'current-season') {

        $years = $this->years($request);

        $queryBuilder = DB::table('rankings')
                      ->join('athletes', 'athletes.id', 'rankings.athleteId')
                      ->join('race_events as events', 'events.id', 'rankings.raceEventId' )
                      ->leftJoin('countries', 'countries.id', 'athletes.countryId')
                      ->join('categories', 'categories.id', 'rankings.categoryId')
                      ->where('rankings.type', 1)
                      ->whereIn('categories.id',  [1, 2, 23, 24, 25, 26])
                      ->whereIn('rankings.rank', [1, 2, 3, 4, 5, 6, 7, 8, 9, 10])
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
                          'events.endDate'
                      );

        if ($year != 'all-seasons') {
            $year = ($year == 'current-season' ? $years->first() : (int)$year);

            if (!$years->contains($year)) {
                return abort(404);
            }

            $timespan = Ranking::getRankingYearTimespan($year);
            $queryBuilder->whereBetween('events.startDate', $timespan);
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

        $categories = collect(['Men', 'Women', 'Men U23', 'Women U23', 'U20 Men', 'U20 Women', 'U18 Men', 'U18 Women'])
                    ->filter(function ($item, $key) use($groupedByAthletes) {
                        return $groupedByAthletes->keys()->contains($item);
        });

        return view('front.statistics.athletes_seasonal', [
            'statsCategorySlug' => 'top-tens',
            'data' => $groupedByAthletes->map(function ($item) {
                return $item->slice(0, 30);
            }),
            'year' => $year,
            'years' =>  $years,
            'categories' => $categories,
            'metric' => ['top tens', '']
        ]);
    }

    public function pointsPerCountry(Request $request, string $year = 'current-season') {

        $years = $this->years($request);

        $queryBuilder = DB::table('rankings as r')
                      ->select(
                          DB::raw('SUM(r.points) as pts'),
                          'r.athleteId',
                          'r.rank',
                          'r.categoryId',
                          'a.firstName',
                          'a.lastName',
                          'a.slug as athleteSlug',
                          'c.code as countryCode',
                          'c.name as countryName',
                          'c.id as countryId',
                          'categories.name as catName'
                      )
                      ->join('athletes as a', 'a.id', 'r.athleteId')
                      ->leftJoin('countries as c', 'c.id', 'a.countryId')
                      ->join('categories', 'categories.id', 'r.categoryId')
                      ->whereIn('r.categoryId', [1, 2]) // i.e., Men, Women
                      ->groupBy('r.athleteId')
                      ->havingRaw('pts > 0')
                      ->orderBy('pts', 'desc')
                      ->orderBy('a.lastName', 'asc');

        if ($year != 'all-seasons') {
            $year = ($year == 'current-season' ? $years->first() : (int)$year);

            if (!$years->contains($year)) {
                return abort(404);
            }

            $timespan = Ranking::getRankingYearTimespan($year);
            $queryBuilder->whereBetween('r.obtainedAt', $timespan);
        }

        $groupedByCategories = $queryBuilder->get()->groupBy('catName');

        $groupedByCountries = $groupedByCategories->map(function ($item, $key) {

            return $item->groupBy('countryId')->map(function ($item, $key) {
                return collect([
                    'countryId' => $key,
                    'countryName' => $item[0]->countryName,
                    'countryCode' => $item[0]->countryCode,
                    'qty' => $item->sum('pts'),
                ]);
            })->sortBy([['qty', 'desc']])->reject(function ($value, $key) {
                return $value['countryId'] == '';
            });
        });

        $categories = collect(['Men', 'Women'])
        ->filter(function ($item, $key) use ($groupedByCountries) {
            return $groupedByCountries->keys()->contains($item);
        });

        return view('front.statistics.countries_seasonal', [
            'statsCategorySlug' => 'points-per-country',
            'data' => $groupedByCountries,
            'year' => $year,
            'years' =>  $years,
            'categories' => $categories,
            'metric' => ['points', '']
        ]);
    }

    public function activeAthletes(Request $request, string $rankingCategory = 'all') {

        $queryBuilder = DB::table('race_events as events')
                      ->selectRaw('athletes.id as athleteId, athletes.firstName, athletes.lastName, athletes.image, athletes.slug, athletes.gender, countries.code as countryCode, categories.name as catName, events.startDate as eventStartDate, events.endDate, DATEDIFF(events.endDate, events.startDate) + 1 as eventDuration')
                      ->join('race_event_participants as participants', 'participants.raceEventId', 'events.id')
                      ->join('athletes', 'participants.athleteId', 'athletes.id')
                      ->join('categories', 'categories.id', 'participants.categoryId')
                      ->leftJoin('countries', 'countries.id', 'athletes.countryId')
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
                      ->whereIn('categories.id', [1, 2, 23, 24, 25, 26])
                      ->whereRaw('DATEDIFF(events.endDate, events.startDate) + 1 > 0')
                      ->whereRaw('DATEDIFF(CURRENT_DATE(), events.startDate) < 420');

        if ($rankingCategory == 'world-cup') {
            $queryBuilder->where('rankings.rankingCategoryId', 1);
        } else {
            $queryBuilder->whereIn('rankings.rankingCategoryId', [6, 7]);
        }

        $groupedByCategories = $queryBuilder->get()->groupBy('catName');

        $groupedByAthletes = $groupedByCategories->map(function ($item, $key) {
            return $item->groupBy('athleteId')->map(function ($item, $key) {

                $raceDays = $item->reduce(function ($carry, $item) {
                    return $carry + $item->eventDuration;
                }, 0);

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
            })->sortBy([['qty', 'desc']]);
        });

        $categories = collect(['Men', 'Women', 'Men U23', 'Women U23', 'U20 Men', 'U20 Women', 'U18 Men', 'U18 Women'])
        ->filter(function ($item, $key) use ($groupedByAthletes) {
            return $groupedByAthletes->keys()->contains($item);
        });

        return view('front.statistics.athletes_by_ranking_category', [
            'statsCategorySlug' => 'active-athletes',
            'data' => $groupedByAthletes->map(function ($item) {
                return $item->slice(0, 30);
            }),
            'rankingCategory' => $rankingCategory,
            'categories' => $categories,
            'metric' => ['recent races', 'Active athlete is one having at least one race day in the last 14 months.']
        ]);
    }

    public function winnersPerCountry(Request $request, string $year = 'current-season') {

        $years = $this->years($request);

        $queryBuilder = DB::table('rankings')
                      ->join('athletes', 'athletes.id', 'rankings.athleteId')
                      ->join('race_events as events', 'events.id', 'rankings.raceEventId' )
                      ->leftJoin('countries', 'countries.id', 'athletes.countryId')
                      ->join('categories', 'categories.id', 'rankings.categoryId')
                      ->where('rankings.type', 1)
                      ->whereIn('categories.id',  [1, 2, 23, 24, 25, 26])
                      ->where('rankings.rank', 1)
                      ->select(
                          'athletes.id as athleteId',
                          'athletes.firstname',
                          'athletes.lastName',
                          'athletes.image',
                          'athletes.slug',
                          'athletes.gender',
                          'athletes.countryId as countryId',
                          'countries.code as countryCode',
                          'countries.name as countryName',
                          'categories.name as catName',
                          'events.startDate as eventStartDate',
                          'events.endDate'
                      );

        if ($year != 'all-seasons') {
            $year = ($year == 'current-season' ? $years->first() : (int)$year);

            if (!$years->contains($year)) {
                return abort(404);
            }

            $timespan = Ranking::getRankingYearTimespan($year);
            $queryBuilder->whereBetween('events.startDate', $timespan);
        }

        $groupedByCategories = $queryBuilder->get()->groupBy('catName');

        $groupedByCountries = $groupedByCategories->map(function ($item, $key) {
            return $item->groupBy('countryId')->map(function ($item, $key) {

                $winners = $item->countBy(function ($entry) {
                    return $entry->athleteId;
                });

                return collect([
                    'countryId' => $key,
                    'countryName' => $item[0]->countryName,
                    'countryCode' => $item[0]->countryCode,
                    'qty' => $winners->count(),
                ]);
            })->sortBy([['qty', 'desc']]);
        });

        $categories = collect(['Men', 'Women', 'Men U23', 'Women U23', 'U20 Men', 'U20 Women', 'U18 Men', 'U18 Women'])
        ->filter(function ($item, $key) use ($groupedByCountries) {
            return $groupedByCountries->keys()->contains($item);
        });

        return view('front.statistics.countries_seasonal', [
            'statsCategorySlug' => 'winners-per-countries',
            'data' => $groupedByCountries->map(function ($item) {
                return $item->slice(0, 30);
            }),
            'year' => $year,
            'years' =>  $years,
            'categories' => $categories,
            'metric' => ['winners', 'Number of winning athletes (ie., those with at least one victory) representing the country.']
        ]);
    }

    public function countriesRacedIn(Request $request, string $year = 'current-season') {

        $years = $this->years($request);

        // $queryBuilder = DB::table('race_events as events')
        //               ->selectRaw('athletes.id as athleteId, athletes.firstName, athletes.lastName, athletes.image, athletes.slug, athletes.gender, countries.code as countryCode, athCtr.code as athleteCountryCode, categories.name as catName, events.startDate as eventStartDate, events.endDate, DATEDIFF(events.endDate, events.startDate) + 1 as eventDuration')
        //               ->join('race_event_participants as participants', 'participants.raceEventId', 'events.id')
        //               ->join('athletes', 'participants.athleteId', 'athletes.id')
        //               ->join('categories', 'categories.id', 'participants.categoryId')
        //               ->leftJoin('countries', 'countries.id', 'events.countryId')
        //               ->leftJoin('countries as athCtr', 'athCtr.id', 'athletes.countryId'  )
        //               ->leftJoin('race_event_teams as teams', 'teams.id', 'participants.raceEventTeamId')
        //               ->join('race_event_entries as entries', function($qb) {
        //                   $qb->on('entries.raceEventParticipantId', '=', 'participants.id')
        //                      ->orOn('entries.raceEventTeamId', '=', 'teams.id');
        //               })
        //               ->leftJoin('rankings', function($qb) {
        //                   $qb->on('rankings.participantId', '=', 'participants.id')
        //                      ->where('rankings.type', 1)
        //                      ->whereIn('rankings.categoryId', [1, 2]);
        //               })
        //               ->whereIn('categories.id', [1, 2, 23, 24, 25, 26])
        //               ->whereRaw('DATEDIFF(events.endDate, events.startDate) + 1 > 0');

        $queryBuilder = DB::table('rankings')
                      ->join('athletes', 'athletes.id', 'rankings.athleteId')
                      ->join('race_events as events', 'events.id', 'rankings.raceEventId' )
                      ->leftJoin('countries', 'countries.id', 'athletes.countryId')
                      ->join('categories', 'categories.id', 'rankings.categoryId')
                      ->where('rankings.type', 1)
                      ->whereIn('categories.id',  [1, 2, 23, 24, 25, 26])
                      ->select(
                          'athletes.id as athleteId',
                          'athletes.firstName',
                          'athletes.lastName',
                          'athletes.image',
                          'athletes.slug',
                          'athletes.gender',
                          'athletes.countryId as countryId',
                          'countries.code as countryCode',
                          'countries.name as countryName',
                          'categories.name as catName',
                          'events.startDate as eventStartDate',
                          'events.endDate',
                          'events.countryId as eventCountryId'
                      );

        if ($year != 'all-seasons') {
            $year = ($year == 'current-season' ? $years->first() : (int)$year);

            if (!$years->contains($year)) {
                return abort(404);
            }

            $timespan = Ranking::getRankingYearTimespan($year);
            $queryBuilder->whereBetween('events.startDate', $timespan);
        }

        $groupedByCategories = $queryBuilder->get()->groupBy('catName');

        $groupedByAthletes = $groupedByCategories->map(function ($item, $key) {
            return $item->groupBy('athleteId')->map(function ($item, $key) {


                $countriesRacedIn = $item->countBy(function ($item) {
                    return $item->eventCountryId;
                })->count();

                return collect([
                    'athleteId' => $key,
                    'firstName' => $item[0]->firstName,
                    'lastName' => $item[0]->lastName,
                    'gender' => $item[0]->gender,
                    'profilePic' => $item[0]->image,
                    'slug' => $item[0]->slug,
                    'country' => $item[0]->countryCode,
                    'qty' => $countriesRacedIn,
                ]);
            })->sortBy([['qty', 'desc']]);
        });

        $categories = collect(['Men', 'Women', 'Men U23', 'Women U23', 'U20 Men', 'U20 Women', 'U18 Men', 'U18 Women'])
        ->filter(function ($item, $key) use ($groupedByAthletes) {
            return $groupedByAthletes->keys()->contains($item);
        });

        return view('front.statistics.athletes_seasonal', [
            'statsCategorySlug' => 'countries-raced-in',
            'data' => $groupedByAthletes->map(function ($item) {
                return $item->slice(0, 30);
            }),
            'year' => $year,
            'years' =>  $years,
            'categories' => $categories,
            'metric' => ['countries', '']
        ]);
    }

    public function youngestAthletes(Request $request, string $rankingCategory = 'all') {

            $queryBuilder = DB::table('race_events as events')
                      ->selectRaw('athletes.id as athleteId, TIMESTAMPDIFF(YEAR, athletes.dateOfBirth, CURRENT_DATE()) as age, athletes.dateOfBirth, athletes.firstName, athletes.lastName, athletes.image, athletes.slug, athletes.gender, countries.code as countryCode, categories.name as catName, events.startDate as eventStartDate, events.endDate, DATEDIFF(events.endDate, events.startDate) + 1 as eventDuration')
                      ->join('race_event_participants as participants', 'participants.raceEventId', 'events.id')
                      ->join('athletes', 'participants.athleteId', 'athletes.id')
                      ->join('categories', 'categories.id', 'participants.categoryId')
                      ->leftJoin('countries', 'countries.id', 'athletes.countryId')
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
                      // ->whereIn('categories.id', [1, 2, 23, 24, 25, 26])
                      ->whereRaw('DATEDIFF(events.endDate, events.startDate) + 1 > 0')
                      ->whereRaw('DATEDIFF(CURRENT_DATE(), events.startDate) < 420')
                      ->whereRaw('athletes.dateOfBirth IS NOT NULL AND DATEDIFF(CURRENT_DATE(), athletes.dateOfBirth) > 0');

        if ($rankingCategory == 'world-cup') {
            $queryBuilder->where('rankings.rankingCategoryId', 1);
        } else {
            $queryBuilder->whereIn('rankings.rankingCategoryId', [6, 7]);
        }

        $groupedByCategories = $queryBuilder->get()->groupBy('gender');

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
                    'qty' => $item[0]->age,
                ]);
            })->sortBy([['qty', 'asc']]);
        });

        $categories = collect(['male', 'female'])
        ->filter(function ($item, $key) use ($groupedByAthletes) {
            return $groupedByAthletes->keys()->contains($item);
        });

        return view('front.statistics.athletes_by_ranking_category', [
            'statsCategorySlug' => 'youngest-athletes',
            'data' => $groupedByAthletes->map(function ($item) {
                return $item->slice(0, 30);
            }),
            'rankingCategory' => $rankingCategory,
            'categories' => $categories,
            'metric' => ['age', 'Only athletes having at least one race day in the last 14 months are considered.']
        ]);
    }

    public function oldestAthletes(Request $request, string $rankingCategory = 'all') {

        $queryBuilder = DB::table('race_events as events')
                      ->selectRaw('athletes.id as athleteId, TIMESTAMPDIFF(YEAR, athletes.dateOfBirth, CURRENT_DATE()) as age, athletes.dateOfBirth, athletes.firstName, athletes.lastName, athletes.image, athletes.slug, athletes.gender, countries.code as countryCode, categories.name as catName, events.startDate as eventStartDate, events.endDate, DATEDIFF(events.endDate, events.startDate) + 1 as eventDuration')
                      ->join('race_event_participants as participants', 'participants.raceEventId', 'events.id')
                      ->join('athletes', 'participants.athleteId', 'athletes.id')
                      ->join('categories', 'categories.id', 'participants.categoryId')
                      ->leftJoin('countries', 'countries.id', 'athletes.countryId')
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
                      ->whereIn('categories.id', [1, 2, 23, 24, 25, 26])
                      ->whereRaw('DATEDIFF(events.endDate, events.startDate) + 1 > 0')
                      ->whereRaw('DATEDIFF(CURRENT_DATE(), events.startDate) < 420')
                      ->whereRaw('athletes.dateOfBirth IS NOT NULL AND DATEDIFF(CURRENT_DATE(), athletes.dateOfBirth) > 0');

        if ($rankingCategory == 'world-cup') {
            $queryBuilder->where('rankings.rankingCategoryId', 1);
        } else {
            $queryBuilder->whereIn('rankings.rankingCategoryId', [6, 7]);
        }

        $groupedByCategories = $queryBuilder->get()->groupBy('gender');

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
                    'qty' => $item[0]->age,
                ]);
            })->sortBy([['qty', 'desc']]);
        });

        $categories = collect(['male', 'female'])
        ->filter(function ($item, $key) use ($groupedByAthletes) {
            return $groupedByAthletes->keys()->contains($item);
        });

        return view('front.statistics.athletes_by_ranking_category', [
            'statsCategorySlug' => 'oldest-athletes',
            'data' => $groupedByAthletes->map(function ($item) {
                return $item->slice(0, 30);
            }),
            'rankingCategory' => $rankingCategory,
            'categories' => $categories,
            'metric' => ['age', 'Only athletes having at least one race day in the last 14 months are considered.']
        ]);
    }

    public function pointsPerMonth(Request $request, $monthYear = null) {

        if (!$monthYear) {
            $monthYear = date('m-Y');
        }

        // dd(explode('-',$monthYear)[1]);

        $years = $this->years($request);

        $month =  explode('-', $monthYear)[0];
        $year =  explode('-', $monthYear)[1];

        if (!$years->contains($year)) {
            return abort(404);
        }

        $queryBuilder = DB::table('rankings')
                      ->join('athletes', 'athletes.id', 'rankings.athleteId')
                      ->join('race_events as events', 'events.id', 'rankings.raceEventId' )
                      ->leftJoin('countries', 'countries.id', 'athletes.countryId')
                      ->join('categories', 'categories.id', 'rankings.categoryId')
                      ->where('rankings.type', 1)
                      ->whereIn('categories.id', [1, 2, 23, 24, 25, 26])
                      ->whereRaw("EXTRACT(MONTH FROM events.startDate) = $month and EXTRACT(YEAR FROM events.startDate) = $year")
                      ->select(
                          'athletes.id as athleteId',
                          'athletes.firstName',
                          'athletes.lastName',
                          'athletes.image',
                          'athletes.slug',
                          'athletes.gender',
                          'athletes.countryId as countryId',
                          'athletes.dateOfBirth',
                          'countries.code as countryCode',
                          'countries.name as countryName',
                          'categories.name as catName',
                          'events.startDate as eventStartDate',
                          'events.endDate',
                          'events.name as eventName',
                          'rankings.points'
                      );

        // dd($queryBuilder->get());

        $groupedByCategories = $queryBuilder->get()->groupBy('catName');

        $groupedByAthletes = $groupedByCategories->map(function ($item, $key) {
            return $item->groupBy('athleteId')->map(function ($item, $key) {

                $points = $item->reduce(function ($carry, $item) {
                    return $carry + $item->points;
                }, 0);

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
            })->sortBy([['qty', 'desc']]);
        });

        $data = $groupedByAthletes
            ->map(function ($item) {
                return $item->slice(0, 30);
            })
            ->filter(function ($item) {
                return $item->first()['qty'] > 0;
            });

        $categories = collect(['Men', 'Women', 'Men U23', 'Women U23', 'U20 Men', 'U20 Women', 'U18 Men', 'U18 Women'])
        ->filter(function ($item, $key) use ($data) {
            return $data->keys()->contains($item);
        });

        return view('front.statistics.athletes_monthly', [
            'statsCategorySlug' => 'points-per-month',
            'data' => $data,
            'month' => (int)$month - 1,
            'year' => (int)$year,
            'years' => $years,
            'categories' => $categories,
            'metric' => ['points', '']
        ]);
    }
}
