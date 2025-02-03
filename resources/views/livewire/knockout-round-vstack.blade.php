<div class="vstack ms-3 me-3">
    <div class="hstack">
        <h2>{{ $knockoutRound->name }}</h2>
        <button type="button"
                class="ms-2"
                wire:click="deleteKnockoutRound">X</button>
    </div>
    @foreach ($knockoutRound->heats as $heat)
        <livewire:heat-table :heatId="$heat->id" :key="$heat->id" />
    @endforeach
    <button type="button" class="p-1" wire:click="addNewHeat" style="font-size:0.7rem">Add heat</button>
</div>
