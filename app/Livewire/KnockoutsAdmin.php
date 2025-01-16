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
    }

    public function render()
    {
        return view('livewire.knockouts-admin',
                    [
                        'knockoutRounds' => $this->rounds
                    ]
        );
    }

    public function addHeatEntry($heatId, $rank, $athleteName, $nationality, $timeRaw) {
        $hentry = HeatEntry::create([
            'heatId' => $heatId,
            'rank' => $rank,
            'athleteName' => $athleteName,
            'nationality' => $nationality,
            'timeRaw' => $timeRaw,
            'time' => null,
        ]);

        $this->dispatch('entryAdded');
    }

    public function updateHeatEntry($entryId, $rank, $athleteName, $nationality, $timeRaw) {
        $hentry = HeatEntry::find($entryId);

        $hentry->athleteName = $athleteName;
        $hentry->rank = $rank;
        $hentry->nationality = $nationality;
        $hentry->timeRaw = $timeRaw;

        $hentry->save();

        $this->dispatch('entryUpdated');
    }

    public function deleteHeatEntry($entryId) {
        // dd(HeatEntry::find($entryId));
        HeatEntry::find($entryId)->delete();
        // $this->render();
        $this->dispatch('entryDeleted');
    }
}
