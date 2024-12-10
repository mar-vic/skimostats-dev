<div>
    <form wire:submit="add">
        <input type="text" wire:model.live.change="todo">
        <button type="submit">Add</button>
    </form>
    <div>
        Current todo: {{ $todo }}
    </div>
    <ul>
        @foreach ($todos as $todo)
            <li>{{ $todo }}</li>
        @endforeach
    </ul>
</div>
