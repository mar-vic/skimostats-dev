<tr x-data="{ editable: false }">
    <form wire:submit="updateHeatEntry">
        <td>
            <span x-show="!editable">{{ $rank }}</span>
            <input x-show="editable"
                   type="number"
                   min="1"
                   step="1"
                   size="1"
                   wire:model="rank" />
        </td>
        <td>
            <span x-show="!editable">{{ $athleteName }}</span>
            <input x-show="editable" type="text" wire:model="athleteName" />
        </td>
        <td>
            <span x-show="!editable">{{ $nationality }}</span>
            <input x-show="editable" type="text" size="3" wire:model="nationality" />
        </td>
        <td>
            <span x-show="!editable">{{ $timeRaw }}</span>
            <input x-show="editable" type="text" size="5" wire:model="timeRaw" />
        </td>
        <td>
            <button type="button" x-show="!editable" x-on:click="editable=true">Edit</button>
            <button type="submit" x-show="editable" x-on:click="editable=false">Save</button>
            <button x-on:click="$wire.deleteHeatEntry(heatEntry.id);">Delete</button>
        </td>
    </form>
</tr>
