<div>
    <h2>Races</h2>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Place</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($races as $race)
                <tr wire:key="{{ $race["id"] }}">
                    <td>{{ $race["name"] }}</td>
                    <td>{{ $race["place"] }}</td>
                    <td>
                        <button
                            type="button"
                            wire:click="delete({{ $race['id'] }})"
                            wire:confirm="Are you sure you want to delete the race event?"
                        >
                            Delete
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
