@extends('layouts.main', [
'selectedPage' => 'rankings',
'title' => 'Rankings',
'meta_keywords' => 'ISMF rank, ismf ranking, ismf world cup rank, ismf rank '.$year.', ismf rank '. ($year-1).'/'.$year.', ismf rankings, ismf rank '.strtolower($category->name)
])
@section('content')
<div class="position-relative">

    <div class="container position-relative py-5">
        <div class="bg-logo-el"></div>
    <div class="bg-text">{{__('Rankings')}}</div>
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-between pt-0 pt-md-5">
                    <div class="flex-grow-1 text-center text-md-left mb-2">
    <h1 class="page-heading text-uppercase font-weight-bold text-blue"><span class="text-uppercase">{{ $rankingTypeText }}</span> {{__('Ranking')}}
                            @if($filter=='all-time' || $year==0)
                            All-time {{ $category->name }}
                            @else
                            {{ $year - 1 }}/{{ $year }}
                            @endif
                        </h1>
                    </div>
                    <div class="flex-shrink-0 mb-4 mb-md-0">
                        @if($category->slug)
                        <div class="position-relative dropdown__container no-vue">
                            <button class="badge badge--custom dropdown__toggle">

                                @if($year != 0)
                                {{$year-1}}/{{$year}}
                                @else
                                {{__('All-time')}}
                                @endif
                                <i class="fas fa-caret-down"></i>
                            </button>

                            <div class="dropdown__menu dropdown__menu--right">

                                @if ($rankingType == 1)
                                    <a href="{{ $filter == 'race-type'
                                    ? route('rankings.type.category', [$raceType->slug, $category->slug])
                                    : route('rankings.all-time', [$category->slug]) }}" class="@if($year==0) font-weight-bold @endif">{{__('All-time')}}</a>
                                @elseif ($rankingType == 2)
                                    <a href="{{ $filter == 'race-type'
                                    ? route('rankings.type.category.ismf', [$raceType->slug, $category->slug])
                                    : route('rankings.all-time-ismf', [$category->slug]) }}" class="@if($year==0) font-weight-bold @endif">{{__('All-time')}}</a>
                                @else
                                    <a href="{{ $filter == 'race-type'
                                    ? route('rankings.type.category.youthwc', [$raceType->slug, $category->slug])
                                    : route('rankings.all-time-youthwc', [$category->slug]) }}" class="@if($year==0) font-weight-bold @endif">{{__('All-time')}}</a>
                                @endif

                                @foreach ($years as $yr)
                                <a href="{{ $filter == 'race-type'
                                        ? route($rankingType==1 ? 'rankings.type.year' : ($rankingType==2 ? 'rankings.type.year.ismf': 'rankings.type.year.youthwc'), [$yr, $raceType->slug, $category->slug])
                                        : route('rankings.year', [$rankingType == 1 ? 'skimostats' : ($rankingType==2 ? 'ismf' : 'youthwc'), $yr, $category->slug]) }}" class="pr-4 @if($yr==$year) font-weight-bold @endif">{{$yr-1}}/{{$yr}}</a>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="d-flex justify-content-center justify-content-md-between flex-wrap mb-2">
                                       <div class="mb-3 mb-md-0 text-center">

                                       <a
                                       href="{{ route('rankings.year', ['skimostats', $year==0 ? date("Y") : $year, 'men']) }}"
                                       class="badge my-1 badge--custom @if($rankingType == 1) badge-active @endif"
                                       >
                                        {{__('SkiMo Stats ranking')}}
                                       </a>

                                           <a href="{{ route('rankings.year', ['ismf', $year==0 ? date("Y") : $year, 'men']) }}" class="badge my-1 badge--custom @if($rankingType == 2) badge-active @endif">ISMF World Cup {{__('ranking')}}</a>

                                               <a href="{{ route('rankings.year', ['youthwc', $year==0 ? date("Y") : $year, 'u18-men']) }}" class="badge my-1 badge--custom @if($rankingType == 3) badge-active @endif">Youth World Cup {{__('ranking')}}</a>

                    </div>
                    <div class="text-center">


                        <!-- DROPDOWN FOR RACE CATS -->
                        <div class="position-relative dropdown__container no-vue d-inline-flex">
                            <button class="badge badge--custom dropdown__toggle badge-active">
                                {{ $category->name }}
                                <i class="fas fa-caret-down"></i>
                            </button>

                            <div class="dropdown__menu dropdown__menu--right text-nowrap text-left">
                                @foreach($categories as $cat)
                                <a href="{{ $filter=='race-type'
                            ? ( $year==0
                                ? route($rankingType==1 ? 'rankings.type.category' : ($rankingType==2 ? 'rankings.type.category.ismf' : 'rankings.type.category.youthwc'), [$raceType->slug, $cat->slug])
                                : route($rankingType==1 ? 'rankings.type.year' : ($rankingType==2 ? 'rankings.type.year.ismf' : 'rankings.type.year.youthwc'), [$year, $raceType->slug, $cat->slug])
                            )
                            : ( $year==0
                                ? route($rankingType==1 ? 'rankings.all-time' : ($rankingType==2 ? 'rankings.all-time-ismf' : 'rankings.all-time-youthwc'), [$cat->slug])
                                : route('rankings.year', [$rankingType == 1 ? 'skimostats' : ($rankingType==2 ? 'ismf' : 'youthwc'), $year, $cat->slug])
                            ) }}" class="@if($cat->id==$category->id) font-weight-bold @endif">{{ $cat->name }}</a>
                                @endforeach
                            </div>
                        </div>
                        <!-- END OF DROPDOWN FOR RACE CATS -->


                        <!-- DROPDOWN FOR RACETYPES -->
                        <div class="position-relative dropdown__container no-vue d-inline-flex">
                            <button class="badge badge--custom dropdown__toggle @if($filter == 'race-type') badge-active @endif">
                                @if($filter == 'race-type')
                                {{ $raceType->name }}
                                @else
                                {{__('Type of race')}}
                                @endif
                                <i class="fas fa-caret-down"></i>
                            </button>

                            <div class="dropdown__menu dropdown__menu--right text-nowrap text-left">
                                @foreach($raceTypes as $type)
                                <a href="{{ $year == 0
                                        ? route($rankingType==1 ? 'rankings.type.category' : ($rankingType==2 ? 'rankings.type.category.ismf' : 'rankings.type.category.youthwc'), [$type->slug, $category->slug])
                                        : route($rankingType==1 ? 'rankings.type.year' : ($rankingType==2 ? 'rankings.type.year.ismf' : 'rankings.type.year.youthwc'), [$year, $type->slug, $category->slug]) }}" class="@if($filter=='race-type' && $entityId==$type->id) font-weight-bold @endif">{{ $type->name }}</a>
                                @endforeach

                                @if($filter == 'race-type')
                                <a href="{{ $year==0
                                        ? route($rankingType==1 ? 'rankings.all-time' : ($rankingType==2 ? 'rankings.all-time-ismf' : 'rankings.all-time-youthwc'), [$category->slug])
                                        : route('rankings.year', [$rankingType == 1 ? 'skimostats' : ($rankingType==2 ? 'ismf' : 'youthwc'), $year, $category->slug]) }}">&times; Clear filter</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <table class="table table--races table--races-striped">
                    <thead>
                        <tr>
                            <th style="width: 50px">#</th>
                            <th>Name</th>
                            <th style="width: 80px;" class="text-right">Points</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($ranking as $place => $item)
                        <tr>
                            <td class="text-nowrap">
                                @if ($place < 0) <span class="font-weight-bold" style="color:{{ ['#EEC700', '#A0A0A0', '#CD7F32'][$place] }}">
                                    {{$place + 1}}.
                                    </span>
                                    @else
                                    {{$place + 1}}.
                                    @endif

                                    @if (!$filter && isset($item->rank, $item->rankBefore) && $item->rank != $item->rankBefore)
                                    @if ($item->rank - $item->rankBefore < 0) <span class="fa fa-caret-up text-success" title="change: +{{ $item->rankBefore - $item->rank }}"></span>
                                        @else
                                        <span class="fa fa-caret-down text-danger" title="change: {{ $item->rankBefore - $item->rank }}"></span>
                                        @endif
                                        @endif
                            </td>
                            <td>
                                <a href="{{ route('athletes.detail.slug', [$item->athleteSlug]) }}" class="d-inline-flex align-items-center" id="athlete-rank-{{$item->athleteSlug}}">
                                    <img src="{{ asset('images/flags/flags-mini/'.strtolower($item->countryCode).'.png') }}" class="flex-shrink-0 flag-icon--micro mr-2" alt="">
                                    <div>
                                        {{$item->firstName}}
                                        <span class="text-uppercase">{{$item->lastName}}</span>
                                    </div>
                                </a>
                            </td>
                            <td class="text-right">
                                {{$item->pts}}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
