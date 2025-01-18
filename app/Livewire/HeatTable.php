<?php

namespace App\Livewire;

use App\Heat;
use App\HeatEntry;
use Livewire\Component;
use Livewire\Attributes\On;

class HeatTable extends Component
{
    public $heat;

    public function mount($heatId = null)
    {
        $this->heat = Heat::find($heatId);
        // dd($this->heat->id);
    }

    public function render()
    {
        return view('livewire.heat-table');
    }

    #[On('delete-entry')]
    public function deleteHeatEntry($heatEntryId)
    {
        HeatEntry::find($heatEntryId)->delete();
    }
}
