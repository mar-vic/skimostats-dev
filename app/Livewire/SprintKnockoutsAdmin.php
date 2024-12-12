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
        // $quaterFinals = [
        //     [[1, "John Doe", "USA", "0:00.00", "+0.00"], [1, "John Doe", "USA", "0:00.00", "+0.00"], [1, "John Doe", "USA", "0:00.00", "+0.00"]],
        //     [[1, "John Doe", "USA", "0:00.00", "+0.00"], [1, "John Doe", "USA", "0:00.00", "+0.00"], [1, "John Doe", "USA", "0:00.00", "+0.00"]],
        //     [[1, "John Doe", "USA", "0:00.00", "+0.00"], [1, "John Doe", "USA", "0:00.00", "+0.00"], [1, "John Doe", "USA", "0:00.00", "+0.00"]],
        //     [[1, "John Doe", "USA", "0:00.00", "+0.00"], [1, "John Doe", "USA", "0:00.00", "+0.00"], [1, "John Doe", "USA", "0:00.00", "+0.00"]],
        //     [[1, "John Doe", "USA", "0:00.00", "+0.00"], [1, "John Doe", "USA", "0:00.00", "+0.00"], [1, "John Doe", "USA", "0:00.00", "+0.00"]]
        // ];

        // $semiFinals = [
        //     [[1, "John Doe", "USA", "0:00.00", "+0.00"], [1, "John Doe", "USA", "0:00.00", "+0.00"], [1, "John Doe", "USA", "0:00.00", "+0.00"]],
        //     [[1, "John Doe", "USA", "0:00.00", "+0.00"], [1, "John Doe", "USA", "0:00.00", "+0.00"], [1, "John Doe", "USA", "0:00.00", "+0.00"]]
        // ];

        // $finals = [
        //     [[1, "John Doe", "USA", "0:00.00", "+0.00"], [1, "John Doe", "USA", "0:00.00", "+0.00"], [1, "John Doe", "USA", "0:00.00", "+0.00"]],
        //     [[1, "John Doe", "USA", "0:00.00", "+0.00"], [1, "John Doe", "USA", "0:00.00", "+0.00"], [1, "John Doe", "USA", "0:00.00", "+0.00"]]
        // ];

        // $this->knockouts = [
        //     "quaterFinals" => $quaterFinals,
        //     "semiFinals" => $semiFinals,
        //     "finals" => $finals
        // ];
    }

    public function addEntry($round, $heatIdx) {
        // dd("Adding entry: ", $idx[0], $idx[1]);
        // dd($this->knockouts[$idx[0]][$idx[1]]);
        $heat = &$this->knockouts[$round][$heatIdx];
        $heat[] = [1, "John Doe", "USA", "0:00.00", "+0.00"];
        // dd($this->knockouts[$idx[0]][$idx[1]]);
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
