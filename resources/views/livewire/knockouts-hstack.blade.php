<div>
    <form wire:submit="addKnockoutRound" class="ms-3 me-3 mb-2 mt-2">
        <div class="row">
            <div class="col-auto">
                <input class="form-control" type="text" placeholder="Knockout name" wire:model="newKnockoutName" />
            </div>
            <div class="col-auto">
                <button class="btn btn-success" type="submit">Add</button>
            </div>
        </div>
        <div>
            @error('newKnockoutName') <span class="error">{{ $message }}</span> @enderror
        </div>
    </form>
    @if ($knockouts != null)
        <div class="row">
            @foreach ($knockouts->rounds as $knockoutRound)
                <livewire:knockout-round-vstack :knockoutRoundId="$knockoutRound->id" :key="$knockoutRound->id" />
            @endforeach
        </div>
    @endif
</div>
