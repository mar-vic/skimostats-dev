<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

use Vinkla\Instagram\Instagram;

use Illuminate\Support\Facades\DB;

use App\Race;
use App\RaceEvent;
use App\Country;
use App\Category;
use App\Athlete;
use App\Enums\RankingType;
use App\PartnerCategory;
use App\RaceEventCategory;
use App\Ranking;
use App\RaceEventParticipant;

use App\Http\Resources\RaceEventResource;
use App\Http\Resources\RaceEventResultsResource;
use App\Services\AthleteService;
use Throwable;

class FrontController extends Controller
{
    // TESTING
    public function test(Request $request) {
        dd(RaceEvent::all());
        return view('test', [
            'data' => $this->getHomeData(),
            'partners' => PartnerCategory::where('id', 1)->get()
        ]);
    }
    // END TESTING

    public function cookies(Request $request) {
        return view('front.cookies');
    }

    public function privacyPolicy(Request $request) {
        return view('front.privacy-policy');
    }

    public function preview(Request $request) {
        return view('front.index', [
            'data' => $this->getHomeData(),
	        'partners' => PartnerCategory::where('id', 1)->get()
        ]);
    }

    public function getHomeData() {
        $eventsDb = RaceEvent::latestEntries()->get();

        $events = [];

        foreach ($eventsDb as $event) {
            $events[] = (new RaceEventResource($event))->toArray($event);
        }

        $birthdays = DB::table('athletes as a')
            ->select(
                'a.id',
                'a.slug',
                'a.firstName',
                'a.lastName',
                'c.code as countryCode',
                'a.dateOfBirth'
            )
            ->leftJoin('countries as c', 'c.id', 'a.countryId')
            ->whereNotNull('a.dateOfBirth')
            ->whereRaw('DATE_ADD(a.dateOfBirth,
            INTERVAL YEAR(CURDATE())-YEAR(a.dateOfBirth)
                    + IF(DAYOFYEAR(CURDATE()) > DAYOFYEAR(a.dateOfBirth),1,0)
            YEAR) BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 300 DAY)')
            ->orderByRaw('DATE_ADD(a.dateOfBirth,
            INTERVAL YEAR(CURDATE())-YEAR(a.dateOfBirth)
                    + IF(DAYOFYEAR(CURDATE()) > DAYOFYEAR(a.dateOfBirth),1,0)
            YEAR) ASC')
            ->limit(10)->get();

        $upcomingEvents = [];

        function mapAgeToIds($age) {
            return array_map(function($item){return $item['id'];}, Category::where('age', $age)->get()->toArray());
        }

        // $cats = [
        //     [1,2],
        //     [5,6],
        //     [7,8],
        //     [9,10],
        // ];

        $cats = [
            mapAgeToIds('adult'),
            mapAgeToIds('junior'),
            mapAgeToIds('cadet'),
            mapAgeToIds('master'),
        ];

        foreach ($cats as $catsToAdd) {
            $eventsToCheck = RaceEvent::with(['categories', 'country', 'raceType'])
                ->where('startDate', '>=', Carbon::now()->startOfDay())
                ->where('is_visible', 1)
                ->whereHas('categories', function($q) use ($catsToAdd) {
                    $q->whereIn('categories.id', $catsToAdd);
                })
                ->orderBy('startDate', 'ASC')
                ->limit(10)
                ->get();

            foreach ($eventsToCheck as $eventToCheck) {
                $findInArray = array_filter($upcomingEvents, function($item) use ($eventToCheck) {
                    return ($item->id ===$eventToCheck->id);
                });

                if (!$findInArray) {
                    $upcomingEvents[] = $eventToCheck;
                }
            }
        }

        $categories = [];

        foreach($upcomingEvents as $event) {
            foreach($event->categories as $eventCat) {
                if (!count(array_filter($categories, function($cat) use ($eventCat) {
                    return $cat->id === $eventCat->id;
                }))) {
                    $categories[] = $eventCat;
                }
            }
        }

        $upcoming = [
            'races' => $upcomingEvents,
            'categories' => $categories
        ];

        return [
            'events' => $events,
            'birthdays' => $birthdays,
            // 'popularAthletes' => $popularAthletes,
            'upcoming' => $upcoming,
        ];
    }

    public function athletes(Request $request) {
        $athleteCount = Athlete::count();
        $countries = DB::table('countries')
            ->select(
                'countries.id',
                'countries.name',
                'countries.code',
                DB::raw('COUNT(athletes.id) as athleteCount')
            )
            ->join('athletes', 'athletes.countryId', 'countries.id')
            ->groupBy('countries.id')
            ->get();

        $searchQuery = $request->find;

        $selectedCountry = 'ALL';
        $perPage = 12;

        $query = DB::table('athletes as a')
            ->select([
                'a.firstName',
                'a.slug',
                'a.lastName',
                DB::raw('CONCAT(a.firstName, " ", a.lastName) as name'),
                'a.image',
                'a.gender',
                'c.name as countryName',
                'c.code as countryCode'
            ])
            ->leftJoin('countries as c', 'c.id', 'a.countryId')
            ->leftJoin('ranking_tables as rt', function($q){
                $q->on('rt.athleteId', 'a.id')
                    ->on('rt.type', DB::raw(RankingType::SKIMO_STATS))
                    ->on('rt.year', DB::raw(RankingController::getActualYear()));
            })
            ->where('a.lastName', '!=', '')
            ->orderByRaw('ISNULL(rt.rank)')
            ->orderBy('rt.rank', 'asc')
            ->orderByRaw('IF(a.image IS NULL, 0, 1) DESC')
            ->orderByRaw('TRIM(a.lastName)')
            ->orderBy('a.firstName', 'asc');

        // $query = Athlete::leftJoin('ranking_table', function($q){
        //     $q->on('ranking_table.athleteId', 'athletes.id')
        //         ->on('ranking_table.type', DB::raw(RankingType::SKIMO_STATS))
        //         ->on('ranking_table.year', DB::raw(RankingController::getActualYear()));
        // })->where('lastName', '!=', '')->orderByRaw('TRIM(lastName)', 'asc')->orderBy('firstName', 'asc');

        // TODO: Remove later
        if ($searchQuery == 'Lausanne 2020') {
            $query->where('attendsLausanne', true);
        } else if ($searchQuery) {
            $query->where(function($qb) use ($searchQuery) {
                $qb->where('a.firstName', 'LIKE', '%'.$searchQuery.'%')
                ->orWhere('a.lastName', 'LIKE', '%'.$searchQuery.'%')
                ->orWhereRaw('CONCAT(a.firstName, " ", a.lastName) LIKE ?', ['%' . $searchQuery . '%'])
                ->orWhereRaw('CONCAT(a.lastName, " ", a.firstName) LIKE ?', ['%' . $searchQuery . '%']);
            });
        } else if ($request->has('country')) {
            $country = Country::whereCode($request->get('country'))->first();
            if ($country) {
                $selectedCountry = $request->get('country');

                $query->where('a.countryId', $country->id);
            }
        }



        $pageCount = ceil($query->count() / $perPage);

        $requestedPage = $request->has('page') ? (int)$request->get('page') : 1;
        $actualPage = $requestedPage > 0 && $requestedPage <= $pageCount ? $requestedPage : 1;

        $entries = $query->limit($perPage)->offset($perPage * $actualPage - $perPage)->get();

        return view('front.athletes', [
            'athleteCount' => $athleteCount,
            'countries' => $countries,
            'selectedCountry' => $selectedCountry,

            'pageCount' => $pageCount,
            'actualPage' => $actualPage,

            'entries' => $entries,
        ]);
    }

    public function getInstafeed(Request $request) {
        $instafeed = [];

        try {
            $instagram = new Instagram('20644764513.1677ed0.46a83f9570a4400ba8e949de6422b7f4');

            // Fetch recent user media items.
            $instafeed = $instagram->media();

            // $html = file_get_contents($request->get('url'));

            // preg_match('/_sharedData = ({.*);<\/script>/', $html, $matches);
            // $profile_data = json_decode($matches[1])->entry_data->ProfilePage[0]->graphql->user;

            // foreach($profile_data->edge_owner_to_timeline_media->edges as $edgeNode) {
            //     $edge = $edgeNode->node;
            //     $instafeed[] = [
            //         'url' => 'https://instagram.com/p/'.$edge->shortcode.'/',
            //         'image' => $edge->thumbnail_src,
            //     ];
            // }
        } catch(Throwable $e) {}

        return $instafeed ?? [];
    }

    public function athleteDetailRedirect(Request $request, Athlete $athlete) {
        return redirect(route('athletes.detail.slug', $athlete->slug), 302);
    }

    public function athleteDetail(Request $request, string $slug) {
        $athlete = Athlete::where('slug', $slug)->first();

        if (!$athlete) {
            return abort(404);
        }

        // $topResults = DB::table('race_event_entries')
        //     ->join('race_event_participants', 'race_event_participants.id', 'race_event_entries.raceEventParticipantId')
        //     ->join('race_events', 'race_events.id', 'race_event_entries.raceEventId')
        //     ->join('race_types', 'race_types.id', 'race_events.type')
        //     ->select(
        //         'race_event_entries.rank',
        //         'race_events.name AS eventName',
        //         'race_events.optionalName AS eventOptionalName',
        //         'race_types.name AS raceType',
        //         'race_events.year'
        //     )
        //     ->where('race_event_participants.athleteId', $athlete->id)
        //     ->orderBy('race_event_entries.rank', 'asc')
        //     ->orderBy('race_events.year', 'desc')
        //     ->get();

        $athlete->logVisit();

        $topResults = [];

        $customTopResults = RaceEventParticipant::where('athleteId', $athlete->id)
            ->whereNotNull('topResult')
            ->orderBy('topResult', 'asc')
            ->get();
        $computedTopResults = Ranking::select('rankings.*')
            ->where('athleteId', $athlete->id)
            ->whereNotNull('raceEventId')
            ->orderBy('points', 'desc')
            ->orderBy('obtainedAt', 'desc')
            ->limit(6)
            ->get();
        if (count($customTopResults) > 0) {
            foreach($customTopResults as $topResult) {
                $topResults[$topResult->topResult] = (object)[
                    'event' => $topResult->raceEvent,
                    'rank' => $topResult->raceEventEntry ? $topResult->raceEventEntry->rank : $topResult->raceEventTeam->raceEventEntry->rank,
                    'category' => $topResult->category,
                ];
            }

            $index = 0;
            for ($i = 1; $i <= 6; $i++) {
                if (!isset($topResults[$i]) && isset($computedTopResults[$index])) {
                    $topResults[$i] = $computedTopResults[$index];
                    $index++;
                }
            }
            ksort($topResults);
        } else {
            $topResults = $computedTopResults;
        }

        // $timespan = Ranking::getRankingYearTimeSpan();
        // $pointsPerSpecialty = DB::table('rankings')
        //         ->select(
        //             'race_types.name as raceTypeName',
        //             DB::raw('SUM(rankings.points) as points'),
        //             // sum of all of athlete's points
        //             DB::raw('(SELECT SUM(rk.points) as pts FROM rankings as rk WHERE rk.type = 1 && rk.categoryId IN (1,2) && rk.athleteId=rankings.athleteId GROUP BY rk.athleteId ORDER BY pts DESC LIMIT 1) as maxPoints')
        //         )
        //         ->join('race_types', 'race_types.id', 'rankings.raceTypeId')
        //         ->where('rankings.type', 1)
        //         ->where('rankings.athleteId', $athlete->id)
        //         ->whereIn('rankings.categoryId', [1, 2])
        //         // ->whereBetween('rankings.obtainedAt', $timespan)
        //         ->groupBy('rankings.athleteId')
        //         ->groupBy('rankings.raceTypeId')
        //         ->get();

        // New TOP Results by MV
        $topResultsNew = $this->topResults($athlete);
        $grouppedRankCounts = array();

        foreach($topResultsNew as $raceResults) {
            $ranks = array();
            foreach($raceResults as $result) {
                $ranks[] = $result['rank'];
            }
            $grouppedRankCounts[$result['raceName']] = collect($ranks)->countBy();
        }
        // dd($grouppedRankCounts);
        // END new TOP Results

        $athleteService = new AthleteService();
        $pointsPerSpecialty = $athleteService->getPointsPerSpecialty($athlete);

        return view('front.athlete-detail', [
            'athlete' => $athlete,
            'topResults' => $topResults,
            'topResultsNew' => $topResultsNew,
            'allRankCounts' => $grouppedRankCounts,
            'customTopResults' => $customTopResults,
            'pointsPerSpecialty' => $pointsPerSpecialty,
        ]);
    }

    public function aboutUs(Request $request) {
        return view('front.about-us');
    }

    public function contributions() {
        return view('front.contributions');
    }

    public function partners() {
        return view('front.partners', [
            'partnerCategories' => PartnerCategory::orderBy('position', 'asc')->get(),
        ]);
    }

    public function raceEventDetailRedirect(Request $request, RaceEvent $event) {
        return redirect(route('raceEventDetail.slug', $event->slug), 302);
    }
    public function raceEventDetail(Request $request, string $slug, string $categorySlug = null) {
        $event = RaceEvent::where('slug', $slug)->first();

        if (!$event) {
            return abort(404);
        }

        $eventCategory = RaceEventCategory::where('raceEventId', $event->id)->orderBy('id', 'asc')->first();
        $category = Category::where('id', $eventCategory->categoryId)->first();
        if($categorySlug) {
            $category = Category::where('slug', $categorySlug)->first();
        }

        if (!$category || !$event->hasCategory($category->id)) {
            return abort(404);
        }

        $results = (new RaceEventResultsResource($event))->toArray($event);
        // dd($results);
        $generalClassificationResults = [];

        // dd($event->isGeneralClassification);

        return view('front.race-event', [
            'data' => [
                'event' => (new RaceEventResource($event))->toArray($event),
                'results' => $results,
                'stage' => $event->stageNumber,
                'isGeneralClassification' => $event->isGeneralClassification,
                'showGeneralClassification' => $event->eventParent && $event->eventParent->hasStages(),
                'generalClassificationResults' => $event->getGeneralClassificationResults(),
            ],
        ]);
    }

    public function races(Request $request) {
        $races = RaceEvent::where('is_visible', 1)->whereNotNull('type')->with('country')->with('raceType')->orderBy('startDate', 'asc')->get();

        return view('front.races', [
            'data' => [
                'races' => $races,
            ],
        ]);
    }

    public function getRacesV1List(Request $request) {
        $year = $request->get('year', RankingController::getActualYear());
        $races = RaceEvent::where('is_visible', 1)
            ->whereNotNull('type')
            ->where(function($q) use ($year) {
                $q
                    ->orWhere(DB::raw('YEAR(startDate)'), $year)
                    ->orWhere(DB::raw('YEAR(endDate)'), $year);
            })
            ->with('country')
            ->with('raceType')
            ->orderBy('startDate', 'asc')
            ->get();

        $years = DB::table('race_events')
            ->select([
                DB::raw('YEAR(startDate) as year')
            ])
            ->where('is_visible', 1)
            ->whereNotNull('type')
            ->groupBy('year')
            ->orderBy('year', 'desc')
            ->get()
            ->toArray();

        return [
            'years' => array_map(function($y) { return $y->year; }, $years),
            'races' => $races,
            'year' => $year
        ];
    }

    public function rankings(Request $request) {
        return view('front.rankings');
    }

    public function statistics(Request $request) {
        $racersPerCountry = DB::table('athletes as a')
            ->join('countries as co', 'co.id', 'a.countryId')
            ->select(
                'co.id as countryId',
                'co.code as countryCode',
                'co.name as countryName',
                DB::raw('COUNT(a.id) as athleteCount')
            )
            ->orderBy('athleteCount', 'DESC')
            ->groupBy('co.id')
            ->get();

        $racersPerAge = DB::query()->fromSub(function($qb) {
                $qb->from('athletes as a')
                ->select(
                    'a.id',
                    DB::raw("ROUND(DATEDIFF(CURDATE(), Cast(a.dateOfBirth as Date)) / 365.25, 0) as age")
                );
            }, 'sub')
            ->select(
                'sub.age',
                DB::raw("COUNT(`sub`.`id`) as athleteCount")
            )
            ->groupBy('sub.age')
            ->orderByRaw('ISNULL(sub.age)')
            ->orderBy('athleteCount', 'DESC')
            ->get();
        $oldestAthletes = Athlete::orderByRaw('ISNULL(dateOfBirth)')->orderBy('dateOfBirth', 'asc')->limit(20)->get();
        $youngestAthletes = Athlete::orderByRaw('ISNULL(dateOfBirth)')->orderBy('dateOfBirth', 'desc')->limit(20)->get();

        $tallestAthletes = Athlete::orderByRaw('ISNULL(height)')->orderBy('height', 'desc')->limit(20)->get();
        $shortestAthletes = Athlete::orderByRaw('ISNULL(height)')->orderBy('height', 'asc')->limit(20)->get();
        $heaviestAthletes = Athlete::orderByRaw('ISNULL(weight)')->orderBy('weight', 'desc')->limit(20)->get();
        $lightestAthletes = Athlete::orderByRaw('ISNULL(weight)')->orderBy('weight', 'asc')->limit(20)->get();
        // $mostPopular = DB::table('athletes as a')
        //     ->join('athlete_visits as av', 'av.athleteId', 'a.id')
        //     ->leftJoin('countries as co', 'co.id', 'a.countryId')
        //     ->select(
        //         'a.id',
        //         'co.code as countryCode',
        //         'a.firstName',
        //         'a.lastName',
        //         DB::raw('COUNT(av.id) as visits')
        //     )
        //     ->where('av.lastVisit', '>', Carbon::now()->subDays(7))
        //     ->groupBy('a.id')
        //     ->orderBy('visits', 'desc')
        //     ->orderBy('lastName', 'asc')
        //     ->orderBy('firstName', 'asc')
        //     ->limit(20)
        //     ->get();

        $mostPopular = Athlete::orderBy('clicks', 'DESC')->with(['country'])->limit(20)->get();


        return view('front.hypermedia_statistics', [
            'racersPerCountry' => $racersPerCountry,
            'racersPerAge' => $racersPerAge,
            'oldestAthletes' => $oldestAthletes,
            'youngestAthletes' => $youngestAthletes,
            'mostPopular' => $mostPopular,
            'tallestAthletes' => $tallestAthletes,
            'shortestAthletes' => $shortestAthletes,
            'heaviestAthletes' => $heaviestAthletes,
            'lightestAthletes' => $lightestAthletes,
        ]);
    }

    public function hypermediaStatistics(Request $request, string $statsCategory = "victories") {
        $queryBuilder = DB::table('rankings')
                      ->join('athletes', 'rankings.athleteId', 'athletes.id')
                      ->join('countries', 'athletes.countryId', 'countries.id')
                      ->join('categories', 'rankings.categoryId', 'categories.id')
                      ->join('race_events as events', 'rankings.raceEventId', 'events.id')
                      ->where('rankings.type', 2)
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

        $year = False;
        if ($year) {
            $timespan = Ranking::getRankingYearTimespan($year);
            $queryBuilder = $queryBuilder->whereBetween('events.startDate', $timespan);
        }

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

        return view('front.hypermedia_statistics', [
            'statsCategorySlug' => $statsCategory,
            'data' => $groupedByAthletes->map(function ($item) {
                return $item->slice(0, 30);
            })
        ]);
    }

    public function statsVictories(Request $request, string $year = 'current-season') {
        $queryBuilderYears = DB::table('race_events')
            ->select('race_events.year')
            ->whereRaw('race_events.startDate < CURRENT_DATE')
            ->groupBy('race_events.year')
            ->orderBy('race_events.year', 'desc');
        $years = $queryBuilderYears->get()->map(function ($item) {
            return $item->year;
        });

        $queryBuilderVictories = DB::table('rankings')
                      ->join('athletes', 'rankings.athleteId', 'athletes.id')
                      ->join('countries', 'athletes.countryId', 'countries.id')
                      ->join('categories', 'rankings.categoryId', 'categories.id')
                      ->join('race_events as events', 'rankings.raceEventId', 'events.id')
                      ->where('rankings.type', 2)
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
            $queryBuilderVictories = $queryBuilderVictories->whereBetween('events.startDate', $timespan);
        }

        $groupedByCategories = $queryBuilderVictories->get()->groupBy('categoryName');

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

    public function statsRaceDays(Request $request, string $year = 'current-season') {

        $queryBuilderYears = DB::table('race_events')
            ->select('race_events.year')
            ->whereRaw('race_events.startDate < CURRENT_DATE')
            ->groupBy('race_events.year')
            ->orderBy('race_events.year', 'desc');
        $years = $queryBuilderYears->get()->map(function ($item) {
            return $item->year;
        });

        $queryBuilderRaceDays = DB::table('race_events as events')
                      ->selectRaw('athletes.id as athleteId, athletes.firstName, athletes.lastName, athletes.image, athletes.slug, athletes.gender, countries.code as countryCode, categories.name as catName, rankings.rank, rankings.rankingCategoryId, rankings.points, events.id as eventId, events.name as eventName, events.startDate as eventStartDate, events.endDate, DATEDIFF(events.endDate, events.startDate) + 1 as eventDuration')
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
                      ->where(function($query) {
                          $query->whereIn('rankings.rankingCategoryId', [1, 2, 3, 4, 5, 7, 8, 9, 10, 11, 12, 13])
                                ->orWhere('rankings.rankingCategoryId', null);
                      })
                      // ->where('athletes.id', 126)
                      ->whereRaw('DATEDIFF(events.endDate, events.startDate) + 1 > 0');

        if ($year != 'all-seasons') {
            $year = ($year == 'current-season' ? $years->first() : (int)$year);

            if (!$years->contains($year)) {
                return abort(404);
            }

            $timespan = Ranking::getRankingYearTimespan($year);
            $queryBuilderRaceDays = $queryBuilderRaceDays->whereBetween('events.startDate', $timespan);
        }

        $groupedByCategories = $queryBuilderRaceDays->get()->groupBy('catName');

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

    public function statsPointsPerAge(Request $request, string $year = 'current-season') {

        $queryBuilder  = DB::table('race_events')
            ->select('race_events.year')
            ->whereRaw('race_events.startDate < CURRENT_DATE')
            ->groupBy('race_events.year')
            ->orderBy('race_events.year', 'desc');
        $years = $queryBuilder->get()->map(function ($item) {
            return $item->year;
        });

        $queryBuilder = DB::table('race_events as events')
            ->select(
                'athletes.id as athleteId',
                'athletes.firstName',
                'athletes.lastName',
                'athletes.image',
                'athletes.slug',
                'athletes.gender',
                'athletes.dateOfBirth',
                'countries.code as countryCode',
                'categories.name as catName',
                'rankings.points'
            )
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
            ->leftJoin('race_types as types', 'types.id', 'events.type')
            ->whereIn('categories.id', [1, 2]);

        if ($year != 'all-seasons') {
            $year = ($year == 'current-season' ? $years->first() : (int)$year);

            if (!$years->contains($year)) {
                return abort(404);
            }

            $timespan = Ranking::getRankingYearTimespan($year);
            $queryBuilder = $queryBuilder->whereBetween('events.startDate', $timespan);
        }

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

        // dd($groupedByAthletes->first()->first());

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

        // dd($groupedByAge);

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

        // dd($groupedByAgeWithStats);

        // $groupedByAge = $groupedByAthletes->groupBy

        // $data = $groupedByAthletes
        //     ->map(function ($item) {
        //         return $item->slice(0, 30);
        //     })
        //     ->filter(function ($item) {
        //         return $item->first()['qty'] > 0;
        //     });

        // dd($groupedByAge);

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
            'metric' => ['race days', '']
        ]);
    }



    public function statsGCVictories(Request $request, string $year = 'current-season') {

        $queryBuilderYears = DB::table('race_events')
            ->select('race_events.year')
            ->whereRaw('race_events.startDate < CURRENT_DATE')
            ->groupBy('race_events.year')
            ->orderBy('race_events.year', 'desc');
        $years = $queryBuilderYears->get()->map(function ($item) {
            return $item->year;
        });

        $queryBuilderGCWins = DB::table('race_events as events')
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
                      ->where('rankings.rankingCategoryId', 6)
                      ->where('entries.rank', 1);

        if ($year != 'all-seasons') {
            $year = ($year == 'current-season' ? $years->first() : (int)$year);

            if (!$years->contains($year)) {
                return abort(404);
            }

            $timespan = Ranking::getRankingYearTimespan($year);
            $queryBuilderGCWins = $queryBuilderGCWins->whereBetween('events.startDate', $timespan);
        }

        $groupedByCategories = $queryBuilderGCWins->get()->groupBy('catName');

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

    public function statsWCVictories(Request $request, string $year = 'current-season') {

        $queryBuilderYears = DB::table('race_events')
            ->select('race_events.year')
            ->whereRaw('race_events.startDate < CURRENT_DATE')
            ->groupBy('race_events.year')
            ->orderBy('race_events.year', 'desc');
        $years = $queryBuilderYears->get()->map(function ($item) {
            return $item->year;
        });

        $queryBuilderWCWins = DB::table('race_events as events')
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
                      ->where('rankings.rankingCategoryId', 1)
                      ->where('entries.rank', 1);


        if ($year != 'all-seasons') {
            $year = ($year == 'current-season' ? $years->first() : (int)$year);

            if (!$years->contains($year)) {
                return abort(404);
            }

            $timespan = Ranking::getRankingYearTimespan($year);
            $queryBuilderWCWins = $queryBuilderWCWins->whereBetween('events.startDate', $timespan);
        }

        $groupedByCategories = $queryBuilderWCWins->get()->groupBy('catName');

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

    public function statsChocolates(Request $request, string $year = 'current-season') {

        $queryBuilderYears = DB::table('race_events')
            ->select('race_events.year')
            ->whereRaw('race_events.startDate < CURRENT_DATE')
            ->groupBy('race_events.year')
            ->orderBy('race_events.year', 'desc');
        $years = $queryBuilderYears->get()->map(function ($item) {
            return $item->year;
        });

        $queryBuilderChocolates = DB::table('race_events as events')
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
                      ->join('ranking_categories', 'ranking_categories.id', 'events.rankingCategoryId')
                      ->whereNotIn('ranking_categories.id', [3, 12])
                      ->whereIn('categories.id', [1, 2, 23, 24, 25, 26])
                      ->where('entries.rank', 4);


        if ($year != 'all-seasons') {
            $year = ($year == 'current-season' ? $years->first() : (int)$year);

            if (!$years->contains($year)) {
                return abort(404);
            }

            $timespan = Ranking::getRankingYearTimespan($year);
            $queryBuilderChocolates = $queryBuilderChocolates->whereBetween('events.startDate', $timespan);
        }

        $groupedByCategories = $queryBuilderChocolates->get()->groupBy('catName');

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

    public function statsTopTens(Request $request, string $year = 'current-season') {

        $queryBuilderYears = DB::table('race_events')
            ->select('race_events.year')
            ->whereRaw('race_events.startDate < CURRENT_DATE')
            ->groupBy('race_events.year')
            ->orderBy('race_events.year', 'desc');
        $years = $queryBuilderYears->get()->map(function ($item) {
            return $item->year;
        });

        $queryBuilderTopTens = DB::table('race_events as events')
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
                      ->join('ranking_categories', 'ranking_categories.id', 'events.rankingCategoryId')
                      ->whereNotIn('ranking_categories.id', [3, 12])
                      ->whereIn('categories.id', [1, 2, 23, 24, 25, 26])
                      ->whereIn('entries.rank', [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);

        if ($year != 'all-seasons') {
            $year = ($year == 'current-season' ? $years->first() : (int)$year);

            if (!$years->contains($year)) {
                return abort(404);
            }

            $timespan = Ranking::getRankingYearTimespan($year);
            $queryBuilderTopTens = $queryBuilderTopTens->whereBetween('events.startDate', $timespan);
        }

        $groupedByCategories = $queryBuilderTopTens->get()->groupBy('catName');

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

    public function statsCountriesRacedIn(Request $request, string $year = 'current-season') {

        $queryBuilder = DB::table('race_events')
            ->select('race_events.year')
            ->whereRaw('race_events.startDate < CURRENT_DATE')
            ->groupBy('race_events.year')
            ->orderBy('race_events.year', 'desc');
        $years = $queryBuilder->get()->map(function ($item) {
            return $item->year;
        });

        $queryBuilder = DB::table('race_events as events')
                      ->selectRaw('athletes.id as athleteId, athletes.firstName, athletes.lastName, athletes.image, athletes.slug, athletes.gender, countries.code as countryCode, athCtr.code as athleteCountryCode, categories.name as catName, events.startDate as eventStartDate, events.endDate, DATEDIFF(events.endDate, events.startDate) + 1 as eventDuration')
                      ->join('race_event_participants as participants', 'participants.raceEventId', 'events.id')
                      ->join('athletes', 'participants.athleteId', 'athletes.id')
                      ->join('categories', 'categories.id', 'participants.categoryId')
                      ->leftJoin('countries', 'countries.id', 'events.countryId')
                      ->leftJoin('countries as athCtr', 'athCtr.id', 'athletes.countryId'  )
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

        if ($year != 'all-seasons') {
            $year = ($year == 'current-season' ? $years->first() : (int)$year);

            if (!$years->contains($year)) {
                return abort(404);
            }

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

    public function statsPointsPerRaceday(Request $request, string $year = 'current-season') {

        $queryBuilder = DB::table('race_events')
            ->select('race_events.year')
            ->whereRaw('race_events.startDate < CURRENT_DATE')
            ->groupBy('race_events.year')
            ->orderBy('race_events.year', 'desc');
        $years = $queryBuilder->get()->map(function ($item) {
            return $item->year;
        });

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

    public function statsActiveAthletes(Request $request, string $rankingCategory = 'all') {

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
            $queryBuilder = $queryBuilder->where('rankings.rankingCategoryId', 1);
        } else {
            $queryBuilder = $queryBuilder->whereIn('rankings.rankingCategoryId', [6, 7]);
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

    public function statsOldestAthletes(Request $request, string $rankingCategory = 'all') {

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
            $queryBuilder = $queryBuilder->where('rankings.rankingCategoryId', 1);
        } else {
            $queryBuilder = $queryBuilder->whereIn('rankings.rankingCategoryId', [6, 7]);
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

    public function statsYoungestAthletes(Request $request, string $rankingCategory = 'all') {

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
            $queryBuilder = $queryBuilder->where('rankings.rankingCategoryId', 1);
        } else {
            $queryBuilder = $queryBuilder->whereIn('rankings.rankingCategoryId', [6, 7]);
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

    public function statsCountriesByWins(Request $request, string $year = 'current-season') {

        $queryBuilder = DB::table('race_events')
            ->select('race_events.year')
            ->whereRaw('race_events.startDate < CURRENT_DATE')
            ->groupBy('race_events.year')
            ->orderBy('race_events.year', 'desc');
        $years = $queryBuilder->get()->map(function ($item) {
            return $item->year;
        });

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

        if ($year != 'all-seasons') {
            $year = ($year == 'current-season' ? $years->first() : (int)$year);

            if (!$years->contains($year)) {
                return abort(404);
            }

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

        $categories = collect(['Men', 'Women', 'Men U23', 'Women U23', 'U20 Men', 'U20 Women', 'U18 Men', 'U18 Women'])
        ->filter(function ($item, $key) use ($groupedByCountries) {
            return $groupedByCountries->keys()->contains($item);
        });

        return view('front.statistics.countries_seasonal', [
            'statsCategorySlug' => 'countries-by-wins',
            'data' => $groupedByCountries->map(function ($item) {
                return $item->slice(0, 30);
            }),
            'year' => $year,
            'years' =>  $years,
            'categories' => $categories,
            'metric' => ['victories', 'Number of victories achieved by athletes representing the country.']
        ]);
    }

    public function statsCountriesByWinners(Request $request, string $year = 'current-season') {

        $queryBuilder = DB::table('race_events')
            ->select('race_events.year')
            ->whereRaw('race_events.startDate < CURRENT_DATE')
            ->groupBy('race_events.year')
            ->orderBy('race_events.year', 'desc');
        $years = $queryBuilder->get()->map(function ($item) {
            return $item->year;
        });

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

        if ($year != 'all-seasons') {
            $year = ($year == 'current-season' ? $years->first() : (int)$year);

            if (!$years->contains($year)) {
                return abort(404);
            }

            $timespan = Ranking::getRankingYearTimespan($year);
            $queryBuilder = $queryBuilder->whereBetween('events.startDate', $timespan);
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
            'statsCategorySlug' => 'countries-by-winners',
            'data' => $groupedByCountries->map(function ($item) {
                return $item->slice(0, 30);
            }),
            'year' => $year,
            'years' =>  $years,
            'categories' => $categories,
            'metric' => ['winners', 'Number of winning athletes (ie., those with at least one victory) representing the country.']
        ]);
    }

    public function statsCountriesBySkimo(Request $request, string $year = 'current-season') {

        $minYear = 1900;

        $queryBuilder = DB::table('race_events')
        ->select('race_events.year')
        ->whereRaw('race_events.startDate < CURRENT_DATE')
        ->groupBy('race_events.year')
        ->orderBy('race_events.year', 'desc');
        $years = $queryBuilder->get()->map(function ($item) {
            return $item->year;
        });

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
            'statsCategorySlug' => 'countries-skimo-scores',
            'data' => $groupedByCountries,
            'year' => $year,
            'years' =>  $years,
            'categories' => $categories,
            'metric' => ['points', '']
        ]);
    }

    public function statsPointsPerMonth(Request $request, $monthYear = null) {

        if (!$monthYear) {
            $monthYear = date('m-Y');
        }

        // dd(explode('-',$monthYear)[1]);

        $queryBuilder = DB::table('race_events')
        ->select('race_events.year')
        ->whereRaw('race_events.startDate < CURRENT_DATE')
        ->groupBy('race_events.year')
        ->orderBy('race_events.year', 'desc');

        $years = $queryBuilder->get()->map(function ($item) {
            return $item->year;
        });

        $month =  explode('-', $monthYear)[0];
        $year =  explode('-', $monthYear)[1];

        if (!$years->contains($year)) {
            return abort(404);
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

    public function worldCup(Request $request, int $year = 0) {

        if (!$year) {
            $year = date("Y");
        }

        $ismfWorldCupIds = Race::getIsmfWorldCupIds();

        $events = RaceEvent::whereIn('raceId', $ismfWorldCupIds)
            ->with(['raceType', 'country'])
            ->whereBetween('startDate', [($year - 1).'-11-01 00:00:00', $year.'-05-31 23:59:59'])
            ->where('is_visible', true)
            ->whereNotNull('type')
            ->orderBy('startDate', 'asc')
            ->get();
        if (!count($events)) {
            return abort(404);
        }

        $years = DB::table('race_events')
            ->select(DB::raw('YEAR(startDate) as year'))
            ->where('is_visible', true)
            ->whereIn('raceId', $ismfWorldCupIds)
            ->groupBy(DB::raw('YEAR(startDate)'))
            ->orderBy('year', 'asc')
            ->get()
            ->toArray();

        return view('front.world-cup', [
            'events' =>  $events,
            'year' => $year,
            'years' => array_map(function($item){return $item->year;}, $years),
        ]);
    }

    public function raceOverview(Request $request, string $slug, int $year = 0) {

        $race = Race::where('slug', $slug)->first();

        if (!$race) {
            return abort(404);
        }

        $years = array_map(
            function($item){
                return $item->year;
            }, DB::table('race_events')
                ->select(DB::raw('YEAR(startDate) as year'))
                ->where('is_visible', true)
                ->where('raceId', $race->id)
                ->groupBy(DB::raw('YEAR(startDate)'))
                ->orderBy('year', 'asc')
                ->get()
                ->toArray()
        );

        if (!$year) {
            $year = date("Y");

            if (!in_array($year, $years)) {
                $year = $years[count($years) - 1];
            }
        }

        list($startDate, $endDate) = $this->parseStartEndDates($race->yearStart, $race->yearEnd, $year);

        $events = RaceEvent::where('raceId', $race->id)
            ->with(['raceType', 'country'])
            ->whereBetween('startDate', [$startDate, $endDate])
            ->where('is_visible', true)
            ->whereNotNull('type')
            ->orderBy('startDate', 'asc')
            ->get();

        if (!count($events)) {
            return abort(404);
        }


        return view('front.race-overview', [
            'race' => $race,
            'events' =>  $events,
            'year' => $year,
            'years' => $years,
        ]);
    }

    public function parseStartEndDates($yearStart, $yearEnd, $year) {
        if (!$yearStart) {
            $yearStart = new Carbon("2019-01-01 00:00:00");
        }

        if (!$yearEnd) {
            $yearEnd = new Carbon("2019-12-31 23:59:59");
        }

        $ysYear = $yearStart->year;
        $yeYear = $yearEnd->year;

        if ($ysYear < $yeYear) {
            $yearStart->year = $year - 1;
        } else {
            $yearStart->year = $year;
        }

        $yearEnd->year = $year;

        $start = $yearStart->isoFormat('YYYY-MM-DD HH:mm:ss');
        $end = $yearEnd->isoFormat('YYYY-MM-DD HH:mm:ss');

        return [
            $start,
            $end,
        ];
    }

    // New stuff by MV
    public function topResults(Athlete $athlete) {
        // return the top results of the athlete
        // top results are grouped by races and order by category IDs as follows: 6, 5, 1, 7, 8, 2, 4, 11, 3, 10
        $queryBuilder = DB::table('rankings')
             ->where('athleteId', '=', $athlete->id)
             ->whereNotNull('rankings.rankingCategoryId')
             ->whereRaw('rankings.rank in (1, 2, 3)')
             ->join('race_events as events', 'events.id', 'rankings.raceEventId')
             ->join('races', 'races.id', 'events.raceId')
             ->whereNotIn('races.rankingCategoryId', [10, 7])
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
                         'rankings.rankingCategoryId as rankingCategory, '.
                         'events.name as eventName, '.
                         'events.slug as eventSlug, '.
                         'events.startDate as date, '.
                         'rankings.points, '.
                         'rankings.rank');

        // get php collection object from querybuilder
        $collection = $queryBuilder->get();

        // dd($collection);

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

        // dd($grouppedByRace);

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
                9 => 11
            ];

            // dd($b[0]);

            $aScore = $rnkCatsScores[$a[0]["rankingCategoryId"]] ?? 12; // returns the lowest score of 12, if there is no mapping
            $bScore = $rnkCatsScores[$b[0]["rankingCategoryId"]] ?? 12;

            return $aScore - $bScore;
        });

        return $sorted->values()->slice(0, 8);
    }
}
