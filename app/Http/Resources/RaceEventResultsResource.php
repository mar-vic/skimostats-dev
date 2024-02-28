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
        return RaceEventCategoryResource::collection($this->categories)->toArray(request());
    }
}
