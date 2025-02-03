<div>
    <h2>{{ $raceEvent->name }}</h2>
    <div class="row ms-1 me-1">
        @foreach ($raceEvent->categories as $category)
            <div wire:key="catbtn-{{ $category->id }}" class="col-auto">
                <button type="button" wire:click="selectCategory({{ $category->id }})">{{ $category->name }}</button>
            </div>
        @endforeach
    </div>
    @foreach ($raceEvent->categories as $category)
        @if ($selectedCategoryId == $category->id)
            <livewire:knockouts-hstack
                :raceEventId="$raceEvent->id"
                :categoryId="$category->id"
                :key="'khstac-'.$category->id"
            />
        @endif
    @endforeach
    @script
    <script>
     $wire.on('new-category-selected', () => {
         console.log("New category selected");
         /* Livewire.restart(); */
         /* $wire.$refresh(); */
     });
    </script>
    @endscript
</div>
