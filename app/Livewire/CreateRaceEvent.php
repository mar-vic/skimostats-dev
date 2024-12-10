<?php

namespace App\Livewire;

use App\RaceEvent;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Rule;

#[Title('Create Race Event')]
class CreateRaceEvent extends Component
{
    #[Rule("required")]
    public $name = "";

    #[Rule("required")]
    public $place = "";

    public function save() {
        $this->validate();

        $event = new RaceEvent([
            "name" => $this->name,
            "place" => $this->place,
            "raceId" => 7
        ]);

        $event->save();

        $this->redirect("/races");
    }

    public function render()
    {
        return view('livewire.create-race-event');
    }
}
