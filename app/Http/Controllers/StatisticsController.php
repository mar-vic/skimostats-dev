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
                'events.id as eventId',
                'events.name as eventName',
                'events.slug as eventSlug',
                'countries.code as countryCode',
                'countries.name as countryName',
                'entries.rank',
                'rankings.points',
                'types.name as raceTypeName',
                'categories.slug as categorySlug',
                'events.startDate',
                'entries.status',
                'athletes.id as athleteId',
                'athletes.firstName',
                'athletes.lastName',
                'athletes.gender',
                'athletes.image',
                'athletes.slug',
                'categories.name as catName'
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
            ->leftJoin('race_types as types', 'types.id', 'events.type')
            ->whereIn('categories.id', [1, 2, 23, 24, 25, 26])
            ->where('entries.rank', 1);

        if ($year) {
            $timespan = Ranking::getRankingYearTimespan($year);
            $queryBuilder = $queryBuilder->whereBetween('events.startDate', $timespan);
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

        return $groupedByAthletes->map(function ($item) {
            return $item->slice(0, 30);
        });
    }

    public function mostWinsByCountries(Request $request, $year = null) {

        $queryBuilder = DB::table('race_events as events')
            ->select(
                'events.id as eventId',
                'events.name as eventName',
                'events.slug as eventSlug',
                'countries.code as countryCode',
                'countries.name as countryName',
                'entries.rank',
                'rankings.points',
                'types.name as raceTypeName',
                'categories.slug as categorySlug',
                'events.startDate',
                'entries.status',
                'athletes.id as athleteId',
                'athletes.firstName',
                'athletes.lastName',
                'athletes.gender',
                'athletes.image',
                'athletes.slug',
                'athletes.countryId as countryId',
                'categories.name as catName'
            )
            ->join('race_event_participants as participants', 'participants.raceEventId', 'events.id')
            ->join('athletes', 'participants.athleteId', 'athletes.id')
            ->join('categories', 'categories.id', 'participants.categoryId')
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
            ->leftJoin('race_types as types', 'types.id', 'events.type')
            ->join('countries', 'countries.id', 'athletes.countryId')
            ->whereIn('categories.id', [1, 2, 23, 24, 25, 26])
            ->where('entries.rank', 1);

        if ($year) {
            $timespan = Ranking::getRankingYearTimespan($year);
            $queryBuilder = $queryBuilder->whereBetween('events.startDate', $timespan);
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

        return $groupedByCountries;
    }

    public function mostRaceDays(Request $request, $year = null) {

        $queryBuilder = DB::table('race_events as events')
                      ->selectRaw('athletes.id as athleteId, athletes.firstName, athletes.lastName, athletes.image, athletes.slug, athletes.gender, countries.code as countryCode, categories.name as catName, events.startDate as eventStartDate, events.endDate, DATEDIFF(events.endDate, events.startDate) + 1 as eventDuration')
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
                      ->whereIn('categories.id', [1, 2, 23, 24, 25, 26])
                      ->whereRaw('rankings.id != 6')
                      ->whereRaw('DATEDIFF(events.endDate, events.startDate) + 1 > 0');

        if ($year) {
            $timespan = Ranking::getRankingYearTimespan($year);
            $queryBuilder = $queryBuilder->whereBetween('events.startDate', $timespan);
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

        return $groupedByAthletes->map(function ($item) {
            return $item->slice(0, 30);
        });
    }

    public function mostNationsRacedIn(Request $request, $year = null) {

        $queryBuilder = DB::table('race_events as events')
                      ->selectRaw('athletes.id as athleteId, athletes.firstName, athletes.lastName, athletes.image, athletes.slug, athletes.gender, countries.code as countryCode, categories.name as catName, events.startDate as eventStartDate, events.endDate, DATEDIFF(events.endDate, events.startDate) + 1 as eventDuration')
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
                      ->whereIn('categories.id', [1, 2, 23, 24, 25, 26])
                      ->whereRaw('DATEDIFF(events.endDate, events.startDate) + 1 > 0');

        if ($year) {
            $timespan = Ranking::getRankingYearTimespan($year);
            $queryBuilder = $queryBuilder->whereBetween('events.startDate', $timespan);
        }

        $groupedByCategories = $queryBuilder->get()->groupBy('catName');

        $groupedByAthletes = $groupedByCategories->map(function ($item, $key) {
            return $item->groupBy('athleteId')->map(function ($item, $key) {

                $nationsRacedIn = $item->countBy('countryCode')->count();

                return collect([
                    'athleteId' => $key,
                    'firstName' => $item[0]->firstName,
                    'lastName' => $item[0]->lastName,
                    'gender' => $item[0]->gender,
                    'profilePic' => $item[0]->image,
                    'slug' => $item[0]->slug,
                    'country' => $item[0]->countryCode,
                    'qty' => $nationsRacedIn,
                ]);
            })->sortBy([['qty', 'desc']]);
        });

        return $groupedByAthletes->map(function ($item) {
            return $item->slice(0, 30);
        });
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
        $queryBuilder = DB::table('race_events as events')
                      ->selectRaw('athletes.id as athleteId, athletes.firstName, athletes.lastName, athletes.image, athletes.slug, athletes.gender, countries.code as countryCode, categories.name as catName, events.startDate as eventStartDate, events.endDate, DATEDIFF(events.endDate, events.startDate) + 1 as eventDuration')
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
                      ->whereIn('categories.id', [1, 2, 23, 24, 25, 26])
                      ->where('rankings.rankingCategoryId', 6)
                      ->where('entries.rank', 1);

        if ($year) {
            $timespan = Ranking::getRankingYearTimespan($year);
            $queryBuilder = $queryBuilder->whereBetween('events.startDate', $timespan);
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

        return $groupedByAthletes->map(function ($item) {
            return $item->slice(0, 30);
        });
    }

    public function mostWorldCupWins(Request $request, $year = null)
    {
        $queryBuilder = DB::table('race_events as events')
                      ->selectRaw('athletes.id as athleteId, athletes.firstName, athletes.lastName, athletes.image, athletes.slug, athletes.gender, countries.code as countryCode, categories.name as catName, events.startDate as eventStartDate, events.endDate, DATEDIFF(events.endDate, events.startDate) + 1 as eventDuration')
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
                      ->whereIn('categories.id', [1, 2, 23, 24, 25, 26])
                      ->where('rankings.rankingCategoryId', 1)
                      ->where('entries.rank', 1);

        if ($year) {
            $timespan = Ranking::getRankingYearTimespan($year);
            $queryBuilder = $queryBuilder->whereBetween('events.startDate', $timespan);
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

        return $groupedByAthletes->map(function ($item) {
            return $item->slice(0, 30);
        });

    }

    public function mostChocolates(Request $request, $year = null) {

        $queryBuilder = DB::table('race_events as events')
                      ->selectRaw('athletes.id as athleteId, athletes.firstName, athletes.lastName, athletes.image, athletes.slug, athletes.gender, countries.code as countryCode, categories.name as catName, events.startDate as eventStartDate, events.endDate, DATEDIFF(events.endDate, events.startDate) + 1 as eventDuration')
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
                      ->whereIn('categories.id', [1, 2, 23, 24, 25, 26])
                      ->where('entries.rank', 4);

        if ($year) {
            $timespan = Ranking::getRankingYearTimespan($year);
            $queryBuilder = $queryBuilder->whereBetween('events.startDate', $timespan);
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

        return $groupedByAthletes->map(function ($item) {
            return $item->slice(0, 30);
        });
    }

    public function mostTopTens(Request $request, $year = null) {

        $queryBuilder = DB::table('race_events as events')
                      ->selectRaw('athletes.id as athleteId, athletes.firstName, athletes.lastName, athletes.image, athletes.slug, athletes.gender, countries.code as countryCode, categories.name as catName, events.startDate as eventStartDate, events.endDate, DATEDIFF(events.endDate, events.startDate) + 1 as eventDuration')
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
                      ->whereIn('categories.id', [1, 2, 23, 24, 25, 26])
                      ->whereIn('entries.rank', [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);

        if ($year) {
            $timespan = Ranking::getRankingYearTimespan($year);
            $queryBuilder = $queryBuilder->whereBetween('events.startDate', $timespan);
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

        return $groupedByAthletes->map(function ($item) {
            return $item->slice(0, 30);
        });
    }

    public function activeAthletes(Request $request, $raceCat = null) {

        $queryBuilder = DB::table('race_events as events')
                      ->selectRaw('athletes.id as athleteId, athletes.firstName, athletes.lastName, athletes.image, athletes.slug, athletes.gender, countries.code as countryCode, categories.name as catName, events.startDate as eventStartDate, events.endDate, DATEDIFF(events.endDate, events.startDate) + 1 as eventDuration')
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
                      ->whereIn('categories.id', [1, 2, 23, 24, 25, 26])
                      ->whereRaw('DATEDIFF(events.endDate, events.startDate) + 1 > 0')
                      ->whereRaw('DATEDIFF(CURRENT_DATE(), events.startDate) < 420');

        if ($raceCat) {
            if ($raceCat == 'world-cup') {
                $queryBuilder = $queryBuilder->where('rankings.rankingCategoryId', 1);
            } else {
                $queryBuilder = $queryBuilder->whereIn('rankings.rankingCategoryId', [6, 7]);
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

        return $groupedByAthletes->map(function ($item) {
            return $item->slice(0, 30);
        });
    }

    public function youngestAthletes(Request $request, $raceCat = null) {

        $queryBuilder = DB::table('race_events as events')
                      ->selectRaw('athletes.id as athleteId, TIMESTAMPDIFF(YEAR, athletes.dateOfBirth, CURRENT_DATE()) as age, athletes.dateOfBirth, athletes.firstName, athletes.lastName, athletes.image, athletes.slug, athletes.gender, countries.code as countryCode, categories.name as catName, events.startDate as eventStartDate, events.endDate, DATEDIFF(events.endDate, events.startDate) + 1 as eventDuration')
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
                      ->whereIn('categories.id', [1, 2, 23, 24, 25, 26])
                      ->whereRaw('DATEDIFF(events.endDate, events.startDate) + 1 > 0')
                      ->whereRaw('DATEDIFF(CURRENT_DATE(), events.startDate) < 420')
                      ->whereRaw('athletes.dateOfBirth IS NOT NULL AND DATEDIFF(CURRENT_DATE(), athletes.dateOfBirth) > 0');

        if ($raceCat) {
            if ($raceCat == 'world-cup') {
                $queryBuilder = $queryBuilder->where('rankings.rankingCategoryId', 1);
            } else {
                $queryBuilder = $queryBuilder->whereIn('rankings.rankingCategoryId', [6, 7]);
            }
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
                    'qty' => $item[0]->age,
                ]);
                // }
            })->sortBy([['qty', 'asc']]);
        });

        return $groupedByAthletes->map(function ($item) {
            return $item->slice(0, 30);
        });
    }

    public function oldestAthletes(Request $request, $raceCat = null) {

        $queryBuilder = DB::table('race_events as events')
                      ->selectRaw('athletes.id as athleteId, TIMESTAMPDIFF(YEAR, athletes.dateOfBirth, CURRENT_DATE()) as age, athletes.dateOfBirth, athletes.firstName, athletes.lastName, athletes.image, athletes.slug, athletes.gender, countries.code as countryCode, categories.name as catName, events.startDate as eventStartDate, events.endDate, DATEDIFF(events.endDate, events.startDate) + 1 as eventDuration')
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
                      ->whereIn('categories.id', [1, 2, 23, 24, 25, 26])
                      ->whereRaw('DATEDIFF(events.endDate, events.startDate) + 1 > 0')
                      ->whereRaw('DATEDIFF(CURRENT_DATE(), events.startDate) < 420')
                      ->whereRaw('athletes.dateOfBirth IS NOT NULL AND DATEDIFF(CURRENT_DATE(), athletes.dateOfBirth) > 0');

        if ($raceCat) {
            if ($raceCat == 'world-cup') {
                $queryBuilder = $queryBuilder->where('rankings.rankingCategoryId', 1);
            } else {
                $queryBuilder = $queryBuilder->whereIn('rankings.rankingCategoryId', [6, 7]);
            }
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
                    'qty' => $item[0]->age,
                ]);
                // }
            })->sortBy([['qty', 'desc']]);
        });

        return $groupedByAthletes->map(function ($item) {
            return $item->slice(0, 30);
        });
    }


    public function pointsPerMonth(Request $request, $year = null, $month = null) {

        if (!$year) {
            $year = date('Y');
        }

        if (!$month) {
            $month = date('m');
        }

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
                'rankings.points'
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
            ->leftJoin('race_types as types', 'types.id', 'events.type')
            ->whereIn('categories.id', [1, 2, 23, 24, 25, 26])
            ->whereRaw("EXTRACT(MONTH FROM events.startDate) = $month and EXTRACT(YEAR FROM events.startDate) = $year");

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

        return $groupedByAthletes->map(function ($item) {
            return $item->slice(0, 30);
        });
    }

    public function mostPointsPerRaceDay(Request $request, $year = null)
    {
        $queryBuilder = DB::table('race_events as events')
                      ->selectRaw('athletes.id as athleteId, athletes.firstName, athletes.lastName, athletes.image, athletes.slug, athletes.gender, countries.code as countryCode, categories.name as catName, rankings.points, events.startDate as eventStartDate, events.endDate, DATEDIFF(events.endDate, events.startDate) + 1 as eventDuration')
                      ->join('race_event_participants as participants', 'participants.raceEventId', 'events.id')
                      ->join('athletes', 'participants.athleteId', 'athletes.id')
                      ->join('categories', 'categories.id', 'participants.categoryId')
                      ->leftJoin('countries', 'countries.id', 'events.countryId')
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
                      ->whereIn('categories.id', [1, 2, 23, 24, 25, 26]);

        if ($year) {
            $timespan = Ranking::getRankingYearTimespan($year);
            $queryBuilder = $queryBuilder->whereBetween('events.startDate', $timespan);
        }

        // dd($queryBuilder->get()->filter(function ($value, $key) {
        //     return $value->lastName == 'Verbnjak';
        // }));

        $groupedByCategories = $queryBuilder->get()->groupBy('catName');

        $groupedByAthletes = $groupedByCategories->map(function ($item, $key) {
            return $item->groupBy('athleteId')->map(function ($item, $key) {

                $points = $item->reduce(function ($carry, $item) {
                    return $carry + $item->points;
                }, 0);

                // $raceDays = $item->reduce(function ($carry, $item) {
                //     return $carry + $item->eventDuration;
                // }, 0);


                if ($item[0]->lastName == 'Verbnjak') {
                    // dd([
                    //     'athlete' => $item[0]->firstName . ' ' . $item[0]->lastName,
                    //     'racedays' => $raceDays,
                    //     'points' => $points,
                    //     'ppr' => $points / $raceDays
                    // ]);
                    // dd($raceDays);
                }

                // $ppr = $raceDays == 0 ? -1 : $points / $raceDays;

                // dd($ppr);

                $raceDays = $item->count();

                return collect([
                    'athleteId' => $key,
                    'firstName' => $item[0]->firstName,
                    'lastName' => $item[0]->lastName,
                    'gender' => $item[0]->gender,
                    'profilePic' => $item[0]->image,
                    'slug' => $item[0]->slug,
                    'country' => $item[0]->countryCode,
                    'qty' => round($raceDays == 0 ? -1 : $points / $raceDays)
                ]);
            })->sortBy([['qty', 'desc']]);
        });

        return $groupedByAthletes->map(function ($item) {
            return $item->slice(0, 30);
        });
    }




}
