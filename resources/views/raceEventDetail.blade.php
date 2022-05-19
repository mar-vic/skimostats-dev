@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <div>
                        <a href='/'>Races</a> &raquo; {{ $raceEvent->name }}
                    </div>
                </div>

                <div class="card-body">

                    <div class='alert alert-info'>
                        Location: {{$raceEvent->place}}<br>
                        Start time: {{date("d. m. Y H:i", strtotime($raceEvent->startDate))}}
                    </div>

                    @if (count($raceCategories) > 1)
                    <div class='mb-3'>
                        @foreach ($raceCategories as $item)
                            <a href='{{ route('raceEventDetail.category', [$raceEvent, $item]) }}' class='btn btn-outline-secondary @if($category->id == $item->id) active @endif'>{{ $item->name }}</a>
                        @endforeach

                        @auth
                            <a href='{{ route('admin.race-events.show', $raceEvent)}}?raceCategory={{$category->id}}' class='btn btn-success float-right'>Edit</a>
                        @endauth
                    </div>
                    @endif


                    <div class='mb-2'>Event participants: {{count($entries)}}</div>

                    <table class='table table-striped'>
                        <thead>
                            <tr>
                                <th style='width:45px;'>#</th>
                                <th>Name</th>
                                @if(!$category->isTeam)
                                    <th>Gender</th>
                                @endif
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($entries as $entry)
                            <tr>
                                <td>{{$entry->rank ? $entry->rank.'.' : '-'}}</td>
                                @if($entry->participant)
                                <td>
                                    @if($entry->participant->athlete && $entry->participant->athlete->country)
                                        <img style='width: 25px;' src='/images/flags/mini/{{$entry->participant->athlete->country->code}}.png' alt='{{$entry->participant->athlete->country->name}}' />
                                    @endif
                                    {{$entry->participant->name}}</td>
                                <td>{{$entry->participant->gender}}</td>
                                @endif
                                @if($entry->team)
                                <td>
                                    @if($entry->team && $entry->team->country)
                                        <img style='width: 25px;' src='/images/flags/mini/{{$entry->team->country->code}}.png' alt='{{$entry->team->country->name}}' />
                                    @endif
                                    {{ $entry->team->name }}
                                </td>
                                @endif
                                <td>{{$entry->time}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

