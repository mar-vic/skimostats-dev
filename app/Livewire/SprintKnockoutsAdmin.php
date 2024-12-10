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

    public function addEntry($idx) {
        // dd("Adding entry: ", $idx[0], $idx[1]);
        // dd($this->knockouts[$idx[0]][$idx[1]]);
        $heat = &$this->knockouts[$idx[0]][$idx[1]];
        $heat[] = [1, "John Doe", "USA", "0:00.00", "+0.00"];
    }

    public function deleteEntry($idx) {
        // dd($idx);
        // dd($this->knockouts[$idx[0]][$idx[1]][$idx[2]]);
        unset($this->knockouts[$idx[0]][$idx[1]][$idx[2]]);
        // TODO: deleting entries makes in some cases indexes out of sync
        // dd($this->knockouts[$idx[0]][$idx[1]]);
    }

    public function addHeat($round) {
        $heats = &$this->knockouts[$round];
        $heats[] = [];
    }

    public function render()
    {
        // dd($this->knockouts);
        return view('livewire.sprint-knockouts-admin');
    }
}
