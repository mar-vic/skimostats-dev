<?php

namespace App\Http\Resources;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Resources\Json\JsonResource;

use App\RaceEventEntry;
use App\RaceEvent;

class RaceEventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  App\RaceEvent  $raceEvent
     * @return array
     */
    public function toArray($raceEvent)
    {
        $relatedEvents = [];
        if($this->raceId === 13) {
            $relatedEvents = RaceEvent::whereNotNull('type')->where('is_visible', true)->where('raceId', $this->raceId)->orderBy('year', 'asc')->get();
        }

        $stageSlugs = null;
        $generalClassificationSlug = null;

        if ($this->stageNumber or $this->isGeneralClassification) {
            $queryBuilder = DB::table('race_events as events')
                          ->select('events.id',
                                   'events.name',
                                   'events.stageNumber',
                                   'events.isGeneralClassification',
                                   'events.parent',
                                   'events.slug'
                          )
                          ->where('events.parent', '=', $raceEvent->parent)
                          ->orderBy('events.stageNumber');
            $events = $queryBuilder->get()->map(function ($event) { return $event->slug; });
            $stageSlugs = $events->slice(1);
            $generalClassificationSlug = $events->first();
        }

        // dd($generalClassificationSlug);

        return [
            'id' => $this->id,
            'raceId' => $this->raceId,
            'relatedEvents' => $relatedEvents,
            'name' => $this->name,
            'slug' => $this->slug,
            'elevation' => $this->elevation,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'raceType' => $this->raceType,
            'countryCode' => $this->country ? $this->country->code : null,
            'countryName' => $this->country ? $this->country->name : null,
            'year' => $this->year,
            'categories' => RaceEventCategoryResource::collection($this->categories)->toArray(request()),
            'resultCount' => RaceEventEntry::where('raceEventId', $this->id)->count(),
            'stageSlugs' => $stageSlugs,
            'gcSlug' => $generalClassificationSlug,
            'place' => $this->place
        ];
    }
}
