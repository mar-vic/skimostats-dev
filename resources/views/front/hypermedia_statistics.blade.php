@extends('layouts.main', [
'selectedPage' => 'statistics',
'title' => 'Statistics',
'meta_keywords' => 'skimo statistics, skimo stats, skimo results, skimountaineering statistics, skimo results, pierra menta results, ismf world cup, ismf world cup results, skimo countries, skimo racers, popular athletes, oldest skimo athlete, youngest skimo athlete, tallest skimo athlete, heaviest skimo athlete, lightest skimo athlete'
])
@section('content')
@include('front/partials/statistics-nav')
@endsection

@push('scripts')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endpush
