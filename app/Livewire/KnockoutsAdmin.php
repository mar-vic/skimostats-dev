<?php

namespace App\Livewire;

use App\KnockoutRound;
use App\Knockouts;
use App\Heat;
use App\HeatEntry;
use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Knockouts')]
class KnockoutsAdmin extends Component
{
    public $knockouts;

    public function mount() {
        $this->knockouts = Knockouts::all();
    }

    public function render()
    {
        $rounds = $this->knockouts->first()->rounds;
        // dd($rounds->first()->knockouts);
        return view('livewire.knockouts-admin',
                    [
                        'knockoutRounds' => $rounds
                    ]
        );
    }

    public function addHeatEntry($heatId, $entry) {
        // dd("Heat ID: ".$heatId);
        // dd($entry, $heatId);
        // dd($entry);
        $hentry = HeatEntry::create([
            'heatId' => $heatId,
            'athleteName' => $entry['name'],
            'nationality' => $entry['nsa'],
            'timeRaw' => $entry['timeRaw'],
            'time' => null,
            'rank' => $entry['rnk']
        ]);
        // dd($hentry);
    }

    public function deleteHeatEntry($entryId) {
        // dd(HeatEntry::find($entryId));
        HeatEntry::find($entryId)->delete();
    }
}
