@extends('layouts.main', [
    'selectedPage' => 'races',
    'title' => 'Races',
    'meta_keywords' => 'skimo races, upcoming skimo events, upcoming skimo races, skimo calendar, skimountaineering calendar, ismf world cup calendar, ismf world championships, latest skimo races, '
])
@section('content')

<div id="races-container"></div>

@endsection
@push('scripts')
    <script src="{{ mix('js/races.js')}}"></script>
    <script>
        window.racesVM.setData({!! json_encode($data) !!})
    </script>
@endpush
