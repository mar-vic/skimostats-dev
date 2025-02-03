<?php

namespace App\Http\Resources;

use App\Http\Resources\HeatResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KnockoutsRoundResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'order' => $this->order,
            'heats' => HeatResource::collection($this->heats)
        ];
    }
}
