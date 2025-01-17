<div class="row border border-primary">
    @foreach ($knockoutRounds as $roundIdx => $round)
        <div wire:key="{{$roundIdx}}" class="col">
            <div class="row">
                <h2>{{ $round->name }}</h2>
            </div>
            @foreach ($round->heats as $heatIdx => $heat)
                <div wire:key="{{$roundIdx}}-{{$heatIdx}}" x-data="{heatEntries:{{$heat->entries}}}">
                    <table class="table border table-striped" style="font-size:0.7rem">
                        <span></span>
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
                            <template x-if="!heatEntries">
                                <tr><td colspan="5"><i>Loading...</i></tr>
                            </template>
                            <template x-for="heatEntry in heatEntries">
                                <tr x-data="{ editable: false }">
                                    <td>
                                        <span x-show="!editable" x-text="heatEntry.rank"></span>
                                        <input x-show="editable"
                                               type="number"
                                               min="1"
                                               step="1"
                                               size="1"
                                               x-model="heatEntry.rank" />
                                    </td>
                                    <td>
                                        <span x-show="!editable" x-text="heatEntry.athleteName"></span>
                                        <input x-show="editable" type="text" x-model="heatEntry.athleteName" />
                                    </td>
                                    <td>
                                        <span x-show="!editable" x-text="heatEntry.nationality"></span>
                                        <input x-show="editable" type="text" size="3" x-model="heatEntry.nationality" />
                                    </td>
                                    <td>
                                        <span x-show="!editable" x-text="heatEntry.timeRaw"></span>
                                        <input x-show="editable" type="text" size="5" x-model="heatEntry.timeRaw" />
                                    </td>
                                    <td>
                                        <button x-on:click="editable=!editable">
                                            <span x-show="!editable">Edit</span>
                                            <span x-show="editable" x-on:click="$wire.updateHeatEntry(heatEntry.id, heatEntry.rank, heatEntry.athleteName, heatEntry.nationality, heatEntry.timeRaw)">Save</span>
                                        </button>
                                        <button x-on:click="$wire.deleteHeatEntry(heatEntry.id);">Delete</button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                        <tr x-data="{entry: { rank: 1, athleteName: '', nationality: '', timeRaw: '' }}">
                            <td><input type="number" min="1" step="1" size="1" x-model="entry.rank" /></td>
                            <td><input type="text" placeholder="Athlete name" x-model="entry.athleteName" /></td>
                            <td><input type="text" placeholder="Nationality" size="3" x-model="entry.nationality" /></td>
                            <td><input type="text" placeholder="Time" size="5" x-model="entry.timeRaw" /></td>
                            <td><button wire:click="addHeatEntry({{$heat->id}}, entry.rank, entry.athleteName, entry.nationality, entry.timeRaw)">Add</button></td>
                        </tr>
                    </table>
                </div>
            @endforeach
        </div>
    @endforeach
</div>

@script
<script>
 $wire.on('entryDeleted', () => {
     console.log('An entry was deleted!');
     $wire.$refresh();
 });

 $wire.on('entryUpdated', () => {
     console.log('An entry was updated!');
     $wire.$refresh();
 });

 $wire.on('entryAdded', () => {
     console.log('An entry was created!');
     $wire.$refresh();
     $wire.$refresh();
     $wire.$refresh();
 });
</script>
@endscript
