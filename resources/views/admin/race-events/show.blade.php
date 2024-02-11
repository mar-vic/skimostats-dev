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
    &raquo;
<a href='{{ route('admin.' . $slug . '.list', $race) }}'>Race events</a>
       @if ($entry->hasParent())
       &raquo; <a href='{{ route('admin.race-events.list-subevents', $entry->eventParent) }}'>{{ $entry->eventParent->name }}</a>
           @endif
           &raquo;
       {{ $entry->name }} ({{$entry->raceType->name}}) <a class="btn btn-sm btn-primary" href="{{ route('admin.race-events.edit', $entry) }}">edit event</a>
                                                               </div>
                                                               </div>

                                                               <div class="card-body">
                                                               <h2>{{$entry->name}} ({{$entry->raceType->name}}) - results</h2>


                                                                   @if($entry->hasStages())
                                                                       <div class="card mb-4">
                                                               <div class="card-header">
                                                               Choose stage
                                                               </div>
                                                               <div class="card-body">
                                                               @foreach($stages as $key=>$stage)
                                                               <div onclick="location.href='{{route('admin.race-events.showStage', [$entry, $stage->stage])}}'" class="btn btn-outline-dark {{ $selectedStage && $selectedStage->stage == $stage->stage ? ' active' : ''  }}">
                                                               Stage {{ $stage->stage }}
@if($key == count($stages) - 1)
    <a href="{{route('admin.race-events.deleteStage', $stage)}}" onclick="event.stopPropagation();return confirm('Really delete stage {{$stage->stage}}?')" class="d-inline-block px-1 py-0 text-danger ml-2">&times;</a>
        @endif
        </div>
        @endforeach
        <a href="{{ route('admin.race-events.newStage', [$entry]) }}" class="btn btn-success">Add Stage</a>
         </div>
         </div>
         @endif

         @if(!$entry->hasStages() || ($entry->hasStages() && $selectedStage))

         <div class='mb-3 d-flex align-items center justify-content-between'>
         <form method='POST' enctype="multipart/form-data" action="{{route('admin.race-events.import', $entry)}}">
         <input type='file' name='import' />
         CSV Delimiter: <input type='text' name='delimiter' value='{{ old('delimiter', ';') }}' style="width:50px" />
                                                                                                     @csrf
                                                                                                     <input type='hidden' name='raceCategoryId' value='{{ $selectedRaceCategory->id }}' />
                                                                                                     @if($selectedStage)
                                                                                                     <input type="hidden" name="raceEventStageId" value="{{ $selectedStage->id }}" />
                                                                                                     @endif
                                                                                                     <button class='btn btn-primary' type='submit'>import</button>
                                                                                                     &nbsp;
<a href="{{ route('admin.race-events.export-entries.csv', [$entry->id, $selectedRaceCategory->id]) }}" class="btn btn-dark">Export as CSV</a>
       </form>

        @if ($entry->isGeneralClassification == 1)
            <a class="btn btn-warning tw-border-2 tw-border-red-500" href="{{route('admin.race-events.generateGCResults', $entry)}}" onclick="return confirm('Calculate GC results? (this will modify the database)');">Generate GC results</a>
        @endif

       <a class='btn btn-danger' href='{{ route('admin.race-events.deleteAllResults', [$entry, $selectedRaceCategory, $selectedStage ? $selectedStage->id : 0]) }}' onclick="return confirm('Really delete all entries?');">Delete all entries</a>
       </div>

       <div class="btn-group mb-3" role="group" aria-label="Basic example">
       @foreach($entry->categories as $raceCategory)
       <a href="?raceCategory={{$raceCategory->id}}" class="btn btn-secondary {{$raceCategory->id == $selectedRaceCategory->id ? 'active':''}}">{{$raceCategory->name}}</a>
           @endforeach
           </div>

           @include('layouts.success')
           @include('layouts.errors')

           <form method='POST' action='{{ $entry->isTeamRace() ? route('admin.race-event-entry.add-team') : route('admin.race-events.add-entry', [$entry, $selectedRaceCategory]) }}'>
       @csrf
       <input type="hidden" name="raceEventId" value="{{ $entry->id }}" />
       <input type="hidden" name="categoryId" value="{{ $selectedRaceCategory->id }}" />
       <table class="table">
       <tr>
       <td style="padding:0;">
       @if($selectedStage)
       <input type='hidden' name='raceEventStageId' value="{{ $selectedStage->id }}" />
       @endif
       <input class="form-control" type="text" value="{{ old('rank') }}" name="rank" placeholder="Rank" style="padding-left: 0; padding-right: 0;text-align:center;" />
       </td>
       <td
       @if($entry->isTeamRace())
       colspan="2"
       style="width:70%;padding:0;"
       @else
       style="padding:0;"
       @endif
       >
       @if($entry->isTeamRace())
       <input class='form-control' value="{{ old('athleteIds') }}" type="text" name="athleteIds" placeholder="Athlete IDs/Athlete names (Lastname Firstname, ...)/Mix" />
       @else
       <div class="d-flex">
       <input class='form-control' type="number" value="{{ old('entityId') }}" name="entityId" placeholder="ID" style="width:100px;" />
       <input class='form-control' type="text" value="{{ old('entityName') }}" name="entityName" placeholder="{{ $entry->isTeamRace() ? 'Athlete Names separated by comma' : 'Athlete name' }} (you can leave empty if ID is filled)" />
       </div>
       @endif
       </td>
       @if(!$entry->isTeamRace())
       <td style="padding:0;">
       <select name='gender' class="form-control">
       <option value="">-- Select gender --</option>
       <option value="male" @if(old('gender') == 'male') selected="selected" @endif>male</option>
       <option value="female" @if(old('gender') == 'female') selected="selected" @endif>female</option>
       <option value="it" @if(old('gender') == 'it') selected="selected" @endif>other</option>
       </select>
       </td>
       @endif
       <td style="padding:0;">
       <div class="d-flex">
       <input type='text' style="min-width:0;" value="{{ old('time') }}" class='form-control' name='time' placeholder="Time" />
       <input type="submit" value="+ Add" class="btn btn-success" />
       </div>
       </td>
       </tr>
       </table>
       </form>
       <table class="table table-striped">
       <thead>
       <tr>
       <th style="width:50px">#</th>
       <th
       @if($entry->isTeamRace())
       colspan="2"
       @endif
       >Name</th>
       @if(!$entry->isTeamRace())
       <th style="width:15%">Gender</th>
       @endif
       <th style="width:18%">Time</th>
       </tr>
       </thead>
       <tbody>



       @foreach($raceEventEntries as $raceEntry)
       <tr style='{{$raceEntry->status ? "background:#fee;" : ''}}'>
       <td>
       {{ $raceEntry->status ?? $raceEntry->rank }}
</td>
    <td>
    @if($raceEntry->participant)
        @if($raceEntry->participant->athlete)
            @if($raceEntry->participant->athlete->country)
                <img src='/images/flags/mini/{{ $raceEntry->participant->athlete->country->code }}.png' style="width:20px;" />
       @endif
       <a id="participant-{{$raceEntry->participant->id}}" href='{{ route('admin.athletes.edit', $raceEntry->participant->athlete->id) }}' title="Participant saved name: {{ $raceEntry->participant->name }}">{{ $raceEntry->participant->athlete->name }}</a>

           / {{ $raceEntry->participant->athlete->id}}

<button class="btn btn-sm edit-entry">edit</button>

       <div class="entry-edit d-none pt-4">
       <form method="post" action="{{ route('admin.race-event-entry.update', $raceEntry) }}">
       @csrf
       <table>
       <tr>
       <td>#</td>
       <td><input type="text" class="form-control" name="rank" value="{{ $raceEntry->status ?? $raceEntry->rank }}" /></td>
       </tr>
       <tr>
       <td>Athlete ID:</td>
       <td><input type="number" name="athleteId" value="{{$raceEntry->participant->athlete->id}}" class="form-control" /></td>
       </tr>
       <tr>
       <td>Gender:</td>
       <td>
       <select name='gender' class="form-control">
       <option value="male" {!! $raceEntry->participant->gender === 'male' ? " selected='selected'" : '' !!}>male</option>
           <option value="female" {!! $raceEntry->participant->gender === 'female' ? " selected='selected'" : '' !!}>female</option>
               <option value="it" {!! $raceEntry->participant->gender === 'it' ? " selected='selected'" : '' !!}>other</option>
                   </select>
                   </td>
                   </tr>
                   <tr>
                   <td>Time</td>
                   <td><input type="text" name="time" class="form-control" value="{{$raceEntry->timeRaw}}" /></td>
       </tr>
       <tr>
       <td>F/SQ/...</td>
       <td><input type="text" name="prependTime" class="form-control" value="{{$raceEntry->prependTime}}" /></td>
       </tr>
       <tr>
       <td colspan="2">
       <button type="submit" class="btn btn-primary btn-sm">Edit</button>
       </td>
       </tr>
       </table>
       </form>
       </div>
       @else
               {{ $raceEntry->participant->name }}
@endif
    @elseif($raceEntry->team)
{{ $raceEntry->team->name }} -
    @foreach($raceEntry->team->participants as $key => $participant)
    @if ($key != 0),@endif
                                 @if ($participant->athlete)
                                 <a id="participant-{{$participant->id}}" href='{{ route('admin.athletes.edit', $participant->athlete) }}'>{{ $participant->athlete->name }}</a> / {{ $participant->athlete->id }}
@else
{{ $participant->name }}
@endif
    @endforeach

    <button class="btn btn-sm edit-entry">edit</button>

    <div class="entry-edit d-none pt-4">
    <form method="post" action="{{ route('admin.race-event-entry.update-team', $raceEntry) }}">
    @csrf
    <input type="hidden" name="teamId" value="{{ $raceEntry->team->id }}" />
    <table>
    <tr>
    <td>#</td>
    <td><input type="text" class="form-control" name="rank" value="{{ $raceEntry->status ?? $raceEntry->rank }}" /></td>
    </tr>
    <tr>
    <td>Athlete IDs:</td>
    <td><input type="text" name="athleteIds" value="{{implode(',', array_map(function($item) { return $item['athleteId']; }, $raceEntry->team->participants->toArray()))}}" class="form-control" /></td>
    </tr>
    <tr>
    <td>Time</td>
    <td><input type="text" name="time" class="form-control" value="{{$raceEntry->timeRaw}}" /></td>
    </tr>
    <tr>
    <td>F/SQ/...</td>
    <td><input type="text" name="prependTime" class="form-control" value="{{$raceEntry->prependTime}}" /></td>
    </tr>
    <tr>
    <td colspan="2">
    <button type="submit" class="btn btn-primary btn-sm">Edit</button>
    </td>
    </tr>
    </table>
    </form>
    </div>
    @endif

    </td>
    <td>
{{ $raceEntry->participant ? $raceEntry->participant->gender : '' }}
    </td>
        <td>
    {{ $raceEntry->prependTime ? $raceEntry->prependTime.' ' : '' }}
@if($raceEntry->status)
    -
    @else
{{ Helper::millisToTime($raceEntry->time) }}
@if($firstRaceEventEntry->time - $raceEntry->time > 0)
    <span class="text-success">
    -{{Helper::millisToTime(abs(($firstRaceEventEntry->time - $raceEntry->time)), false)}}
    </span>
        @elseif($firstRaceEventEntry->time - $raceEntry->time == 0)
            <span class="text-warning">
    +0s
    </span>
    @else
    <span class="text-danger">
    +{{Helper::millisToTime(abs(($firstRaceEventEntry->time - $raceEntry->time)), false)}}
</span>
    @endif
    @endif
    <a href='{{ route('admin.race-event-entry.delete', $raceEntry) }}' class="btn btn-sm btn-danger float-right">X</a>
    </td>
    </tr>
    @endforeach
    </tbody>
    </table>
    @endif
    </div>
    </div>
    </div>
    </div>
    </div>
    <script>
    $(function(){
        $('.edit-entry').click(function(){
            $(this).next().toggleClass('d-none')
                   })
                        })
    </script>
    @endsection
