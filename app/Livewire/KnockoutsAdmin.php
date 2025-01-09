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
    public $rounds;

    public function mount() {
        $this->knockouts = Knockouts::all()->first();
        $this->rounds = $this->knockouts->rounds;
        // dd($this->rounds);
    }

    public function render()
    {
        return view('livewire.knockouts-admin',
                    [
                        'knockoutRounds' => $this->rounds
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

    public function updateHeatEntry($entryId, $entryData) {
        $hentry = HeatEntry::find($entryId);

        $hentry->athleteName = $entryData['athleteName'];
        $hentry->rank = $entryData['rank'];
        $hentry->nationality = $entryData['nationality'];
        $hentry->timeRaw = $entryData['timeRaw'];

        dd($hentry, $entryData);

        $hentry->save();
    }

    public function deleteHeatEntry($entryId) {
        // dd(HeatEntry::find($entryId));
        HeatEntry::find($entryId)->delete();
    }
}
