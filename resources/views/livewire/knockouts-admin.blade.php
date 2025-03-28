<div class="p-3">
    <div class="btn-group mb-2">
        @foreach ($raceEvent->categories as $category)
            <div wire:key="catbtn-{{ $category->id }}">
                <button type="button"
                        class="btn btn-secondary border"
                        wire:click="selectCategory({{ $category->id }})"
                >
                    {{ $category->name }}
                </button>
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
    <datalist id="countryCodes">
        @foreach ($countries as $country)
            <option wire:key="country-option-{{ $country->code }}" value="{{ $country->code }}">{{ $country->name }}</option>
        @endforeach
    </datalist>
</div>
