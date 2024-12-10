<div>
    <h2>Create Race Event</h2>

    Current Race Name: <span x-text="$wire.name"></span>

    <form wire:submit="save">
        <label>
            <span>Name</span>
            <input type="text" wire:model="name">
            @error("name") <em>{{ $message }}</em>@enderror
        </label>
        <label>
            <span>Place</span>
            <input type="text" wire:model="place">
            @error("place") <em>{{ $message }}</em>@enderror
        </label>
        <button type="submit">save</button>
    </form>
</div>
