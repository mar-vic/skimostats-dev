<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RaceEventResultsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    public function toArray($raceEvent)
    {
        $raceEvent->isDetail = true;
        // dd($raceEvent->isDetail);
        return RaceEventCategoryResource::collection($this->categories)->toArray(request());
    }
}
