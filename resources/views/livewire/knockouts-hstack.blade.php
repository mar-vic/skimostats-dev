<div>
    <form wire:submit="addKnockoutRound" class="ms-3 me-3 mb-2 mt-2">
        <input type="text" placeholder="Knockout name" wire:model="newKnockoutName" />
        <button type="submit">Add</button>
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
    @script
    <script>
     $wire.on('knockout-round-added', () => {
         /* console.log("Knockout round added"); */
         /* Livewire.restart(); */
         $wire.$refresh();
     });
    </script>
    @endscript
</div>
