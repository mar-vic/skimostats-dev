<div class="row border border-primary">
    @foreach ($knockoutRounds as $roundIdx => $round)
        <div class="col">
            <div col="row">
                <h2>{{ $round->name }}</h2>
            </div>
            @foreach ($round->heats as $heatIdx => $heat)
                <div col="row">
                    <table class="table border table-striped" style="font-size:0.7rem">
                        <tr>
                            <th>Rk</th>
                            <th>Name</th>
                            <th>NSA</th>
                            <th>Time</th>
                            <th>Actions</th>
                        </tr>
                        @foreach ($heat->entries as $entryIdx => $entry)
                            <tr wire:key="{{$entryIdx}}" x-data="{ editable: false, entryData: { rank: {{$entry->rank}}, athleteName: '{{$entry->athleteName}}', nationality: '{{$entry->nationality}}', timeRaw: '{{$entry->timeRaw}}' }}">
                                <td>
                                    <span x-show="!editable">{{ $entry->rank }}</span>
                                    <input x-show="editable"
                                           type="number"
                                           min="1"
                                           step="1"
                                           size="1"
                                           x-bind:value="entryData.rank" />
                                </td>
                                <td>
                                    <span x-show="!editable">{{ $entry->athleteName }}</span>
                                    <input x-show="editable" type="text" x-bind:value="entryData.athleteName" />
                                </td>
                                <td>
                                    <span x-show="!editable">{{ $entry->nationality }}</span>
                                    <input x-show="editable" type="text" size="3" x-bind:value="entryData.nationality" />
                                </td>
                                <td>
                                    <span x-show="!editable">{{ $entry->timeRaw }}</span>
                                    <input x-show="editable" type="text" size="5" x-bind:value="entryData.timeRaw" />
                                </td>
                                <td>
                                    <button x-show="!editable" x-on:click="editable=true">Edit</button>
                                    <button x-show="editable" x-on:click="editable=false" wire:click="updateHeatEntry({{$entry->id}},entryData)">Save</button>
                                    <button wire:click="deleteHeatEntry({{$entry->id}})">Delete</button>
                                </td>
                            </tr>
                        @endforeach
                            <tr wire:key="{{$heatIdx}}" x-data="{entry: { rnk: 0, name: '', nsa: '', timeRaw: '' }}">
                                <td><input type="number" min="1" step="1" size="1" x-model="entry.rnk" /></td>
                                <td><input type="text" placeholder="Athlete name" x-model="entry.name" /></td>
                                <td><input type="text" placeholder="Nationality" size="3" x-model="entry.nsa" /></td>
                                <td><input type="text" placeholder="Time" size="5" x-model="entry.timeRaw"  /></td>
                                <td><button wire:click="addHeatEntry({{$heat->id}}, entry)">Add</button></td>
                            </tr>
                    </table>
                </div>
            @endforeach
        </div>
    @endforeach
</div>
