@extends('layouts.main', [
    'selectedPage' => 'races',
    'title' => 'World Cup ' . $year,
    'meta_keywords' => 'skimo world cup, ismf world cup, ismf world cup skimountaineering, skimo results, skimountaineering results, ismf results, world cup results, skimo results, skimo stats, skimo stats results, skimo cup '.$year.', skimo races '.$year.', ISMF world cup, ismf world cup '.$year.', ismf cup, ismf races '.$year
])
@section('content')
<div class="position-relative">

    <div class="container position-relative py-5">
        <div class="bg-logo-el"></div>
        <div class="bg-text">World Cup {{ $year }}</div>
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-between pt-0 pt-md-5">
                    <div class="flex-grow-1 text-center text-md-left mb-2">
                        <h1 class="page-heading text-uppercase font-weight-bold text-blue">ISMF World Cup {{ $year }} - Races</h1>
                    </div>
                    <div class="flex-shrink-0 mb-4 mb-md-0">
                        <div class="position-relative dropdown__container no-vue">
                            <button class="badge badge--custom dropdown__toggle">
                                {{$year}}
                                <i class="fas fa-caret-down"></i>
                            </button>

                            <div class="dropdown__menu dropdown__menu--right">
                                @foreach ($years as $yr)
                                    <a href="{{ route('world-cup.year', $yr) }}" class="pr-4 @if($yr==$year) font-weight-bold @endif">{{$yr}}</a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table--races table--races-striped">
                        <thead>
                            <tr>
                                <th style="width: 50px">Date</th>
                                <th>Race</th>
                                <th style="width: 160px">Type</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($events as $event)
                            <tr>
                                <td>
                                    {{ (new \Carbon\Carbon($event->startDate))->isoFormat('DD/MM/YY') }}
                                </td>
                                <td>
                                    <a href="{{ route('raceEventDetail.slug', $event->slug) }}" class="d-inline-flex align-items-center">
                                        @if ($event->country)
                                            <img src='{{ asset('images/flags/flags-mini/'.strtolower($event->country->code).'.png') }}' alt="{{ $event->country->name }}" class="flag-icon--micro mr-2" />
                                        @endif
                                        <div>
                                            {{$event->name}}
                                        </div>
                                    </a>
                                    {{-- <a href="{{ route('athletes.detail.slug', [$item->athleteSlug]) }}"  class="d-inline-flex align-items-center">
                                        <img src="{{ asset('images/flags/flags-mini/'.strtolower($item->countryCode).'.png') }}" class="flex-shrink-0 flag-icon--micro mr-2" alt="">
                                        <div>
                                            {{$item->firstName}}
                                            <span class="text-uppercase">{{$item->lastName}}</span>
                                        </div>
                                    </a> --}}
                                </td>
                                <td class="text-nowrap">
                                    {{ $event->raceType->name }}
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
