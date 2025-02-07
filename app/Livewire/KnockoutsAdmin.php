<?php

namespace App\Livewire;

use App\RaceEvent;
use App\KnockoutRound;
use App\Knockouts;
use App\Heat;
use App\HeatEntry;
use App\Country;
use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Knockouts')]
class KnockoutsAdmin extends Component
{
    public $raceEvent;
    public $knockoutsId = null;
    public $selectedCategoryId = 1;
    public $countries;

    public function mount($raceEventId)
    {
        $this->raceEvent = RaceEvent::find($raceEventId);
        $this->countries = Country::all();
    }

    public function render()
    {
        return view('livewire.knockouts-admin');
    }

    public function selectCategory($categoryId)
    {
        $this->selectedCategoryId = $categoryId;
    }
}
