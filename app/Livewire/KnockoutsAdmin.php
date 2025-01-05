<?php

namespace App\Livewire;

use App\KnockoutRound;
use App\Knockouts;
use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Knockouts')]
class KnockoutsAdmin extends Component
{
    public function mount() {

    }

    public function render()
    {
        $knockouts = Knockouts::all()->first();
        $rounds = $knockouts->rounds;
        // dd($rounds->first()->knockouts);
        return view('livewire.knockouts-admin',
                    [
                        'knockoutRounds' => $rounds
                    ]
        );
    }
}
