<?php

namespace App\Livewire;

use App\Heat;
use App\HeatEntry;
use Livewire\Component;

use Illuminate\Support\Facades\Log;

class HeatTable extends Component
{
    public $heat;
    public $newRank = null;
    public $newAthleteName = '';
    public $newNationality = '';
    public $newTimeRaw = '';

    public function mount($heatId = null)
    {
        $this->heat = Heat::find($heatId);
        $this->dispatch("heat-table-mounted");
    }

    #[On('ranking-updated'), On('delete-entry')]
    public function render()
    {
        // dd($this->heat->entries);
        return view('livewire.heat-table');
    }

    #[On('delete-entry')]
    public function deleteHeatEntry($heatEntryId)
    {
        $heatEntry = HeatEntry::find($heatEntryId);
        if($heatEntry)
        {
            $heatEntry->delete();
        }
    }

    public function addNewEntry()
    {
        HeatEntry::create([
            'heatId' => $this->heat->id,
            'rank' => $this->newRank,
            'athleteName' => $this->newAthleteName,
            'nationality' => $this->newNationality,
            'timeRaw' => $this->newTimeRaw,
            'time' => null,
        ]);

        $this->newRank = null;
        $this->newAthleteName = '';
        $this->newNationality = '';
        $this->newTimeRaw = '';

        $this->dispatch("heat-entry-added");
    }

    public function deleteHeat()
    {
        // Log::debug('Dispatching delete-heat');
        $this->dispatch('delete-heat', heatId: $this->heat->id);
    }
}
