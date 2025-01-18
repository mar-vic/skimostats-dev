<table class="table border table-striped mt-4 w-50" style="font-size:0.7rem">
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
        @foreach ($heat->entries as $entry)
            <livewire:heat-entry-row :heatEntryId="$entry->id" />
        @endforeach
    </tbody>
</table>
