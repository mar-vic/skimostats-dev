<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Category;
use App\Ranking;
use App\RankingTable;
use App\RaceType;

use App\Enums\RankingType;
use Illuminate\Support\Facades\DB;

class RankingController extends Controller
{
    public function temp() {
        return view('front.rankings-temp');
    }

    public static function getActualYear() {
        $year = date("Y");

        if (date("m") > 9) {
            $year++;
        }

        return (int)$year;
    }


    public function ranking(Request $request) {
        return $this->rankingCategory($request, RankingType::SKIMO_STATS, Category::where('slug', 'men')->first(), $this->getActualYear());
    }

    public function rankingCategorySlug(Request $request, string $rankingType, string $category, int $year = null) {
        if (!$year) {
            $year = $this->getActualYear();
        }
        $mapType = [
            'ismf' => RankingType::ISMF,
            'skimostats' => RankingType::SKIMO_STATS,
        ];

        if (!in_array($rankingType, array_keys($mapType))) {
            return abort(404);
        }

        $cat = Category::where('slug', $category)->first();
        if (!$cat) {
            return abort(404);
        }

        return $this->rankingCategory($request, $mapType[$rankingType], $cat, $year);
    }

    public function rankingYear(Request $request, string $rankingType, int $year = null, string $category) {
        return $this->rankingCategorySlug($request, $rankingType, $category, $year);
    }

    public function rankingAllTime(Request $request, string $category) {
        $cat = Category::where('slug', $category)->whereIn('id', [1,2])->first();
        if (!$cat) {
            return abort(404);
        }

        return $this->rankingCategory($request, RankingType::SKIMO_STATS, $cat, 0, 'all-time');
    }

    public function rankingAllTimeISMF(Request $request, string $category)
    {
        $cat = Category::where('slug', $category)->whereIn('id', [1, 2, 3, 4, 5, 6, 25, 26])->first();
        if (!$cat) {
            return abort(404);
        }

        return $this->rankingCategory($request, RankingType::ISMF, $cat, 0, 'all-time');
    }

    public function rankingRaceType(Request $request, string $raceType, string $category) {
        $type = RaceType::where('slug', $raceType)->first();
        $cat = Category::where('slug', $category)->whereIn('id', [1,2])->first();
        if (!$type || !$cat) {
            return abort(404);
        }

        return $this->rankingCategory($request, 1, $cat, 0, 'race-type', $type->id);
    }

    public function rankingRaceTypeISMF(Request $request, string $raceType, string $category) {
        $type = RaceType::where('slug', $raceType)->first();
        $cat = Category::where('slug', $category)->whereIn('id', [1, 2, 3, 4, 5, 6, 25, 26])->first();

        if (!$type || !$cat) {
            return abort(404);
        }

        return $this->rankingCategory($request, RankingType::ISMF, $cat, 0, 'race-type', $type->id);
    }


    public function rankingRaceTypeYear(Request $request, int $year, string $raceType, string $category) {
        $type = RaceType::where('slug', $raceType)->first();
        $cat = Category::where('slug', $category)->whereIn('id', [1,2])->first();
        if (!$type || !$cat) {
            return abort(404);
        }

        return $this->rankingCategory($request, 1, $cat, $year, 'race-type', $type->id);
    }

    public function rankingRaceTypeYearISMF(Request $request, int $year, string $raceType, string $category)
    {
        $type = RaceType::where('slug', $raceType)->first();
        $cat = Category::where('slug', $category)->whereIn('id', [1, 2, 3, 4, 5, 6, 25, 26])->first();
        if (!$type || !$cat) {
            return abort(404);
        }

        return $this->rankingCategory($request, RankingType::ISMF, $cat, $year, 'race-type', $type->id);
    }

    public function rankingCategory(Request $request, int $rankingType, Category $category, int $year = null, string $filter = null, int $entityId = null) {

        // if (!$year) {
        //     dd($year.$filter.$entityId);
        //     $year = $this->getActualYear();
        // }

        $minYear = $rankingType == RankingType::ISMF ? 2018 : 1900;

        if (!$filter && $year < $minYear) {
            return abort(404);
        }

        $categories = Category::whereIn('id',
            $rankingType == 1
            ? [
                1, 2,
            ]
            : [
                1,2,3,4,5,6,25,26
            ]
        )->get();

        if ($filter == 'race-type' && $entityId) {
            $rankingQB = DB::table('rankings as r')
                ->select(
                    DB::raw('SUM(r.points) as pts'),
                    'r.athleteId',
                    'r.rank',
                    'a.firstName',
                    'a.lastName',
                    'a.slug as athleteSlug',
                    'c.code as countryCode'
                )
                ->join('athletes as a', 'a.id', 'r.athleteId')
                ->leftJoin('countries as c', 'c.id', 'a.countryId')
                ->where('r.type', $rankingType)
                ->where('r.raceTypeId', $entityId)
                ->where('r.categoryId', $category->id)
                ->groupBy('r.athleteId')
                ->havingRaw('pts > 0')
                ->orderBy('pts', 'desc')
                ->orderBy('a.lastName', 'asc');
            if ($year != 0) {
                $rankingQB->whereBetween('r.obtainedAt', Ranking::getRankingYearTimeSpan($year));
            }
        } else if ($filter == 'all-time') {
            $rankingQB = DB::table('ranking_tables as r')
                ->select(
                    DB::raw('SUM(r.points) as pts'),
                    'r.athleteId',
                    'r.rank',
                    'a.firstName',
                    'a.lastName',
                    'a.slug as athleteSlug',
                    'c.code as countryCode'
                )
                ->join('athletes as a', 'a.id', 'r.athleteId')
                ->leftJoin('countries as c', 'c.id', 'a.countryId')
                ->where('r.type', $rankingType)
                ->where('r.categoryId', $category->id)
                ->groupBy('r.athleteId')
                ->havingRaw('pts > 0')
                ->orderBy('pts', 'desc')
                ->orderBy('a.lastName', 'asc');
        } else {
            $rankingQB = DB::table('ranking_tables as r')
                ->select(
                    DB::raw('r.points as pts'),
                    'r.athleteId',
                    'r.rank',
                    'r.rankBefore',
                    'a.firstName',
                    'a.lastName',
                    'a.slug as athleteSlug',
                    'c.code as countryCode'
                )
                ->join('athletes as a', 'a.id', 'r.athleteId')
                ->leftJoin('countries as c', 'c.id', 'a.countryId')
                ->where('r.type', $rankingType)
                ->where('r.year', $year)
                ->where('r.categoryId', $category->id)
                ->groupBy('r.athleteId')
                ->havingRaw('pts > 0')
                ->orderBy('r.rank', 'asc');
        }

        $ranking = $rankingQB
            ->get();

        $years = array_map(
            function($item) {
                return $item->year;
            },
            DB::table('rankings')
                ->select(DB::raw('YEAR(obtainedAt) as year'))
                ->where('type', $rankingType)
                ->groupBy(DB::raw('YEAR(obtainedAt)'))
                ->havingRaw('year >= ?', [$minYear])
                ->orderBy(DB::raw('YEAR(obtainedAt)'))
                ->get()
                ->toArray()
        );

        if (!in_array(date("Y"), $years)) {
            $years[] = date("Y");
        }
        if (!in_array(date("Y") + 1, $years) && date("m") > 9) {
            $years[] = date("Y") + 1;
        }

        rsort($years);

        return view('front.rankings', [
            'categories' => $categories,
            'category' => $category,

            'rankingType' => $rankingType,
            'rankingTypeText' => $rankingType == 2 ? 'ISMF World Cup' : 'SkiMo Stats',

            'entityId' => $entityId,
            'raceType' => RaceType::where('id', $entityId)->first(),
            'raceTypes' => RaceType::where('slug','!=','relay-race')->orderBy('name', 'asc')->get(),

            'ranking' => $ranking,
            'year' => $year,
            'years' => $years,
            'filter' => $filter,
        ]);
    }

    public function getRankingCategoryData(int $rankingType, Category $category, $year = null, string $filter = null, int $entityId = null) {
        if (!$year) {
            $year = $this->getActualYear();
        }

        $minYear = $rankingType == RankingType::ISMF ? 2018 : 1900;

        if (!($filter || (int)$year == 0) && $year < $minYear) {
            return abort(404);
        }

        $categoriesQB = DB::table('categories as c')
            ->select([
                'c.id',
                'c.name',
                'c.slug',
                DB::raw('COUNT(rt.id) as entriesCount')
            ])
            ->join('ranking_tables as rt', 'rt.categoryId', 'c.id')
            ->where('rt.type', $rankingType)
            ->whereIn('c.id',
                $rankingType == 1
                ? [
                    1, 2,
                ]
                : [
                    1,2,3,4,5,6,25,26
                ]
            )->groupBy('c.id');

        if ($year !== 'all-time') {
            $categoriesQB->where('rt.year', $year);
        }

        $categories = $categoriesQB->get();

        if ($filter == 'race-type' && $entityId) {
            $rankingQB = DB::table('rankings as r')
                ->select(
                    DB::raw('SUM(r.points) as pts'),
                    'r.athleteId',
                    'r.rank',
                    'a.firstName',
                    'a.lastName',
                    'a.slug as athleteSlug',
                    'c.code as countryCode'
                )
                ->join('athletes as a', 'a.id', 'r.athleteId')
                ->leftJoin('countries as c', 'c.id', 'a.countryId')
                ->where('r.type', $rankingType)
                ->where('r.raceTypeId', $entityId)
                ->where('r.categoryId', $category->id)
                ->groupBy('r.athleteId')
                ->havingRaw('pts > 0')
                ->orderBy('pts', 'desc')
                ->orderBy('a.lastName', 'asc');
            if ($year != 0) {
                $rankingQB->whereBetween('r.obtainedAt', Ranking::getRankingYearTimeSpan($year));
            }
        } else if ($filter == 'all-time' || $year === 'all-time') {
            $rankingQB = DB::table('ranking_tables as r')
                ->select(
                    DB::raw('SUM(r.points) as pts'),
                    'r.athleteId',
                    'r.rank',
                    'a.firstName',
                    'a.lastName',
                    'a.slug as athleteSlug',
                    'c.code as countryCode'
                )
                ->join('athletes as a', 'a.id', 'r.athleteId')
                ->leftJoin('countries as c', 'c.id', 'a.countryId')
                ->where('r.type', $rankingType)
                ->where('r.categoryId', $category->id)
                ->groupBy('r.athleteId')
                ->havingRaw('pts > 0')
                ->orderBy('pts', 'desc')
                ->orderBy('a.lastName', 'asc');
        } else {
            $rankingQB = DB::table('ranking_tables as r')
                ->select(
                    DB::raw('r.points as pts'),
                    'r.athleteId',
                    'r.rank',
                    'r.rankBefore',
                    'a.firstName',
                    'a.lastName',
                    'a.slug as athleteSlug',
                    'c.code as countryCode'
                )
                ->join('athletes as a', 'a.id', 'r.athleteId')
                ->leftJoin('countries as c', 'c.id', 'a.countryId')
                ->where('r.type', $rankingType)
                ->where('r.year', $year)
                ->where('r.categoryId', $category->id)
                ->groupBy('r.athleteId')
                ->havingRaw('pts > 0')
                ->orderBy('r.rank', 'asc');
        }

        $ranking = $rankingQB
            ->get();


        if ($filter == 'all-time' || $year === 'all-time') {
            $i = 0;
            foreach ($ranking as $row) {
                $i++;
                $row->rank = $i;
            }
        }

        $years = array_map(
            function($item) {
                return $item->year;
            },
            DB::table('rankings')
                ->select(DB::raw('YEAR(obtainedAt) as year'))
                ->where('type', $rankingType)
                ->groupBy(DB::raw('YEAR(obtainedAt)'))
                ->havingRaw('year >= ?', [$minYear])
                ->orderBy(DB::raw('YEAR(obtainedAt)'))
                ->get()
                ->toArray()
        );

        if (!in_array(date("Y"), $years)) {
            $years[] = date("Y");
        }
        if (!in_array(date("Y") + 1, $years) && date("m") > 9) {
            $years[] = date("Y") + 1;
        }

        rsort($years);

        return [
            'categories' => $categories,
            'category' => $category,

            'rankingType' => $rankingType,
            'rankingTypeText' => $rankingType == 2 ? 'ISMF World Cup' : 'SkiMo Stats',

            'entityId' => $entityId,
            'raceType' => RaceType::where('id', $entityId)->first(),
            'raceTypes' => RaceType::where('slug','!=','relay-race')->orderBy('name', 'asc')->get(),

            'ranking' => $ranking,
            'year' => $year,
            'years' => $years,
            'filter' => $filter,
        ];
    }

    public function getRankingsV1List(Request $request) {
        $data = $this->getRankingCategoryData(
            $request->get('rankingType', RankingType::SKIMO_STATS),
            Category::where('slug', $request->get('category', 'men'))->first(),
            $request->get('year', $this->getActualYear()),
            $request->get('filter')
        );

        return $data;
    }

    public function getHomepageRankings(Request $request) {
        $rankingType = RankingType::SKIMO_STATS;

        $year = date("Y");

        if (date("m") == 12 && date("d") > 9) {
            $year++;
        }

        $categories = Category::whereIn('id', [
            1,2,
        ])->get();

        $result = [];

        foreach ($categories as $category) {
            $ranking = DB::table('ranking_tables as r')
                ->select(
                    'r.rank',
                    'r.rankBefore',
                    'r.points as pts',
                    'r.athleteId',
                    'a.firstName',
                    'a.lastName',
                    'a.slug as athleteSlug',
                    'c.code as countryCode'
                )
                ->join('athletes as a', 'a.id', 'r.athleteId')
                ->leftJoin('countries as c', 'c.id', 'a.countryId')
                ->where('r.type', $rankingType)
                ->where('r.year', $year)
                ->where('r.categoryId', $category->id)
                ->groupBy('r.athleteId')
                ->havingRaw('pts > 0')
                ->orderBy('r.rank', 'asc')
                ->orderBy('a.lastName', 'asc')
                ->limit(10)
                ->get();

            $result[] = [
                'category' => $category,
                'ranking' => $ranking,
            ];
        }

        return [
            'year' => $year,
            'ranking' => $result
        ];
    }
    public function getHomepageRankingsNations(Request $request) {
        $rankingType = RankingType::SKIMO_STATS;
        $year = date("Y");

        if (date("m") == 12 && date("d") > 9) {
            $year++;
        }

        $ranking = DB::table('ranking_tables as r')
            ->select(
                DB::raw('SUM(r.points) as pts'),
                'c.code',
                'c.name'
            )
            ->join('athletes as a', 'a.id', 'r.athleteId')
            ->join('countries as c', 'c.id', 'a.countryId')
            ->where('r.type', $rankingType)
            ->where('r.year', $year)
            ->whereIn('r.categoryId', [1, 2])
            ->groupBy('c.id')
            ->havingRaw('pts > 0')
            ->orderBy('pts', 'desc')
            ->orderBy('a.lastName', 'asc')
            ->limit(10)
            ->get();

        $result[] = [
            'ranking' => $ranking,
        ];

        return $result;
    }
}
