<?php

namespace App\Http\Resources;

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
            'categories' => RaceEventCategoryResource::collection($this->categories)->toArray($raceEvent),
            'resultCount' => RaceEventEntry::where('raceEventId', $this->id)->count(),
        ];
    }
}
