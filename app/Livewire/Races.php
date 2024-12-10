<?php

namespace App\Livewire;

use App\Race;
use App\RaceEvent;
use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Races')]
class Races extends Component
{
    public function delete($id) {
        dd("Deleting race event with id.: " . $id);
    }

    public function render()
    {
        // dd(RaceEvent::all(["id", "name", "place"])->slice(0,5));
        return view(
            'livewire.races', [
                "races" => RaceEvent::all(["id", "name", "place"]),
            ]
        );
    }
}
