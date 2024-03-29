<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use App\RaceEventEntry;

class RaceEventCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  App\RaceEvent  $raceEvent
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'isU23' => (bool)$this->isU23,
            'gender' => $this->gender,
            'age' => $this->age,
            'entries' => RaceEventEntryResource::collection(RaceEventEntry::where('raceEventId', $this->pivot->raceEventId)
                ->where('categoryId', $this->id)
                ->orderBy('status', 'asc')
                ->orderBy('rank', 'asc')
                ->get())->toArray($request),
        ];
    }
}
