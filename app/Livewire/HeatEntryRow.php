<?php

namespace App\Livewire;

use App\HeatEntry;
use Livewire\Component;

class HeatEntryRow extends Component
{
    public $heatEntryId;
    public $rank;
    public $athleteName;
    public $nationality;
    public $timeRaw;

    public function mount($heatEntryId = null)
    {
        $heatEntry = HeatEntry::find($heatEntryId);
        $this->heatEntryId = $heatEntryId;
        $this->rank = $heatEntry->rank;
        $this->athleteName = $heatEntry->athleteName;
        $this->nationality = $heatEntry->nationality;
        $this->timeRaw = $heatEntry->timeRaw;
    }

    public function render()
    {
        return view('livewire.heat-entry-row');
    }

    public function updateHeatEntry()
    {
        $heatEntry = HeatEntry::find($this->heatEntryId);

        $heatEntry->rank = $this->rank;
        $heatEntry->athleteName = $this->athleteName;
        $heatEntry->nationality = $this->nationality;
        $heatEntry->timeRaw = $this->timeRaw;
        $heatEntry->save();

        $this->dispatch('entry-updated');
    }

    public function delete()
    {
        $this->dispatch('delete-entry', heatEntryId: $this->heatEntryId);
    }
}
