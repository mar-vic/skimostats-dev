<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

use App\Athlete;
use App\RaceEventTeam;
use App\RaceEventParticipant;
use App\RaceEventEntry;
use App\Category;

use App\Helpers\Helper;

class RaceEventEntryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    public function toArray($raceEvent)
    {
        global $participants;

        $participants = [];

        if ($this->raceEventParticipantId) {
            $part = RaceEventParticipant::find($this->raceEventParticipantId);
            $athlete = $part->athlete;
            if ($athlete) {
                $participants[] = [
                    'id' => $athlete->id,
                    'name' => $athlete->name,
                    'slug' => $athlete->slug,
                    'image' => $athlete->image,
                    'avatar' => $athlete->avatar,
                    'country' => $athlete->country ? $athlete->country->code : null,
                ];
            } else {
                if (trim($part->name)) {
                    $participants[] = [
                        'name' => $part->name,
                    ];
                }
            }
        } else if ($this->raceEventTeamId) {
            $pts = RaceEventParticipant::where('raceEventTeamId', $this->raceEventTeamId)->get();
            foreach($pts as $pt) {
                if ($pt->athlete) {
                    $participants[] = [
                        'id' => $pt->athlete->id,
                        'name' => $pt->athlete->name,
                        'slug' => $pt->athlete->slug,
                        'image' => $pt->athlete->image,
                        'avatar' => $pt->athlete->avatar,
                        'country' => $pt->athlete->country ? $pt->athlete->country->code : null,
                    ];
                } else {
                    $participants[] = [
                        'name' => $pt->name,
                    ];
                }
            }
        }

        $gcTime = null;

        // dd($raceEvent);

        // Calculate (provisional) GC times on staged events
        if (($raceEvent->stageNumber or $raceEvent->isGeneralClassification) && $this->status != 'DNF') {
            $queryBuilder = DB::table('race_events as events')
                          ->select('events.id',
                                   'events.name',
                                   'events.stageNumber',
                                   'events.isGeneralClassification',
                                   'events.parent',
                                   'entries.raceEventParticipantId',
                                   'entries.rank',
                                   'entries.time',
                                   'entries.raceEventTeamId',
                                   'participants.athleteId',
                                   'participants.name',
                                   'categories.name'
                          )
                          ->join('race_event_entries as entries', 'entries.raceEventId', 'events.id')
                          // ->join('categories', 'categories.id', 'events.categoryId')
                          ->where('events.parent', '=', $raceEvent->parent);

            // STAGE
            if ($raceEvent->stageNumber) {
                $queryBuilder = $queryBuilder->where('events.stageNumber', '<', $raceEvent->stageNumber);

            // GENERAL CLASSIFICATION
            } else {
                $queryBuilder = $queryBuilder->whereRaw('events.stageNumber is not null');
            }

            // INDIVIDUALS
            if($this->raceEventParticipantId) {
                $queryBuilder = $queryBuilder->leftJoin('race_event_participants as participants', 'participants.id', 'entries.raceEventParticipantId');

            // TEAMS
            } else {
                $queryBuilder = $queryBuilder->leftJoin('race_event_participants as participants', 'participants.raceEventTeamId', 'entries.raceEventTeamId');
            }

            $participantIds = collect($participants)->map(function($item) { return $item['id']; })->toArray();

            // $gcTime = $queryBuilder->select('events.id',
            //                                 'events.name',
            //                                 'events.stageNumber',
            //                                 'events.isGeneralClassification',
            //                                 'events.parent',
            //                                 'entries.raceEventParticipantId',
            //                                 'entries.rank',
            //                                 'entries.time',
            //                                 'entries.raceEventTeamId',
            //                                 'participants.athleteId',
            //                                 'participants.name',
            //                                 'entries.categoryId'
            // )
            //                        ->whereIn('participants.athleteId', $participantIds)
            //                        ->where('entries.categoryId', $this->categoryId)
            //                        ->get();

            // dd([
            //     'this' => $this,
            //     'stageTime' => Helper::millisToTime($this->time),
            //     'category' => $this->categoryId,
            //     'prevStageEntries' => $gcTime,
            //     'teamSize' => sizeof($participantIds),
            //     'oldGcTime' => $gcTime->sum('time') / sizeof($participantIds),
            //     'oldGcTimeFormatted' => Helper::millisToTime($gcTime->sum('time') / sizeof($participantIds)),
            //     'newGcTime' => $gcTime->sum('time') / sizeof($participantIds) + $this->time,
            //     'newGcTimeFormatted' => Helper::millisToTime($gcTime->sum('time') / sizeof($participantIds) + $this->time)
            // ]);

            $gcTime = $queryBuilder->select('events.id',
                                            'events.name',
                                            'events.stageNumber',
                                            'events.isGeneralClassification',
                                            'events.parent',
                                            'entries.raceEventParticipantId',
                                            'entries.rank',
                                            'entries.time',
                                            'entries.raceEventTeamId',
                                            'participants.athleteId',
                                            'participants.name'
            )
                                   ->whereIn('participants.athleteId', $participantIds)
                                   ->where('entries.categoryId', $this->categoryId)
                                   ->get()->sum('time') / sizeof($participantIds) + (($raceEvent->isGeneralClassification) ? 0 : $this->time);
        }

        // dd([
        //     'id' => $this->id,
        //     'rank' => $this->rank,
        //     'time' => $this->time,
        //     'prependTime' => $this->prependTime,
        //     'timeFormatted' => Helper::millisToTime($this->time),
        //     'gcTime' => $gcTime,
        //     'gcTimeFormatted' => ($gcTime) ? Helper::millisToTime($gcTime) : null,
        //     'participants' => $participants,
        //     'status' => $this->status,
        //     'EventId' => $this->raceEventId
        // ]);

        $categoryName = DB::table('categories')->where('id', $this->categoryId)->select('name')->get()->first()->name;

        return [
            'id' => $this->id,
            'rank' => $this->rank,
            'time' => $this->time,
            'prependTime' => $this->prependTime,
            'timeFormatted' => Helper::millisToTime($this->time),
            'gcTime' => $gcTime,
            'gcTimeFormatted' => ($gcTime) ? Helper::millisToTime($gcTime) : $gcTime,
            'participants' => $participants,
            'status' => $this->status,
            'category' => DB::table('categories')->where('id', $this->categoryId)->select('name')->get()->first()->name
        ];
    }
}
