<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Athlete;
use App\RaceEvent;
use App\Ranking;

use App\Enums\RankingType;
use App\Services\AthleteService;
use Carbon\Carbon;
use Doctrine\DBAL\Query\QueryBuilder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;

class AthleteController extends Controller
{
    public function raceYearList(Request $request, Athlete $athlete) {
        $result = DB::table('race_event_participants as rep')
            ->select(
                DB::raw('IF(MONTH(re.startDate) > 9, YEAR(re.startDate) + 1, YEAR(re.startDate)) as year')
            )
            ->join('race_events as re', 're.id', 'rep.raceEventId')
            ->where('rep.athleteId', $athlete->id)
            ->groupBy(DB::raw('IF(MONTH(re.startDate) > 9, YEAR(re.startDate) + 1, YEAR(re.startDate))'))
            ->orderBy('year', 'desc')
            ->get()
            ->toArray();

        return array_map(function($item) { return $item->year; }, $result);
    }

    public function races(Request $request, Athlete $athlete, $year = null) {
        $timespan = Ranking::getRankingYearTimespan($year);

        // dd($timespan);

        $theYear = substr($timespan[0], 0, 4) + 1;

        $builder = DB::table('race_events as re')
            ->select(
                're.id',
                're.name',
                're.slug',
                'c.code as countryCode',
                'c.name as countryName',
                'ree.rank',
                'rnk.points',
                'rt.name as raceTypeName',
                'cat.slug as categorySlug',
                're.startDate',
                'ree.status',
                'rnk.type'
            )
            ->join('race_event_participants as rep', 'rep.raceEventId', 're.id')
            ->join('categories as cat', 'cat.id', 'rep.categoryId')
            ->leftJoin('countries as c', 'c.id', 're.countryId')
            ->leftJoin('race_event_teams as ret', 'ret.id', 'rep.raceEventTeamId')
            ->join('race_event_entries as ree', function($qb) {
                $qb->on('ree.raceEventParticipantId', '=', 'rep.id')
                    ->orOn('ree.raceEventTeamId', '=', 'ret.id');
            })
            ->leftJoin('rankings as rnk', function($qb) {$qb->on('rnk.participantId', '=', 'rep.id')->whereIn('rnk.type', [1])->whereIn('rnk.categoryId', [1, 2, 14, 38]);})
            ->leftJoin('race_types as rt', 'rt.id', 're.type')
            ->where('rep.athleteId', $athlete->id)
            ->whereBetween('re.startDate', $timespan)
            ->groupBy('re.id');

        // dd($builder->get());

        return [
            'year' => $theYear,
            'races' => $builder->get()
        ];
    }

    public function rankingPerSeason(Request $request, Athlete $athlete) {
        $qb = DB::table('ranking_tables as rt')
            ->select(
                'rt.rank',
                'rt.points',
                'rt.year',
                'cat.slug as categorySlug',
                'a.slug as athleteSlug'
            )
            ->join('categories as cat', 'cat.id', 'rt.categoryId')
            ->join('athletes as a', 'a.id', 'rt.athleteId')
            ->where('rt.type', RankingType::SKIMO_STATS)
            ->where('rt.athleteId', $athlete->id)
            ->whereIn('rt.categoryId', [1, 2])
            ->where('rt.year', '>', 0)
            ->groupBy('rt.year')
            ->orderBy('rt.year', 'desc')
            ->limit(6);

        return $qb->get();
    }

    public function racesPerCountry(Request $request, Athlete $athlete, $year = null) {
        $timespan = Ranking::getRankingYearTimespan($year);

        $qb = DB::table('race_event_participants as rep')
            ->select(
                DB::raw('COUNT(rep.id) as participationCount'),
                'c.name',
                'c.id',
                'c.code'
            )
            ->join('race_events as re', 're.id', 'rep.raceEventId')
            ->join('countries as c', 'c.id', 're.countryId')
            ->leftJoin('race_event_teams as ret', 'ret.id', 'rep.raceEventTeamId')
            ->join('race_event_entries as ree', function($qb) {
                $qb->on('ree.raceEventParticipantId', '=', 'rep.id')
                    ->orOn('ree.raceEventTeamId', '=', 'ret.id');
            })
            // ->where('ree.status', '!=', 'DNS')
            ->where('rep.athleteId', $athlete->id)
            ->whereBetween('re.startDate', $timespan)
            ->groupBy('re.countryId')
            ->orderBy('participationCount', 'desc')
            ->limit(6);

        return $qb->get();
    }


    public function getAthlete(Request $request, Athlete $athlete) {
        $athleteService = new AthleteService();
        return [
            'firstName' => $athlete->firstName,
            'lastName' => $athlete->lastName,
            'slug' => $athlete->slug,
            'image' => $athlete->image,
            'age' => $athlete->age,
            'weight' => $athlete->weight,
            'height' => $athlete->height,
            'country' => $athlete->country,
            'rank' => $athlete->rank,
            'points' => $athlete->getTotalPoints(),
            'gender' => $athlete->gender,
            'victories' => $athlete->getCareerWins()->count(),
            'pointsPerSpecialty' => $athleteService->getPointsPerSpecialty($athlete)
        ];
    }

    public function getPopularAthletes(Request $request) {
        $popularAthletes = DB::table('athletes as a')
            ->join('athlete_visits as av', 'av.athleteId', 'a.id')
            ->leftJoin('countries as co', 'co.id', 'a.countryId')
            ->select(
                'a.id',
                'a.slug',
                'co.code as countryCode',
                'a.firstName',
                'a.lastName',
                DB::raw('COUNT(av.id) as visits')
            )
            ->where('av.lastVisit', '>', Carbon::now()->subDays(7))
            ->groupBy('a.id')
            ->orderBy('visits', 'desc')
            ->orderBy('lastName', 'asc')
            ->orderBy('firstName', 'asc')
            ->limit(10)
            ->get();

        return $popularAthletes;
    }

    public function athletePredictions(Request $request) {
        return Athlete::with('country')
        ->where(
            DB::raw('CONCAT(firstName, " ", lastName)'),
            'LIKE',
            '%'.$request->get('q').'%'
        )
        ->orderBy('firstName', 'asc')
        ->limit(8)
        ->get();
    }

    public function getApiV1List(Request $request) {
        $q = $request->get('q');

        $res = DB::table('athletes as a')
            ->select([
                'a.firstName',
                'a.lastName',
                'a.image',
                'a.slug',
                'a.placeOfBirth',
                'a.gender',
                'a.dateOfBirth',
                'c.code as countryCode',
                'c.name as countryName',
                DB::raw('MIN(r.rank) as rank')
            ])
            ->leftJoin('countries as c', 'c.id', 'a.countryId')
            ->leftJoin('ranking_tables as r', function(JoinClause $join) {
                $join->on('r.athleteId', 'a.id')
                    ->where('r.type', RankingType::SKIMO_STATS)
                    ->where('r.year', RankingController::getActualYear());
            })
            ->where(function($qr) use ($q) {
                $qr->where('a.firstName', 'LIKE', '%' . $q . '%')
                    ->orWhere('a.lastName', 'LIKE', '%' . $q . '%')
                    ->orWhere('a.placeOfBirth', 'LIKE', '%' . $q . '%')
                    ->orWhere('c.name', 'LIKE', '%' . $q . '%')
                    ->orWhere(DB::raw("CONCAT(a.firstName, ' ', a.lastName)"), 'LIKE', '%' . $q . '%')
                    ->orWhere(DB::raw("CONCAT(a.lastName, ' ', a.firstName)"), 'LIKE', '%' . $q . '%');
            })
            ->groupBy('a.id')
            ->orderByRaw('ISNULL(a.image)')
            ->orderByRaw('ISNULL(rank)')
            ->orderBy('r.rank', 'asc')
            ->orderByRaw('CONCAT(a.firstName, " ", a.lastName) asc')
            ->limit(200)->get();

        $response = [];

        /** @var \App\Athlete $athlete */
        foreach ($res as $athlete) {
            $row = [
                'firstName' => $athlete->firstName,
                'lastName' => $athlete->lastName,
                'gender' => $athlete->gender,
                'image' => $athlete->image,
                'slug' => $athlete->slug,
                'name' => $athlete->firstName . ' ' . $athlete->lastName,
                'rank' => $athlete->rank,
                'placeOfBirth' => $athlete->placeOfBirth,
                'dateOfBirth' => $athlete->dateOfBirth,
                'countryCode' => $athlete->countryCode,
                'countryName' => $athlete->countryName,
            ];

            $response[] = $row;
        }

        return $response;
    }

    public function getApiListOld(Request $request) {
        $q = $request->get('q');

        $res = Athlete::where('show_in_api', true)
            ->where(function($qr) use ($q) {
                $qr->where('firstName', 'LIKE', '%' . $q . '%')
                    ->orWhere('firstName', 'LIKE', '%' . $q . '%')
                    ->orWhere(DB::raw("CONCAT(athletes.firstName, ' ', athletes.lastName)"), 'LIKE', '%' . $q . '%')
                    ->orWhere(DB::raw("CONCAT(athletes.lastName, ' ', athletes.firstName)"), 'LIKE', '%' . $q . '%');
            })->get();

        $response = [];

        /** @var \App\Athlete $athlete */
        foreach ($res as $athlete) {
            $row = [
                'url' => route('athletes.detail.slug', $athlete->slug),
                'firstName' => $athlete->firstName,
                'lastName' => $athlete->lastName,
                'image' => asset('images/athletes/'.$athlete->image),
                'name' => $athlete->firstName . ' ' . $athlete->lastName,
                'height' => $athlete->height,
                'weight' => $athlete->weight,
                'placeOfBirth' => $athlete->placeOfBirth,
                'dateOfBirth' => $athlete->dateOfBirth,
                'country' => $athlete->country ? $athlete->country->name : null
            ];

            foreach (['strava', 'twitter', 'facebook', 'instagram', 'web'] as $social) {
                $row['social_' . $social] = $athlete->hasSocialLink($social) ? $athlete->getSocialLink($social) : null;
            }

            $response[] = $row;
        }

        return $response;
    }

    // NEW STUFF by MV
    private function topResultsForApiList(Athlete $athlete) {
        // returns top results of an athlete. To be consumed only by API calls.
        $queryBuilder = DB::table('rankings')
             ->where('athleteId', '=', $athlete->id)
             ->whereNotNull('rankings.rankingCategoryId')
             ->whereRaw('rankings.rank in (1, 2, 3, 4)')
             ->join('race_events as events', 'events.id', 'rankings.raceEventId')
             ->join('races', 'races.id', 'events.raceId')
             ->selectRaw('races.id as raceId, '.
                         'races.name as raceName, '.
                         'races.rankingCategoryId as rankingCategory, '.
                         'events.name as eventName, '.
                         'events.startDate as date, '.
                         'rankings.points, '.
                         'rankings.rank');

        // get php collection object from querybuilder
        $collection = $queryBuilder->get();

        // group the collection by races
        $grouppedByRace = $collection->mapToGroups(function ($item, $key) {
            return [$item->raceId => [
                    'raceName' => $item->raceName,
                    'eventName' => $item->eventName,
                    'eventDate' => $item->date,
                    'points' => $item->points,
                    'rank' => $item->rank,
                    'rankingCategoryId' => $item->rankingCategory,
            ]];
        });

        // sort the collection by rankingCategoryId
        $sorted = $grouppedByRace->sort(function ($a, $b) {
            $rnkCatsScores = [
                6 => 1,
                5 => 2,
                1 => 3,
                7 => 4,
                8 => 5,
                2 => 6,
                4 => 7,
                11 => 8,
                3 => 9,
                10 => 10,
                9 => 10
            ];

            $aScore = $rnkCatsScores[$a[0]["rankingCategoryId"]];
            $bScore = $rnkCatsScores[$b[0]["rankingCategoryId"]];

            return $aScore - $bScore;
        });

        // TODO: sort race events by their date

        return $sorted->values();
    }

    private function rankingPerSeasonForApiList(Athlete $athlete)
    {
        $qb = DB::table('ranking_tables as rt')
        ->select(
            'rt.rank',
            'rt.points',
            'rt.year',
        )
            ->join('categories as cat', 'cat.id', 'rt.categoryId')
            ->join('athletes as a', 'a.id', 'rt.athleteId')
            ->where('rt.type', RankingType::SKIMO_STATS)
            ->where('rt.athleteId', $athlete->id)
            ->whereIn('rt.categoryId', [1, 2])
            ->where('rt.year', '>', 0)
            ->groupBy('rt.year')
            ->orderBy('rt.year', 'desc')
            ->limit(6);

        return $qb->get()->slice(0, 2);
    }

    public function getApiList(Request $request) {
        $q = $request->get('q');

        $queryBuilder = Athlete::where('show_in_api', true)
            ->where(function($qr) use ($q) {
                $qr->where('firstName', 'LIKE', '%' . $q . '%')
                    ->orWhere('firstName', 'LIKE', '%' . $q . '%')
                    ->orWhere(DB::raw("CONCAT(athletes.firstName, ' ', athletes.lastName)"), 'LIKE', '%' . $q . '%')
                    ->orWhere(DB::raw("CONCAT(athletes.lastName, ' ', athletes.firstName)"), 'LIKE', '%' . $q . '%');
            });

        $res = $queryBuilder->get();

        $response = [];


        /** @var \App\Athlete $athlete */
        foreach ($res as $athlete) {
            $row = [
                'url' => route('athletes.detail.slug', $athlete->slug),
                'firstName' => $athlete->firstName,
                'lastName' => $athlete->lastName,
                'image' => asset('images/athletes/'.$athlete->image),
                'name' => $athlete->firstName . ' ' . $athlete->lastName,
                'height' => $athlete->height,
                'weight' => $athlete->weight,
                'placeOfBirth' => $athlete->placeOfBirth,
                'dateOfBirth' => $athlete->dateOfBirth,
                'country' => $athlete->country ? $athlete->country->name : null,
                'topResults' => $this->topResultsForApiList($athlete),
                'ranking' => $this->rankingPerSeasonForApiList($athlete)
            ];

            foreach (['strava', 'twitter', 'facebook', 'instagram', 'web'] as $social) {
                $row['social_' . $social] = $athlete->hasSocialLink($social) ? $athlete->getSocialLink($social) : null;
            }

            $response[] = $row;
        }

        return $response;
    }

    public function careerWins(Request $request, Athlete $athlete) {

        $builder = DB::table('race_events as re')
            ->select(
                're.startDate as eventStartDate',
                're.name as eventName',
                're.slug as eventSlug',
                'rt.name as raceType',
                'rnk.rank',
                'rnk.points',
                'cat.slug as categorySlug',
                'c.name as countryName',
                'c.code as countryCode'
            )
            ->join('race_event_participants as rep', 'rep.raceEventId', 're.id')
            ->join('categories as cat', 'cat.id', 'rep.categoryId')
            ->leftJoin('countries as c', 'c.id', 're.countryId')
            ->leftJoin('race_event_teams as ret', 'ret.id', 'rep.raceEventTeamId')
            ->join('race_event_entries as ree', function($qb) {
                $qb->on('ree.raceEventParticipantId', '=', 'rep.id')
                    ->orOn('ree.raceEventTeamId', '=', 'ret.id');
            })
            ->leftJoin('rankings as rnk', function($qb) {
                $qb->on('rnk.participantId', '=', 'rep.id')
                    ->where('rnk.type', 1)
                    ->whereIn('rnk.categoryId', [1, 2]);
            })
            ->leftJoin('race_types as rt', 'rt.id', 're.type')
            ->where('rep.athleteId', $athlete->id)
            ->where('ree.rank', 1)
            ->whereRaw('rnk.rankingCategoryId in (1, 2, 4, 5, 6, 7, 8, 9, 10, 13)');
            // ->groupBy('re.id');

        return $builder->get();
    }

    public function topResults(Request $request, Athlete $athlete) {
        // return the top results of the athlete
        // top results are grouped by races and order by category IDs as follows: 6, 5, 1, 7, 8, 2, 4, 11, 3, 10
        $queryBuilder = DB::table('rankings')
             ->where('athleteId', '=', $athlete->id)
             ->whereNotNull('rankings.rankingCategoryId')
             ->whereRaw('rankings.rank in (1, 2, 3, 4)')
             ->join('race_events as events', 'events.id', 'rankings.raceEventId')
             ->join('races', 'races.id', 'events.raceId')
             // ->groupBy('races.id')
             // ->orderByRaw("case races.rankingCategoryId ".
             //              "when 6 then 1 ".
             //              "when 5 then 2 ".
             //              "when 1 then 3 ".
             //              "when 7 then 4 ".
             //              "when 8 then 5 ".
             //              "when 2 then 6 ".
             //              "when 4 then 7 ".
             //              "when 11 then 8 ".
             //              "when 3 then 9 ".
             //              "else 10 ".
             //              "end asc")
             // ->limit(10)
             ->selectRaw('races.id as raceId, '.
                         'races.name as raceName, '.
                         'races.rankingCategoryId as rankingCategory, '.
                         'events.name as eventName, '.
                         'events.slug as eventSlug, '.
                         'events.startDate as date, '.
                         'rankings.points, '.
                         'rankings.rank');

        // get php collection object from querybuilder
        $collection = $queryBuilder->get();

        // group the collection by races
        $grouppedByRace = $collection->mapToGroups(function ($item, $key) {
            return [$item->raceId => [
                    'raceName' => $item->raceName,
                    'eventName' => $item->eventName,
                    'eventSlug' => $item->eventSlug,
                    'eventDate' => $item->date,
                    'points' => $item->points,
                    'rank' => $item->rank,
                    'rankingCategoryId' => $item->rankingCategory,
                    'raceId' => $item->raceId
            ]];
        });

        // sort the collection by rankingCategoryId
        $sorted = $grouppedByRace->sort(function ($a, $b) {
            $rnkCatsScores = [
                6 => 1,
                5 => 2,
                1 => 3,
                7 => 4,
                8 => 5,
                2 => 6,
                4 => 7,
                11 => 8,
                3 => 9,
                10 => 10,
                9 => 10
            ];

            $aScore = $rnkCatsScores[$a[0]["rankingCategoryId"]];
            $bScore = $rnkCatsScores[$b[0]["rankingCategoryId"]];

            return $aScore - $bScore;
        });

        // TODO: sort race events by their date

        return $sorted->values();
    }

    public function raceDays(Request $request, Athlete $athlete, $year)
    {
        $timespan = Ranking::getRankingYearTimespan($year);

        $builder = DB::table('race_events as re')
            ->select(
                're.id',
                're.name',
                're.slug',
                'c.code as countryCode',
                'c.name as countryName',
                'ree.rank',
                'rnk.points',
                'rt.name as raceTypeName',
                'cat.slug as categorySlug',
                're.startDate',
                'ree.status'
            )
            ->join('race_event_participants as rep', 'rep.raceEventId', 're.id')
            ->join('categories as cat', 'cat.id', 'rep.categoryId')
            ->leftJoin('countries as c', 'c.id', 're.countryId')
            ->leftJoin('race_event_teams as ret', 'ret.id', 'rep.raceEventTeamId')
            ->join('race_event_entries as ree', function($qb) {
                $qb->on('ree.raceEventParticipantId', '=', 'rep.id')
                    ->orOn('ree.raceEventTeamId', '=', 'ret.id');
            })
            ->leftJoin('rankings as rnk', function($qb) {
                $qb->on('rnk.participantId', '=', 'rep.id')
                    ->where('rnk.type', 1)
                    ->whereIn('rnk.categoryId', [1, 2]);
            })
            ->leftJoin('race_types as rt', 'rt.id', 're.type')
            ->where('rep.athleteId', $athlete->id)
            ->whereBetween('re.startDate', $timespan)
            ->groupBy('re.id');

        return $builder->get();
    }

    public function elevation(Request $request, Athlete $athlete, $year)
    {
        // return the elevation overcome by an athlete during a season
        $queryBuilder = DB::table('race_event_participants as participants')
                      ->join('race_events as events', 'participants.raceEventId', 'events.id')
                      ->where('participants.id', '=', $athlete->id)
                      // ->where('events.year', '=', $year)  // TODO: the seasons needs to be selected, not year
                      ->groupBy('events.year')
                      ->selectRaw('sum(events.elevation)');

        return $queryBuilder->get();
    }

    public function seasonSummary(Request $request, Athlete $athlete, $year)
    {
        $timespan = Ranking::getRankingYearTimespan($year);

        $builder = DB::table('race_events as re')
            ->select(
                're.id',
                're.name',
                're.slug',
                'c.code as countryCode',
                'c.name as countryName',
                'ree.rank',
                'rnk.points',
                'rt.name as raceTypeName',
                'cat.slug as categorySlug',
                're.startDate',
                'ree.status'
            )
            ->join('race_event_participants as rep', 'rep.raceEventId', 're.id')
            ->join('categories as cat', 'cat.id', 'rep.categoryId')
            ->leftJoin('countries as c', 'c.id', 're.countryId')
            ->leftJoin('race_event_teams as ret', 'ret.id', 'rep.raceEventTeamId')
            ->join('race_event_entries as ree', function($qb) {
                $qb->on('ree.raceEventParticipantId', '=', 'rep.id')
                    ->orOn('ree.raceEventTeamId', '=', 'ret.id');
            })
            ->leftJoin('rankings as rnk', function($qb) {
                $qb->on('rnk.participantId', '=', 'rep.id')
                    ->where('rnk.type', 1)
                    ->whereIn('rnk.categoryId', [1, 2]);
            })
            ->leftJoin('race_types as rt', 'rt.id', 're.type')
            ->where('rep.athleteId', $athlete->id)
            ->whereBetween('re.startDate', $timespan)
            ->whereRaw('ree.rank is not null')
            ->groupBy('re.id');

        $collection = $builder->get();

        return collect([
            'raceDays' => $collection->count(),
            'points' => $collection->sum('points')
        ]);
    }
}
