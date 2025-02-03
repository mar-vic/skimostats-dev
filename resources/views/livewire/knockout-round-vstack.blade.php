<div class="col-6">
    <div class="row">
        <div class="col-auto">
            <h2>{{ $knockoutRound->name }}</h2>
        </div>
        <div class="col d-flex justify-content-end">
            <button type="button" wire:click="deleteKnockoutRound">X</button>
        </div>
    </div>
    @foreach ($knockoutRound->heats as $heat)
        <livewire:heat-table :heatId="$heat->id" :key="$heat->id" />
    @endforeach
    <button type="button" class="p-1" wire:click="addNewHeat" style="font-size:0.7rem">Add heat</button>
</div>
