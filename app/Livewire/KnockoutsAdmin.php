<?php

namespace App\Livewire;

use App\RaceEvent;
use App\KnockoutRound;
use App\Knockouts;
use App\Heat;
use App\HeatEntry;
use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Knockouts')]
class KnockoutsAdmin extends Component
{
    public $raceEvent;
    public $knockoutsId = null;
    public $selectedCategoryId = 1;

    public function mount($raceEventId)
    {
        $this->raceEvent = RaceEvent::find($raceEventId);
    }

    public function render()
    {
        return view('livewire.knockouts-admin');
    }

    public function selectCategory($categoryId)
    {
        // dd($categoryId);
        $this->selectedCategoryId = $categoryId;
        // $knockouts = Knockouts::where('raceEventId', $this->raceEvent->id)->where('categoryId', $categoryId)->first();
        // if($knockouts)
        // {
        //     $this->knockoutsId = $knockouts->id;
        // }
        $this->dispatch("new-category-selected");
    }
}
