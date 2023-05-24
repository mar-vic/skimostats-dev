@extends('layouts.main', [
'selectedPage' => 'statistics',
'title' => 'Statistics',
'meta_keywords' => 'skimo statistics, skimo stats, skimo results, skimountaineering statistics, skimo results, pierra menta results, ismf world cup, ismf world cup results, skimo countries, skimo racers, popular athletes, oldest skimo athlete, youngest skimo athlete, tallest skimo athlete, heaviest skimo athlete, lightest skimo athlete'
])
@section('content')
@include('front/partials/statistics-nav')
@endsection

@push('scripts')
<script src="{{mix('js/statistics.js')}}"></script>
<script src="https://unpkg.com/htmx.org@1.9.2" integrity="sha384-L6OqL9pRWyyFU3+/bjdSri+iIphTN/bvYyM37tICVyOJkWZLpP2vGn6VUEXgzg6h" crossorigin="anonymous"></script>
@endpush
