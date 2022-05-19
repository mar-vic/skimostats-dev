@extends('layouts.main')

@section('content')

<div class="container py-5">
    <h1 class="text-uppercase font-weight-bold text-blue mb-5">Search results ({{ count($results) }})</h1>
    @if(strlen($query) > 1)
        <div class="alert alert-info">
            Search query: <b>{{ $query }}</b>
        </div>
    @else
        <div class="alert alert-danger">
            Query has to have at least 2 letters.
        </div>
    @endif
    <div>
        @foreach($results as $result)
            <div class="mb-2">
                <a href="{{ $result['link'] }}">
                    {{ $result['name'] }}
                </a>
            </div>
        @endforeach
    </div>

</div>

@endsection
