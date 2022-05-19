<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Athlete;
use App\RaceEventTeam;
use App\RaceEventParticipant;

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

        return [
            'id' => $this->id,
            'rank' => $this->rank,
            'time' => $this->time,
            'prependTime' => $this->prependTime,
            'timeFormatted' => Helper::millisToTime($this->time),
            'participants' => $participants,
            'status' => $this->status
        ];
    }
}
