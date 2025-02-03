<?php

namespace App\Livewire;

use Livewire\Attributes\Validate;
use App\Knockouts;
use App\KnockoutRound;
use Livewire\Component;
use Livewire\Attributes\On;

class KnockoutsHstack extends Component
{
    public $raceEventId;
    public $categoryId;
    public $knockouts;

    #[Validate('required')]
    public $newKnockoutName = "";

    public function mount($raceEventId, $categoryId)
    {
        // dd($this->categoryId);
        $this->raceEventId = $raceEventId;
        $this->categoryId = $categoryId;
        $this->knockouts = Knockouts::where('raceEventId', $raceEventId)->where('categoryId', $categoryId)->first();
        // dd('Mounting knockouts for ' . $categoryId);
    }

    public function render()
    {
        return view('livewire.knockouts-hstack');
    }

    public function addKnockoutRound()
    {
        $this->validate();

        if ($this->knockouts == null)
        {
            $this->knockouts = Knockouts::create([
                'raceEventId' => $this->raceEventId,
                'categoryId' => $this->categoryId,
            ]);
        }

        KnockoutRound::create([
            'knockoutsId' => $this->knockouts->id,
            'name' => $this->newKnockoutName,
            'order' => sizeof($this->knockouts->rounds) + 1
        ]);

        $this->newKnockoutName = '';

        $this->dispatch('knockout-round-added');
    }

    #[On('delete-knockout-round')]
    public function deleteKnockoutRound($roundId)
    {
        // dd('deleting round: ' . $roundId);
        $knockoutRound = KnockoutRound::find($roundId);
        $knockoutRound->delete();
    }
}
