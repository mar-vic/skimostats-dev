<div class="border border-2 rounded p-2 mb-3" style="font-size:0.7rem">
    <div class="row">
        <div class="col d-flex justify-content-end">
            <button type="button" class="mb-1" wire:click="deleteHeat">X</button>
        </div>
    </div>
    <table class="table border border-1 table-striped">
        <thead>
            <tr>
                <th>Rk</th>
                <th>Name</th>
                <th>NSA</th>
                <th>Time</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($heat->entries->sortBy('rank') as $entry)
                <livewire:heat-entry-row :heatEntryId="$entry->id" :key="$entry->id" />
            @endforeach
        </tbody>
    </table>
    <form wire:submit="addNewEntry" class="border p-2 mt-2" style="font-size:0.7rem">
        <div class="row border-1 pt-1 pb-1">
            <div class="col-1">
                <input type="number"
                       min="1"
                       step="1"
                       size="1"
                       wire:model="newRank" />
            </div>
            <div class="col-3">
                <input type="text" placeholder="Athlete's name" wire:model="newAthleteName" />
            </div>
            <div class="col-3">
                <input type="text" placeholder="Nationality" wire:model="newNationality" />
            </div>
            <div class="col-3">
                <input type="text" placeholder="Time" wire:model="newTimeRaw" />
            </div>
            <div class="col-auto">
                <button type="submit" class="">Add</button>
            </div>
        </div>
    </form>
    @script
    <script>
     $wire.on('heat-table-mounted', () => {
         console.log("Heat table mounted");
         /* Livewire.restart(); */
         $wire.$refresh();
     });
     $wire.on('entry-updated', () => {
         console.log("Heat entry updated");
         /* Livewire.restart(); */
         $wire.$refresh();
     });
     $wire.on('heat-entry-added', () => {
         console.log("Heat entry added");
         /* Livewire.restart(); */
         $wire.$refresh();
     });
    </script>
    @endscript
</div>
