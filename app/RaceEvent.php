<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

use Cviebrock\EloquentSluggable\Sluggable;
use \Carbon\Carbon;

// Adding HasFactory trait class
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Helpers\Helper;
use App\RaceCategory;
use App\RaceEventCategory;
use App\RankingCategory;

class RaceEvent extends Model
{
    use HasFactory, Sluggable;

    protected $guarded = ['id'];

    private $cached_ranking_category = null;
    private $cached_ranking_category_fallback_element = null;

    public function sluggable(): array {
        return [
            'slug' => [
                'source' => ['name', 'race_type.name'],
            ],
        ];
    }

    public function race() {
        return $this->belongsTo('App\Race', 'raceId');
    }

    public function country() {
        return $this->belongsTo('App\Country', 'countryId');
    }

    public function entries() {
        return $this->hasMany('App\RaceEventEntry', 'raceEventId');
    }

    public function hasCategory($id) {
        foreach ($this->categories as $cat) {
            if ($cat->id == $id) {
                return true;
            }
        }
        return false;
    }

    public function rankingCategory() {
        return $this->belongsTo('App\RankingCategory', 'rankingCategoryId');
    }

    public function getSkimostatsRankingPointsForRank($rank) {
        $rankingCategory = $this->computedRankingCategory;
        if (!$rankingCategory) {
            return 0;
        }

        $map = json_decode($rankingCategory->rankPointMap, true);

        return $map[$rank] ?? 0;
    }

    public function getComputedRankingCategoryAttribute() {
        if ($this->cached_ranking_category) {
            return $this->cached_ranking_category;
        }
        $this->cached_ranking_category = $this->recursivelyFindRankingCategory($this);

        return $this->cached_ranking_category;
    }

    public function getRankingCategoryFallbackObjectAttribute() {
        return $this->cached_ranking_category_fallback_element;
    }

    public function recursivelyFindRankingCategory(RaceEvent $rc) {
        if (!$rc) {
            return null;
        }

        if ($rc->rankingCategory) {
            $this->cached_ranking_category_fallback_element = $rc;
            return $rc->rankingCategory;
        }

        if ($rc->hasParent()) {
            return $this->recursivelyFindRankingCategory($rc->eventParent);
        } else {
            $this->cached_ranking_category_fallback_element = $rc->race;
            return $rc->race ? $rc->race->rankingCategory : null;
        }

        return null;
    }

    public function scopeShowable($query) {
        return $query->whereNull('type')->where('is_visible', 1);
    }

    public function scopeLatestEntries($query, $limit = 6) {
        return $query
            ->where('is_visible', 1)
            ->whereNotNull('type')
            ->where('startDate', '<', Carbon::now())
            ->orderBy('startDate', 'desc')
            ->limit($limit);
    }

    public function isParent() {
        return !$this->type;
    }

    public function hasStages() {
        return $this->id && (bool)$this->hasStages;
    }

    public function isGeneralClassification() {
        return $this->id && (bool)$this->isGeneralClassification;
    }

    public function isTeamRace() {
        return $this->id && (bool)$this->isTeam;
    }

    public function isVisible() {
        return $this->id && (bool)$this->is_visible;
    }

    public function raceType() {
        return $this->belongsTo('App\RaceType', 'type');
    }

    public function hasParent() {
        return $this->id && (bool)$this->parent;
    }

    public function canHaveCategories() {
        return !$this->isParent();
    }

    public function eventParent() {
        return $this->belongsTo('App\RaceEvent', 'parent');
    }

    public function events() {
        return $this->hasMany('App\RaceEvent', 'parent');
    }

    public function categories() {
        return $this->belongsToMany('App\Category', 'race_event_categories', 'raceEventId', 'categoryId')
            ->orderByRaw('ISNULL(categories.priority)')
            ->orderBy('categories.priority', 'asc')
            ->orderBy('id', 'asc');
    }

    public function getAllStages() {
        $parent = $this->eventParent;

        if ($parent) {
            return array_filter($parent->events->toArray(), function($e) { return (bool)$e['stageNumber']; });
        }

        return [];
    }

    public function getGeneralClassificationResults() {
        $stages = $this->getAllStages();

        if (count($stages) > 0) {
            $result = [];

            $joinParticipantSub = DB::table('race_event_participants as repp')
                ->whereRaw('repp.')
                ->orderBy('id', 'asc')
                ->limit(1);

            foreach($this->categories as $cat) {

                $entries = RaceEventEntry::where('categoryId', $cat->id)
                    ->whereIn('raceEventId', array_map(function($i) { return (int)$i['id']; }, $stages))
                    ->get();

                $res = [];
                foreach ($entries as $entry) {
                    $parts = $entry->participants ?? $entry->team->participants;
                    $ids = [];
                    foreach($parts as $part) {
                        $ids[] = $part->athlete ? $part->athlete->id : $part->id;
                    }
                    sort($ids);
                    $key = implode("|",$ids);
                    if (!isset($res[$key])) {
                        $entry->stageCount = 1;
                        $res[$key] = $entry;
                    } else {
                        $res[$key]->time += $entry->time;
                        $res[$key]->timeRaw = Helper::millisToTime($res[$key]->time);
                        if ($entry->status) {
                            $res[$key]->status = $entry->status;
                        }
                        $res[$key]->stageCount++;
                    }
                }

                usort($res, function($a,$b) {
                    if ($a->time === $b->time) {
                        return 0;
                    } elseif($a->time > $b->time) {
                        return 1;
                    } else {
                        return -1;
                    }
                });

                $final = [];
                $i = 0;
                foreach ($res as $entry) {
                    if ($entry->status || !$entry->time/* || $entry->stageCount < count($stages)*/) {
                        continue;
                    }
                    $i++;

                    $row = [
                        'id' => $entry->id,
                        'rank' => $i,
                        'time' => $entry->time,
                        'timeFormatted' => $entry->timeRaw,
                        'prependTime' => null,
                        'status' => null,
                        'participants' => [],
                    ];

                    foreach(($entry->participants ?? $entry->team->participants) as $p) {
                        $row['participants'][] = [
                            'id' => $p->athlete ? $p->athlete->id : null,
                            'slug' => $p->athlete ? $p->athlete->slug : null,
                            'name' => $p->athlete ? $p->athlete->name : $p->name,
                            'country' => $p->athlete && $p->athlete->country ? $p->athlete->country->code : null,
                        ];
                    }

                    $final[] = $row;
                }

                // dd(array_map(function($a){return $a->timeRaw;}, $res));
                // foreach($entries as $i => $e) {
                //     $e->rank = $i + 1;
                //     $e->timeFormatted = Helper::millisToTime($e->time);
                //     $e->participants = $e->id
                //         ? DB::table('race_event_participants as rep')
                //             ->select(
                //                 'a.id',
                //                 DB::raw('CONCAT(a.firstName, " ", a.lastName) as name'),
                //                 'a.slug',
                //                 'a.image',
                //                 'c.code as country'
                //             )
                //             ->join('athletes as a', 'a.id', 'rep.athleteId')
                //             ->leftJoin('countries as c', 'c.id', 'a.countryId')
                //             ->leftJoin('race_event_teams as ret', 'ret.id', 'rep.raceEventTeamId')
                //             ->join('race_event_entries as ree', function($q) {
                //                 $q->on('ree.raceEventParticipantId', '=', 'rep.id')
                //                     ->orOn('ree.raceEventTeamId', '=', 'ret.id');
                //             })
                //             ->where('ree.id', $e->id)
                //             ->get()
                //         : [];
                // }




                // $entries = DB::select(DB::raw("
                // SELECT
                // MIN(res.id) as id,
                // SUM(res.time) as time,
                // NULL as prependTime,
                // GROUP_CONCAT(res.status) as status
                // FROM (
                //     SELECT
                //     ree.id,
                //     GROUP_CONCAT(a.id) as groupedAthleteIds,
                //     ree.prependTime,
                //     ree.status,
                //     ree.`time` as time
                //     FROM race_event_entries as ree
                //     left join race_event_teams as ret ON ret.id = ree.raceEventTeamId
                //     inner join  race_event_participants as rep ON rep.raceEventTeamId = ret.id OR rep.id=ree.raceEventParticipantId
                //     inner join race_events as re ON re.id=ree.raceEventId
                //     left join (select * from athletes order by id asc) as a ON a.id=rep.athleteId
                //     where ree.categoryId=? && ree.raceEventId IN (".implode(",",array_map(function($i) { return (int)$i['id']; }, $stages)).") && re.stageNumber IS NOT NULL
                //     GROUP BY ree.id
                // ) as res
                // GROUP BY res.groupedAthleteIds
                // ORDER BY time ASC
                // "), [
                //     $cat->id,
                // ]);

                // foreach($entries as $i => $e) {
                //     $e->rank = $i + 1;
                //     $e->timeFormatted = Helper::millisToTime($e->time);
                //     $e->participants = $e->id
                //         ? DB::table('race_event_participants as rep')
                //             ->select(
                //                 'a.id',
                //                 DB::raw('CONCAT(a.firstName, " ", a.lastName) as name'),
                //                 'a.slug',
                //                 'a.image',
                //                 'c.code as country'
                //             )
                //             ->join('athletes as a', 'a.id', 'rep.athleteId')
                //             ->leftJoin('countries as c', 'c.id', 'a.countryId')
                //             ->leftJoin('race_event_teams as ret', 'ret.id', 'rep.raceEventTeamId')
                //             ->join('race_event_entries as ree', function($q) {
                //                 $q->on('ree.raceEventParticipantId', '=', 'rep.id')
                //                     ->orOn('ree.raceEventTeamId', '=', 'ret.id');
                //             })
                //             ->where('ree.id', $e->id)
                //             ->get()
                //         : [];
                // }

                $result[] = [
                    'id' => $cat->id,
                    'name' => $cat->name,
                    'slug' => $cat->slug,
                    'isU23' => (bool)$cat->isU23,
                    'gender' => $cat->gender,
                    'age' => $cat->age,
                    'entries' => $final,
                ];
            }

            return $result;
        }

        return [];
    }
}
