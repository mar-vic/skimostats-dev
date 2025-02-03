<?php

namespace App\Http\Resources;

use App\Http\Resources\KnockoutsRoundResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KnockoutsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // $knockouts = [];

        // $roundsResource =
        // dd($roundsResource);

        // foreach ($this->rounds as $round) {
        //     $roundResource = (new KnockoutsRoundResource($round))->toArray($request);
        //     $knockouts[] = $roundResource;
        // }

        // dd($knockouts);

        return [
            'categoryId' => $this->categoryId,
            'rounds' => KnockoutsRoundResource::collection($this->rounds)
        ];
    }
}
