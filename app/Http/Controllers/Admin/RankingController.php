<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Auth;
use Carbon\Carbon;

use App\Enums\RankingType;

use App\Athlete;
use App\Category;
use App\Race;
use App\RaceEvent;
use App\RaceEventEntry;
use App\RaceEventParticipant;
use App\RaceEventTeam;
use App\Ranking;
use App\RankingTable;
use Illuminate\Support\Facades\DB;

class RankingController extends Controller
{
    public function index() {
        return view('admin.rankings.index');
    }

    public function refreshAllIsmfRankings(Request $request) {
        $type = RankingType::ISMF;
        $user = Auth::user();

        // Script start
        $rustart = getrusage();

        // delete all ismf ranking entries
        Ranking::where('type', $type)->delete();

        // create new entries
        $ismfRaceIds = Race::getIsmfWorldCupIds();

        $entries = DB::table('race_event_participants as p')
            ->select(
                DB::raw('COALESCE(e.rank, ee.rank) as rank'),
                'p.athleteId',
                DB::raw('COALESCE(ev.endDate, ev.startDate) as obtainedAt'),
                'p.id as participantId',
                'ev.type as raceTypeId',
                'ev.raceId',
                'ev.id as raceEventId',
                DB::raw('COALESCE(e.categoryId, ee.categoryId) as categoryId')
            )
            ->leftJoin('race_event_entries as e', 'e.raceEventParticipantId', 'p.id')
            ->leftJoin('race_event_teams as t', 'p.raceEventTeamId', 't.id')
            ->leftJoin('race_event_entries as ee', 'ee.raceEventTeamId', 't.id')
            ->join('race_events as ev', function ($join) {
                $join->on('ev.id', '=', 'e.raceEventId')
                    ->orOn('ev.id', '=', 'ee.raceEventId');
            })
            ->whereNotNull('p.athleteId')
            ->whereIn('ev.raceId', $ismfRaceIds)
            ->having('rank', '>=', 1)
            ->having('rank', '<', 51)
            // ->limit(10)
            ->get();

        // dd($entries);

        $rankings = [];
        $now = Carbon::now();
        foreach ($entries as $entry) {
            $ranking = [
                'type' => $type,
                'rank' => $entry->rank,
                'points' => Ranking::mapIsmfPointsToRank($entry->rank),
                'athleteId' => $entry->athleteId,
                'obtainedAt' => $entry->obtainedAt,
                'participantId' => $entry->participantId,
                'categoryId' => $entry->categoryId,
                'raceTypeId' => $entry->raceTypeId,
                'raceEventId' => $entry->raceEventId,
                'raceId' => $entry->raceId,
                'addedBy' => $user->id,
                'created_at' => $now,
                'updated_at' => $now
            ];

            // $rnk = Ranking::create($ranking);
            DB::table('rankings')->insert($ranking);
            $rankings[] = $ranking;
        }

        // dd($rankings);


        // Code ...

        // Script end
        function rutime($ru, $rus, $index) {
            return ($ru["ru_$index.tv_sec"]*1000 + intval($ru["ru_$index.tv_usec"]/1000))
            -  ($rus["ru_$index.tv_sec"]*1000 + intval($rus["ru_$index.tv_usec"]/1000));
        }

        $ru = getrusage();
        echo count($rankings)." rank entries created.\n";
        echo "This process used " . rutime($ru, $rustart, "utime") .
            " ms for its computations\n";
        echo "It spent " . rutime($ru, $rustart, "stime") .
            " ms in system calls\n";

    }

    public function refreshAllSkimostatsRankings(Request $request) {
        $type = RankingType::SKIMO_STATS;
        $user = Auth::user();

        // Script start
        $rustart = getrusage();

        Ranking::where('type', $type)->delete();

        // create new entries
        $entries = DB::table('race_event_participants as p')
            ->select(
                DB::raw('COALESCE(e.rank, ee.rank) as rank'),
                'p.athleteId',
                DB::raw('COALESCE(ev.endDate, ev.startDate) as obtainedAt'),
                'p.id as participantId',
                'ev.type as raceTypeId',
                'ev.raceId',
                'ev.id as raceEventId',
                DB::raw('COALESCE(e.categoryId, ee.categoryId) as categoryId')
            )
            ->leftJoin('race_event_entries as e', 'e.raceEventParticipantId', 'p.id')
            ->leftJoin('race_event_teams as t', 'p.raceEventTeamId', 't.id')
            ->leftJoin('race_event_entries as ee', 'ee.raceEventTeamId', 't.id')
            ->join('race_events as ev', function ($join) {
                $join->on('ev.id', '=', 'e.raceEventId')
                    ->orOn('ev.id', '=', 'ee.raceEventId')
                    ->orOn('ev.id', '=', 'p.raceEventId');
            })
            ->whereNotNull('p.athleteId')
            ->where(function($qb) {
                $qb->whereNotNull('e.rank')
                    ->orWhereNotNull('ee.rank');
            })
            ->where(function($qb) {
                $qb->where('e.rank', '>=', 1)
                    ->orWhere('ee.rank', '>=', 1);
            })
            ->where(function($qb) {
                $qb->where('e.rank', '<', 101)
                    ->orWhere('ee.rank', '<', 101);
            })
            ->where(function($qb) {
                $qb->whereIn('e.categoryId', [1, 2])
                    ->orWhereIn('ee.categoryId', [1, 2])
                    ->orWhereIn('p.categoryId', [1, 2]);
            })
            ->get();

        $rankings = [];
        $now = Carbon::now();

        $bulk = [];
        $bulkMax = 300;
        $bulkIndex = 0;

        foreach ($entries as $entry) {
            if ($bulkIndex >= $bulkMax) {
                DB::table('rankings')->insert($bulk);

                $bulk = [];
                $bulkIndex = 0;
            }
            $event = RaceEvent::where('id', $entry->raceEventId)->first();
            $points = 0;
            if ($event) {
                $points = $event->getSkimostatsRankingPointsForRank($entry->rank);
            }

            if ($points == 0) {
                continue;
            }

            $rankingCategoryId = $event->computedRankingCategory ? $event->computedRankingCategory->id : null;

            $ranking = [
                'type' => $type,
                'rank' => $entry->rank,
                'rankingCategoryId' => $rankingCategoryId,
                'points' => $points,
                'athleteId' => $entry->athleteId,
                'obtainedAt' => $entry->obtainedAt,
                'participantId' => $entry->participantId,
                'categoryId' => $entry->categoryId,
                'raceTypeId' => $entry->raceTypeId,
                'addedBy' => $user->id,
                'raceId' => $entry->raceId,
                'raceEventId' => $entry->raceEventId,
                'created_at' => $now,
                'updated_at' => $now
            ];

            // $rnk = Ranking::create($ranking);
            $rankings[] = $ranking;
            $bulk[] = $ranking;

            $bulkIndex++;
        }

        DB::table('rankings')->insert($bulk);

        // dd($rankings);

        // Script end
        function rutime($ru, $rus, $index) {
            return ($ru["ru_$index.tv_sec"]*1000 + intval($ru["ru_$index.tv_usec"]/1000))
            -  ($rus["ru_$index.tv_sec"]*1000 + intval($rus["ru_$index.tv_usec"]/1000));
        }

        $ru = getrusage();
        echo count($rankings)." rank entries created.\n";
        echo "This process used " . rutime($ru, $rustart, "utime") .
            " ms for its computations\n";
        echo "It spent " . rutime($ru, $rustart, "stime") .
            " ms in system calls\n";

    }


    public function updateRankingTable(int $rankingType = null, int $year = null) {
        // Script start
        $rustart = getrusage();

        $rankingTypes = [];
        $rankings = [];
        $categories = [1, 2];

        if ($rankingType == 2) {
            $categories = array_map(function($item){return $item['id'];}, Category::all()->toArray());
        }

        if ($rankingType) {
            $rankingTypes = [$rankingType];
        } else {
            $rankingTypes = [
                RankingType::ISMF,
                RankingType::SKIMO_STATS,
            ];
        }
        foreach ($rankingTypes as $type) {
            $years = [];
            $results = [];

            if (!$year) {
                $yearsRes = DB::table('rankings')
                    ->select(DB::raw('YEAR(obtainedAt) as year'))
                    ->where('type', $type)
                    ->whereIn('categoryId', $categories)
                    ->groupBy(DB::raw('YEAR(obtainedAt)'))
                    ->get();

                $years = array_map(function($item){return $item->year;}, $yearsRes->toArray());

                $thisYear = date("Y");
                if (!in_array($thisYear, $years)) {
                    $years[] = $thisYear;
                }

                $nextYear = date("Y") + 1;
                if (!in_array($nextYear, $years)) {
                    $years[] = $nextYear;
                }

                foreach ($years as $yr) {
                    foreach ($categories as $categoryId) {
                        $results[$categoryId] = $this->updateRankingTableYearResults($type, $yr, $categoryId);
                    }
                }
            } else {
                $years[] = $year;

                foreach ($categories as $categoryId) {
                    $results[$categoryId] = $this->updateRankingTableYearResults($type, $year, $categoryId);
                }
            }

            $rankings[$type] = [
                'years' => $years,
                'results' => $results,
            ];
        }



        function rutime($ru, $rus, $index) {
            return ($ru["ru_$index.tv_sec"]*1000 + intval($ru["ru_$index.tv_usec"]/1000))
            -  ($rus["ru_$index.tv_sec"]*1000 + intval($rus["ru_$index.tv_usec"]/1000));
        }

        $ru = getrusage();
        echo count($rankings)." rank entries created.\n";
        echo "This process used " . rutime($ru, $rustart, "utime") .
            " ms for its computations\n";
        echo "It spent " . rutime($ru, $rustart, "stime") .
            " ms in system calls\n";

        // dd($rankings);
    }

    public function updateRankingTableYearResults(int $type, int $year, int $categoryId) {
        // $entries = DB::table('rankings as r')
        //     ->select(
        //         DB::raw('SUM(r.points) as points,
        //         (SELECT rt.points FROM ranking_tables as rt WHERE rt.athleteId=r.athleteId && categoryId=:categoryId && rt.year=:year LIMIT 1) as pointsBefore,
        //         (SELECT rt.rank FROM ranking_tables as rt WHERE rt.athleteId=r.athleteId && categoryId=:categoryId && rt.year=:year LIMIT 1) as rankBefore,
        //         r.athleteId'),
        //         [
        //             'categoryId' => $categoryId, 'year' => $year,
        //         ]
        //     )
        //     ->whereBetween('r.obtainedAt', [($year - 1) . '-10-01 00:00:00', $year . '-05-01 23:59:59'])
        //     ->where('r.categoryId', $categoryId)
        //     ->where('r.type', $type)
        //     ->groupBy('r.athleteId')
        //     ->orderBy('points', 'DESC')
        //     ->get();

        $rankingYearTimespan = Ranking::getRankingYearTimeSpan($year);

        $entries = DB::select('SELECT
        r.athleteId,
        SUM(r.points) as points,
        (SELECT IF(SUM(r.points)=rt.points, rt.pointsBefore, rt.points) FROM ranking_tables as rt WHERE rt.athleteId=r.athleteId && rt.categoryId=r.categoryId && rt.type=r.type && rt.year=? && rt.points IS NOT NULL LIMIT 1) as pointsBefore,
        (SELECT rt.rank FROM ranking_tables as rt WHERE rt.athleteId=r.athleteId && rt.categoryId=r.categoryId && rt.type=r.type && rt.year=? && rt.rank IS NOT NULL LIMIT 1) as rankBefore,
        (SELECT rt.rankBefore FROM ranking_tables as rt WHERE rt.athleteId=r.athleteId && rt.categoryId=r.categoryId && rt.type=r.type && rt.year=? && rt.rank IS NOT NULL LIMIT 1) as rankBeforeBefore
        FROM rankings as r
        JOIN athletes as a ON a.id=r.athleteId
        WHERE
        r.obtainedAt BETWEEN ? AND ?
        && r.categoryId=?
        && r.`type`=?
        GROUP BY r.athleteId
        ORDER BY points DESC, a.lastName ASC',
        [
            $year,
            $year,
            $year,
            $rankingYearTimespan[0], $rankingYearTimespan[1],
            $categoryId,
            $type,
        ]);

        if(!count($entries)) {
            return [];
        }

        // dd($entries);

        RankingTable::where('year', $year)->where('type', $type)->where('categoryId', $categoryId)->delete();

        $i = 0;
        foreach ($entries as $entry) {
            $i++;
            $newEntry = new RankingTable();
            $newEntry->type = $type;
            $newEntry->year = $year;
            $newEntry->points = $entry->points;
            $newEntry->pointsBefore = $entry->pointsBefore;
            $newEntry->categoryId = $categoryId;
            $newEntry->rank = $i;
            $newEntry->rankBefore = $i == $entry->rankBefore ? $entry->rankBeforeBefore : $entry->rankBefore;
            $newEntry->athleteId = $entry->athleteId;
            $newEntry->lastRankChange = $entry->rankBefore ? (int)$entry->rankBefore - $i : 0;
            $newEntry->save();

            // if ($entry->athleteId == 57 && $type == 1) {
            //     echo $entry->rankBefore." /  RB / ".$year."<br>";
            // }
        }

        return $entries;
    }
}
