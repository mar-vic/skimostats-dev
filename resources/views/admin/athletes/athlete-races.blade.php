
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <div>
                        <a href="{{ route('admin.athletes.list') }}">Athletes</a> &raquo;
                        <a href="{{ route('admin.'.$slug.'.edit', $athlete->id) }}">{{ $athlete->name }}</a> &raquo; Races
                    </div>
                </div>

                <div class="card-body">
                    @include('layouts.success')

                    <form method='GET'>
                        <div class='input-group'>
                            <input type='text' name='filter' value='{{ request('filter') }}' placeholder="Search..." class='form-control' />
                            <div class="input-group-append">
                                <input type='submit' value='Filter' class='btn btn-outline-primary' />
                            </div>
                        </div>
                    </form>

                    <form method="POST" action="{{route('admin.athletes.top-results.update')}}">
                        @csrf
                        <input type="hidden" name="athleteId" value="{{$athlete->id}}">
                        <table class='table table-striped'>
                            <thead>
                                <tr>
                                    <th>
                                        Race event entry ({{ $entries->count() }})
                                    </th>
                                    <th>
                                        Category
                                    </th>
                                    <th>
                                        Place
                                    </th>
                                    <th>
                                        Time
                                    </th>
                                    <th>
                                        Pts
                                    </th>
                                    <th style='width: 100px;text-align:right'>
                                        Top result
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($entries as $entry)
                                    <tr>
                                        <td>
                                        <a href='{{ route('admin.race-events.show', [$entry->raceEventId, 'raceCategory' => $entry->category->id]) }}#participant-{{ $entry->id }}' class="d-flex align-items-center">
                                                @if($entry->raceEvent->country)
                                                    <img src="{{asset('images/flags/flags-mini/'.strtolower($entry->raceEvent->country->code).'.png') }}" class="mr-2" style="width: 18px;" alt="">
                                                @endif
                                                <div>
                                                    {{ $entry->raceEvent->name }} ({{ $entry->raceEvent->raceType->name }})
                                                </div>
                                            </a>
                                            <div style="font-size:10px;">{{$entry->raceEvent->startDate}}</div>
                                        </td>

                                        <td>
                                            {{$entry->category->name}}
                                        </td>

                                        @if($entry->raceEventEntry)
                                            <td>
                                                {{$entry->raceEventEntry->rank}}.
                                            </td>
                                            <td>
                                                {{Helper::millisToTime($entry->raceEventEntry->time)}}
                                            </td>
                                        @elseif($entry->raceEventTeam)
                                        <td>
                                            {{$entry->raceEventTeam->raceEventEntry->rank}}.
                                        </td>
                                        <td>
                                            {{Helper::millisToTime($entry->raceEventTeam->raceEventEntry->time)}}
                                        </td>
                                        @endif

                                        <td>
                                            {{$entry->points}}
                                        </td>

                                        <td style='text-align: right;'>
                                            <input type="number" class="form-control" name="topresults[{{$entry->id}}]" value="{{ $entry->topResult ?? '0' }}" />
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <button type="submit" class="btn btn-primary">Update top results</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
