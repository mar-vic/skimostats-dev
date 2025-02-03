<tr x-data="{ editable: false }">
    <form wire:submit="updateHeatEntry">
        <td>
            <span x-show="!editable" x-on:click="editable=true" style="cursor:pointer;">{{ $rank }}</span>
            <input x-show="editable"
                   type="number"
                   min="1"
                   step="1"
                   size="1"
                   wire:model="rank" />
        </td>
        <td>
            <span x-show="!editable" x-on:click="editable=true" style="cursor:pointer;">{{ $athleteName }}</span>
            <input x-show="editable" type="text" wire:model="athleteName" />
        </td>
        <td>
            <span x-show="!editable" x-on:click="editable=true" style="cursor:pointer;">{{ $nationality }}</span>
            <input x-show="editable" type="text" size="3" wire:model="nationality" />
        </td>
        <td>
            <span x-show="!editable" x-on:click="editable=true" style="cursor:pointer;">{{ $timeRaw }}</span>
            <input x-show="editable" type="text" size="5" wire:model="timeRaw" />
        </td>
        <td>
            <button type="button" x-show="!editable" x-on:click="editable=true">Edit</button>
            <button type="submit" x-show="editable" x-on:click="editable=false">Save</button>
            <button type="button" wire:click="delete">Delete</button>
        </td>
    </form>
</tr>
