<?php

namespace App\Http\Resources;

use App\Http\Resources\HeatEntryResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HeatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $sortedHeatEntries = $this->entries->sortBy(function ($heatEntry) {
            return $heatEntry->rank;
        });

        return [
            HeatEntryResource::collection($sortedHeatEntries)
        ];
    }
}
