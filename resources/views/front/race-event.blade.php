@extends('layouts.main', [
    'selectedPage' => 'races',
    'title' => $data['event']['name'] . ($data['event']['raceType'] ? ' (' . $data['event']['raceType']['name'] . ')' : ''),
    'meta_description' => 'Skimo ' . strtolower($data['event']['raceType'] ? $data['event']['raceType']['name'] : '') . ' race - ' . $data['event']['name'],
    'meta_keywords' => 'skimo race, skimo results, skimountaineering, ismf world cup results, ismf results, pierra menta results, race results, ski mountaineering, skimo ' . ($data['event']['raceType'] ? $data['event']['raceType']['name'] : '')
        . ', ' . $data['event']['name']
        . ', skimo event, ski mountaineering ' . ($data['event']['raceType'] ? $data['event']['raceType']['name'] : '')
        . ', ' . ($data['event']['raceType'] ?  $data['event']['raceType']['name']  : '')
        . ', ' . implode(", ", explode(" ", $data['event']['name']))
])
@section('content')

<div id="race-event-container"></div>

@endsection
@push('scripts')
    <script src="{{ mix('js/race-event.js')}}"></script>
    <script>
        window.raceEventVM.setData({!! json_encode($data) !!})
    </script>
@endpush
