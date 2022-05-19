@extends('layouts.main')

@section('content')
{{ dd($data) }}
<h1>This is a test</h1>

<h2>Data</h2>

<h3>Events</h3>
@foreach ( $data['events'] as $event)
<h4>Event {{ $loop->iteration }}</h4>
<ul>

    <li>
        <b>ID:</b> {{ $event['id'] }}
    </li>

    <li>
        <b>Race ID:</b> {{ $event['raceId'] }}
    </li>

    <li>
        <b>Name:</b> {{ $event['name'] }}
    </li>

    <li>
        <b>Slug:</b> {{ $event['slug'] }}
    </li>

    <li>
        <b>Elevation:</b> {{ $event['elevation'] }}
    </li>

    <li>
        <b>Start Date:</b> {{ $event['startDate'] }}
    </li>

    <li>
        <b>Race Type:</b> {{ $event['raceType'] }}
    </li>

    <li>
        <b>Country Code:</b> {{ $event['countryCode'] }}
    </li>

    <li>
        <b>Country Name:</b> {{ $event['countryName'] }}
    </li>

    <li>
        <b>Year:</b> {{ $event['year'] }}
    </li>
</ul>
@endforeach

<h2>Partners</h2>


@endsection
