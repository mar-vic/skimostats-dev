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
                        &raquo; <a href='{{ route('admin.' . $slug . '.list', $race) }}'>Race events</a>
                        @if($parentEvent)
                        &raquo; <a href='{{route('admin.race-events.list-subevents', $parentEvent)}}'>{{$parentEvent->name}}</a>
                        @endif
                        &raquo;
                        Race events
                        &raquo;
                        @if($edit)
                        {{ $entry->name }} (Edit)
                        @else
                        Add
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    <form method='POST' enctype="multipart/form-data" action='{{ route('admin.' . $slug . '.' . ($edit ? 'update' : 'create'), $edit ? $entry : $race) }}'>

                        @include('layouts.errors')
                        @include('layouts.success')

                        @if($parentEvent)
                        <div class="alert alert-info">
                            <b>Parent event:</b> <a href='{{route('admin.race-events.list-subevents', $parentEvent)}}'>{{$parentEvent->name}}</a>

                            <input type='hidden' name='parent' value='{{$parentEvent->id}}' />
                        </div>
                        @endif


                        <table class='table'>
                            <tbody>
                                <tr>
                                    <td style='width: 200px'>Name:</td>
                                    <td>
                                        <input type='text' name='name' value='{{ old('name', $entry->name) }}' class='form-control' />
                                    </td>
                                </tr>
                                <tr>
                                    <td style='width: 200px'>Optional name:</td>
                                    <td>
                                        <input type='text' name='optionalName' value='{{ old('optionalName', $entry->optionalName) }}' class='form-control' />
                                    </td>
                                </tr>
                                @if($parentEvent && $parentEvent->hasStages())
                                <tr>
                                    <td>Stage number:</td>
                                    <td><input type="number" class="form-control" name="stageNumber" value="{{ old('stageNumber', $entry->stageNumber) }}" />
                                        <div class="small">Leave empty if it's general classification</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>
                                        <label for="igc">
                                            <input type="checkbox" name="isGeneralClassification" id="igc" {{ $entry->isGeneralClassification() ? " checked='checked'" : "" }}>

                                            Is General Classification (Final results)
                                        </label>
                                    </td>
                                </tr>
                                @else
                                <tr>
                                    <td></td>
                                    <td>
                                        <label for="igc">
                                            <input type="checkbox" name="hasStages" id="igc" {{ $entry->id && $entry->hasStages() ? " checked='checked'" : "" }}>

                                            This event has stages
                                        </label>
                                    </td>
                                </tr>
                                @endif
                                <tr>
                                    <td>Ranking Category</td>
                                    <td>
                                        <select name="rankingCategoryId" class="form-control">
                                            <option value="">- Select category -</option>
                                            @foreach($rankingCategories as $rankingCategory)
                                            <option value="{{$rankingCategory->id}}" @if($entry->rankingCategoryId == $rankingCategory->id) selected="selected" @endif>{{$rankingCategory->name}}</option>
                                            @endforeach
                                        </select>
                                        @if(!$entry->rankingCategoryId && $entry->computedRankingCategory)
                                        <div class="pt-1">
                                            Fallbacks to category of
                                            @if($entry->rankingCategoryFallbackObject && get_class($entry->rankingCategoryFallbackObject) == 'App\Race')
                                            <a href="{{route('admin.races.edit', $entry->rankingCategoryFallbackObject->id)}}">{{$entry->rankingCategoryFallbackObject->name}}</a>
                                            @else
                                            <a href="{{route('admin.race-events.edit', $entry->rankingCategoryFallbackObject->id)}}">
                                                {{$entry->rankingCategoryFallbackObject->name}}
                                                @if($entry->rankingCategoryFallbackObject->raceType)
                                                - {{$entry->rankingCategoryFallbackObject->raceType->name}}
                                                @endif
                                            </a>
                                            @endif
                                            : <b>{{$entry->computedRankingCategory->name}}</b>

                                        </div>

                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>Start date:</td>
                                    <td>
                                        <input type='text' placeholder="YYYY-MM-DD HH:MM:SS" name='startDate' value='{{ old('startDate', $entry->startDate) }}' class='form-control' />
                                    </td>
                                </tr>
                                <tr>
                                    <td>End date:</td>
                                    <td>
                                        <input type='text' name='endDate' placeholder="YYYY-MM-DD HH:MM:SS" value='{{ old('endDate', $entry->endDate) }}' class='form-control' />
                                    </td>
                                </tr>
                                <tr>
                                    <td>Place:</td>
                                    <td>
                                        <input type='text' name='place' value='{{ old('place', $entry->place) }}' class='form-control' />
                                    </td>
                                </tr>
                                <tr>
                                    <td>Country:</td>
                                    <td><select name='countryId' class='form-control'>
                                            <option value='0'>- select country -</option>
                                            @foreach($countries as $country)
                                            <option value='{{ $country->id }}' @if($country->id == $entry->countryId) selected='selected' @endif>{{ $country->name }}</option>
                                            @endforeach
                                        </select></td>
                                </tr>
                                <tr>
                                    <td>Elevation (in metres):</td>
                                    <td>
                                        <input type='text' name='elevation' value='{{ old('elevation', $entry->elevation) }}' class='form-control' />
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan='2'>
                                        <label>
                                            <input type="checkbox" name='isVisible' value='1' {{ $entry->isVisible() ? " checked='checked'" : "" }} />
                                            Make visible
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Type</td>
                                    <td>
                                        <select name='raceType' class='race-type-select form-control'>
                                            <option value="0">No type - event has subevents</option>
                                            @foreach($raceTypes as $type)
                                            <option value="{{ $type->id }}" {{ $entry->type == $type->id ? " selected='selected'" : "" }}>{{ $type->name}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr class='race-categories-container'>
                                    <td>Race categories:</td>
                                    <td>
                                        <a href="javascript:checkCategories(true);">Check all</a> / <a href="javascript:checkCategories(false);">Uncheck all</a>
                                        @foreach($categories as $category)
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox"
                                                name='category[{{$category->id}}]'
                                                value='1' class="custom-control-input"
                                                id="customCheck{{ $category->id }}"
                                                {{ !$entry->id || $entry->hasCategory($category->id) ? " checked='checked'" : ""}}>
                                            <label class="custom-control-label" for="customCheck{{ $category->id }}">{{ $category->name }}</label>
                                        </div>
                                        @endforeach
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        @csrf
                        <button type='submit' class='btn btn-primary'>Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <div>
                        <a href='{{ route('admin.races.list') }}'>Races</a> &raquo;
                        <a href='{{ route('admin.races.edit', $race) }}'>{{ $race->name }}</a>
                        &raquo; <a href='{{ route('admin.' . $slug . '.list', $race) }}'>Race events</a>
                        @if($parentEvent)
                            &raquo; <a href='{{route('admin.race-events.list-subevents', $parentEvent)}}'>{{$parentEvent->name}}</a>
                        @endif
                        &raquo;
                        Race events
                        &raquo;
                        @if($edit)
                            {{ $entry->name }} (Edit Knockout Rounds)
                        @else
                            Add
                        @endif
                    </div>
                </div>

                <!-- Livewire component for knockouts administration (only relevent for sprints / type 3 race) -->
                @if ($entry->type == 3)
                    <livewire:KnockoutsAdmin :raceEventId="$entry->id" />
                @endif

            </div>
        </div>
    </div>
</div>

<!-- Loading HTMX  -->
<script src="https://unpkg.com/htmx.org@2.0.3" integrity="sha384-0895/pl2MU10Hqc6jd4RvrthNlDiE9U1tWmX7WRESftEDRosgxNsQG/Ze9YMRzHq" crossorigin="anonymous"></script>

<!-- Loading AlpineJS -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<script>
    function checkCategories(check) {
        $('input[name^=category]').prop('checked', check);
    }

    function toggleRaceCategories() {
        var val = $('.race-type-select').val()
        if (Boolean(Number(val))) {
            $('.race-categories-container').show()
        } else {
            $('.race-categories-container').hide()
        }
    }

    $(function() {
        toggleRaceCategories()

        $('.race-type-select').change(function() {
            toggleRaceCategories()
        })
    })
</script>
@endsection
