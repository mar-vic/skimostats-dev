<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Create Race Event')]
class SprintKnockoutsAdmin extends Component
{
    public $knockouts = [
        "quaterFinals" => [],
        "semiFinals" => [],
        "finals" => []
    ];

    public function mount() {
    }

    public function addEntry($round, $heatIdx, $vals) {
        // dd("Adding entry: ", $idx[0], $idx[1]);
        // dd($this->knockouts[$idx[0]][$idx[1]]);
        $heat = &$this->knockouts[$round][$heatIdx];
        $heat[] = $vals;
    }

    public function saveEntry($round, $heatIdx, $entryIdx , $vals) {
        $this->knockouts[$round][$heatIdx][$entryIdx] = $vals;
    }

    public function deleteEntry($round, $heatIdx, $entryIdx)
    {
        unset($this->knockouts[$round][$heatIdx][$entryIdx]);
        // Restting array indexing
        $this->knockouts[$round][$heatIdx] = array_values($this->knockouts[$round][$heatIdx]);
    }

    public function addHeat($round) {
        $this->knockouts[$round][] = [];
    }

    public function deleteHeat($round, $heatIdx) {
        unset($this->knockouts[$round][$heatIdx]);
        // Resetting array indexing
        $this->knockouts[$round] = array_values($this->knockouts[$round]);
    }

    public function render()
    {
        // dd($this->knockouts);
        return view('livewire.sprint-knockouts-admin');
    }
}
