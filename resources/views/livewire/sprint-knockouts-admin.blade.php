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
                                <tr x-data="{ entryIdx: {{ $loop->index }}, editable: false, rnk: {{ $entry[0] }}, name: '{{ $entry[1] }}', nsa: '{{ $entry[2] }}', time: '{{ $entry[3] }}', diff: '{{ $entry[4] }}'}">
                                    <td><span x-show="!editable">{{ $entry[0] }}</span><input x-show="editable" type="number" x-model="rnk"></td>
                                    <td><span x-show="!editable">{{ $entry[1] }}</span><input x-show="editable" type="text" x-model="name"></td>
                                    <td><span x-show="!editable">{{ $entry[2] }}</span><input x-show="editable" type="text" x-model="nsa"></td>
                                    <td><span x-show="!editable">{{ $entry[3] }}</span><input x-show="editable" type="text" x-model="time"></td>
                                    <td><span x-show="!editable">{{ $entry[4] }}</span><input x-show="editable" type="text" x-model="diff"></td>
                                    <td>
                                        <button x-show="!editable" x-on:click="editable=true">Edit</button>
                                        <button x-show="editable" x-on:click="$wire.saveEntry(round, heatIdx, entryIdx, [rnk, name, nsa, time, diff]);editable=false">Save</button>
                                        <button x-on:click="$wire.deleteEntry(round, heatIdx, entryIdx)">Del</button>
                                    </td>
                                </tr>
                            </form>
                        @endforeach
                        <tr x-data="{ rnk: 0, name: '', nsa: '', time: '', diff: ''}">
                            <td><input type="number" x-model="rnk"></td>
                            <td><input type="text" x-model="name"></td>
                            <td><input type="text" x-model="nsa"></td>
                            <td><input type="time" step="0.001" x-model="time"></td>
                            <td><input type="text" x-model="diff"></td>
                            <td>
                                <button x-on:click="$wire.addEntry(round, heatIdx, [rnk, name, nsa, time, diff])">
                                    Add
                                </button>
                            </td>
                        </tr>
                    </table>
                    <button x-on:click="$wire.deleteHeat(round, heatIdx)">Delete heat</button>
                </div>
            @endforeach
            <button x-on:click="$wire.addHeat(round)">Add new heat</button>
        </div>
    @endforeach
</div>
