
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <div>
                        <a href='{{ route('admin.races.list') }}'>Races</a> &raquo;
                        <a href='{{ route('admin.races.edit', $race) }}'>{{ $race->name }}</a>
                        &raquo; <a href='{{ route('admin.race-events.list', $race) }}'>Race events</a>
                        @if ($raceEvent)
                            &raquo; {{ $raceEvent->name }} <a class="btn btn-sm btn-primary" href="{{ route('admin.race-events.edit', $raceEvent) }}">edit event</a>
                        @endif

                    </div>
                    @if ($raceEvent)
                        <a href='{{ route('admin.' . $slug . '.add-subevent', [$raceEvent->getKey()]) }}' class='ml-auto btn btn-sm btn-success'>+ Add</a>
                    @else
                        <a href='{{ route('admin.' . $slug . '.add', [$race->getKey()]) }}' class='ml-auto btn btn-sm btn-success'>+ Add</a>
                    @endif
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

                    <table class='table table-striped'>
                        <thead>
                            <tr>
                                <th style='width:50px;'>#</th>
                                <th>
                                    Name
                                </th>
                                <th>Year</th>
                                <th style='width: 150px;text-align:right'>
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($entries as $entry)
                                <tr>
                                    <td>{{ $entry->id }}</td>
                                    <td>
                                        @if ($entry->country)
                                            <img src="{{ asset('images/flags/flags-mini/'.strtolower($entry->country->code).'.png') }}" style="width: 17px;" class="mr-1" alt="">
                                        @endif
                                        <a href='{{ route($entry->isParent() ? 'admin.race-events.list-subevents' : 'admin.race-events.show', [$entry]) }}'>{{ $entry->name }}</a>
                                            @if($entry->isParent())
                                                (has subevents)
                                            @else
                                                ({{$entry->raceType->name}})
                                            @endif

                                            @if($entry->stageNumber)
                                                <div class='badge badge-warning'>Stage {{$entry->stageNumber}}</div>
                                            @endif

                                            @if($entry->isGeneralClassification())
                                                <div class='badge badge-danger'>GC</div>
                                            @endif

                                            @if($entry->hasStages())
                                                <div class='badge badge-secondary'>stages</div>
                                            @endif

                                            @if($entry->isVisible())
                                                <div class='badge badge-success'>visible</div>
                                            @else
                                                <div class='badge badge-dark'>hidden</div>
                                            @endif
                                    </td>
                                    <td>{{ $entry->year }}</td>
                                    <td style='text-align: right;'>
                                        @if ($raceEvent)
                                            <a href='{{ route('admin.' . $slug . '.edit-subevent', [$raceEvent, $entry]) }}' class='btn btn-sm btn-primary'>Edit</a>
                                        @else
                                            <a href='{{ route('admin.' . $slug . '.edit', $entry) }}' class='btn btn-sm btn-primary'>Edit</a>
                                        @endif
                                        <a href='{{ route('admin.' . $slug . '.delete', $entry) }}'
                                            class='btn btn-sm btn-danger'
                                            onclick="return confirm('Really delete {{ $entry->name }}?')">Delete</a>
                                    </td>
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
