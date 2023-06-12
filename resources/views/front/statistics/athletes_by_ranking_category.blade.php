@extends('front.hypermedia_statistics')


@section('content')
@parent

@if ($data->isNotEmpty())
<div x-data="{
          data: {{ $data }},
          selectedRaceCat: '{{ $categories->first() }}',
          highlightedAthlete: {{ $data->get($categories->first())->first() }},
          highlightedRowId: '',
          highlightedPosition: 1,
          }" x-init="highlightedRowId=selectedRaceCat + '-1'" class="container position-relative pb-5">

    <!-- FILTERS -->
    <div class="row mb-4">

        <!-- RANKING CATEGORY FILTER -->
        <div class="col-6 col-md-3 mt-1">
            <div class="row">
                <div class="my-auto col col-auto font-weight-bold text-uppercase text-blue">RANK CATEGORY:</div>
                <div class="col col-auto pl-1 pr-1">

                    <div class="position-relative dropdown__container no-vue">

                        <button class="badge badge--custom dropdown__toggle">
                            {{ ucwords(str_replace('-', ' ', $rankingCategory)) }}
                            <i class="fas fa-caret-down"></i>
                        </button>

                        <div class="dropdown__menu dropdown__menu--right">

                            <a href={{ "/statistics/".$statsCategorySlug."/all" }} {{ $rankingCategory == 'all' ? "class=fw-bold" : '' }}>All</a>

                            <a {{ $rankingCategory == 'world-cup' ? "class=fw-bold" : '' }} href={{ "/statistics/".$statsCategorySlug."/world-cup" }}>World Cup</a>

                            <a {{ $rankingCategory == 'grand-course' ? "class=fw-bold" : '' }} href={{ "/statistics/".$statsCategorySlug."/grand-course" }}>Grand Course</a>

                        </div>

                    </div>

                </div>
            </div>
        </div>

        <div class="col-6 col-md-9">
            <div class="row justify-content-right">

                @foreach ($categories as $category)

                <div class="col col-auto ml-1 mr-1 pl-0 pr-0">
                    <a @click.prevent="selectedRaceCat = $event.target.innerHTML; highlightedRowId = selectedRaceCat + '-1'; highlightedPosition = 1" href="#" :class="selectedRaceCat == $el.innerHTML ? 'badge-active' : ''" class="badge my-1 badge--custom">{{ $category }}</a>
                </div>

                @endforeach
            </div>
        </div>
    </div>

    <!-- DATA -->

    <div class="row">

        <div class="col-3 d-none d-md-block">
            <a :href="'/athlete/' + highlightedAthlete.slug" class="latest-results__first mb-3 profile-pic">
                <div class="latest-results__photo position-relative" x-bind:style="'background-image:url(/images' + (highlightedAthlete.profilePic ? '/athletes/' + highlightedAthlete.profilePic : (highlightedAthlete.gender == 'female' ? '/woman_silhouette.jpg' : '/man_silhouette.jpg')) + ')'">
                    <div class="latest-results__number latest-results__number--first" x-text="highlightedPosition"></div>
                </div>
                <div class="latest-results__info">
                    <div class="font-weight-bold" x-text="highlightedAthlete.firstName + ' ' + highlightedAthlete.lastName"></div>
                    <div class="small mb-2" x-text="'wins: ' + highlightedAthlete.qty"></div>
                    <div class="d-flex justify-content-between align-items-center">
                        <img :if="highlightedAthlete.country" class="latest-results__mini-flag" :src="'/images/flags/flags-mini/' + highlightedAthlete.country.toLowerCase() + '.png'" :alt="highlightedAthlete.country" />
                        <div class="latest-results__view-profile">View profile</div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-9">

            @foreach ($categories as $category)
            <div x-show="'{{ $category }}' == selectedRaceCat" class="row">
                <table class="table table-stats table--races table--races-striped">
                    <thead>
                        <tr>
                            <th style="width:10%">#</th>
                            <th style="">Name</th>
                            <th style="text-align:right">
                                {{ $metric[0] }}
                                @if ($metric[1] != '')
                                <span class="metric-tooltip" data-bs-toggle="tooltip" data-bs-placement="bottom" title="{{ $metric[1] }}">
                                    ?
                                </span>
                                @endif
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data[$category] as $entry)
                        <tr id="{{ $category.'-'.$loop->iteration }}" x-data="{ athlete: {{$entry}} }" @click="highlightedRowId = $el.id;highlightedPosition = {{ $loop->iteration }}" x-effect="highlightedRowId == $el.id ? highlightedAthlete = athlete : null" :class="highlightedRowId == $el.id ? 'highlighted-row text-blue' : ''" style="cursor:pointer">
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <span class="d-none d-md-inline">{{$entry['firstName']}} {{$entry['lastName']}}</span><a class="d-inline d-md-none" href="/athlete/{{$entry['slug']}}">{{$entry['firstName']}} {{$entry['lastName']}}</a>
                            </td>
                            <td style="text-align:right">{{$entry['qty']}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endforeach

        </div>
    </div>
</div>
@else
<div class="container position-relative pb-5">
    <!-- FILTERS -->
    <div class="row mb-4 gx-5">

        <!-- SEASON FILTER -->
        <div class="col-5 col-md-2 mt-1">
            <div class="row">
                <div class="my-auto col col-auto font-weight-bold text-uppercase text-blue">season:</div>
                <div class="col col-auto pl-1 pr-1">

                    <div class="position-relative dropdown__container no-vue">

                        <button class="badge badge--custom dropdown__toggle">
                            {{ $year == 'all-seasons' ? 'All' : $year }}
                            <i class="fas fa-caret-down"></i>
                        </button>

                        <div class="dropdown__menu dropdown__menu--right">

                            <a href={{ "/statistics/".$statsCategorySlug."/all-seasons" }} {{ $year == 'all-seasons' ? "class=fw-bold" : '' }}>All</a>

                            @foreach ($years as $yr)
                            <a {{ $year == $yr ? "class=fw-bold" : '' }} href={{ "/statistics/".$statsCategorySlug."/".$yr }}>{{ $yr }}</a>
                            @endforeach

                        </div>

                    </div>

                </div>
            </div>
        </div>

        <div class="col">
            <div class="alert alert-info" role="alert">
                Sorry, but we have no data for this season!
            </div>
        </div>
    </div>
</div>

@endif

@endsection
