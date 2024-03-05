@extends('layouts.main', [
    'selectedPage' => 'races',
    'title' => 'Races',
    'meta_keywords' => 'skimo races, upcoming skimo events, upcoming skimo races, skimo calendar, skimountaineering calendar, ismf world cup calendar, ismf world championships, latest skimo races, '
])
@section('content')

@if ($partner != null)
<div class="container pt-4 px-5" style="text-align: center;">
    <a class="m-auto" href="{{ $partner->url }}" title="{{ $partner->name }}">
        <img src="{{ asset('uploads/'.$partner->image) }}" style="min-width: 100%; max-width: 100%;">
    </a>
</div>
@endif

<div id="races-container"></div>

@endsection
@push('scripts')
<script src="{{ mix('js/races.js')}}"></script>
<script>
  window.racesVM.setData({!! json_encode($data) !!})
</script>
<script defer>
    // Enabling Bootstrap tooltips
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
</script>
@endpush
