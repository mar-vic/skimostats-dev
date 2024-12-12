<div class="row border border-primary">
    @foreach ($knockouts as $round => $knockout)
        <div class="col" x-data="{ round: '{{ $round }}'}">
            @foreach ($knockout as $heat)
                <div class="row border border-primary" x-data="{ heatIdx: {{ $loop->index }}}">
                    <table class="table border table-striped" style="font-size:0.7rem">
                        <tr>
                            <th>Rk</th>
                            <th>Name</th>
                            <th>NSA</th>
                            <th>Time</th>
                            <th>Diff</th>
                            <th>Actions</th>
                        </tr>
                        @foreach ($heat as $entry)
                            <tr  x-data="{ entryIdx: {{ $loop->index }}}">
                                <td>{{ $entry[0] }}</td>
                                <td>{{ $entry[1] }}</td>
                                <td>{{ $entry[2] }}</td>
                                <td>{{ $entry[3] }}</td>
                                <td>{{ $entry[4] }}</td>
                                <td><button>Edit</button><button x-on:click="$wire.deleteEntry(round, heatIdx, entryIdx)">Del</button></td>
                            </tr>
                        @endforeach
                        <tr>
                            <td><input type="number"></td>
                            <td><input type="text"></td>
                            <td><input type="text"></td>
                            <td><input type="text"></td>
                            <td><input type="text"></td>
                            <td><button x-on:click="$wire.addEntry(round, heatIdx)">Add</button></td>
                        </tr>
                    </table>
                    <button x-on:click="$wire.deleteHeat(round, heatIdx)">Delete heat</button>
                </div>
            @endforeach
            <button x-on:click="$wire.addHeat(round)">Add new heat</button>
        </div>
    @endforeach
</div>
