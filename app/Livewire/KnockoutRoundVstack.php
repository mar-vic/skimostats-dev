<?php
namespace App\Livewire;

use App\KnockoutRound;
use App\Heat;
use Livewire\Component;
use Livewire\Attributes\On;

use Illuminate\Support\Facades\Log;

class KnockoutRoundVstack extends Component
{
    public $knockoutRound;

    public function mount($knockoutRoundId = null)
    {
        $this->knockoutRound = KnockoutRound::find($knockoutRoundId);
    }

    public function render()
    {
        // dd("Rendering heat");
        return view('livewire.knockout-round-vstack');
    }

    public function addNewHeat()
    {
        Heat::create([ "knockoutRoundId"  => $this->knockoutRound->id ]);
    }

    #[On('delete-heat')]
    public function deleteHeat($heatId)
    {
        $heat = Heat::find($heatId);
        if ($heat) {
            $heat->delete();
        }
    }

    public function deleteKnockoutRound()
    {
        $this->dispatch('delete-knockout-round', roundId: $this->knockoutRound->id);
    }
}
