<div class="row border border-primary">
    <h1>Knockouts</h1>
    <ul>
        @foreach ($knockoutRounds as $round)
            <h2>{{ $round->name }}</h2>
            @foreach ($round->heats as $heat)
                <ul>
                    @foreach ($heat->entries as $entry)
                        <li>{{ $entry->timeRaw }}</li>
                    @endforeach
                </ul>
            @endforeach
        @endforeach
    </ul>
</div>
