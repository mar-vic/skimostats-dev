@extends('front.statistics')


@section('content')
@parent

@if ($data->isNotEmpty())
<div x-data="{ selectedRaceCat: '{{ $categories->first() }}' }"
     class="container position-relative pb-5">

    <!-- FILTERS -->
    <div class="row mb-4">

        <!-- SEASON FILTER -->
        <div class="col-auto mt-1">
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

        <div class="col d-flex justify-content-center">
            <div class="row">

                @foreach ($categories as $category)

                <div class="col col-auto ml-1 mr-1 pl-0 pr-0">
                    <a @click.prevent="selectedRaceCat = $event.target.innerHTML" href="#" :class="selectedRaceCat == $el.innerHTML ? 'badge-active' : ''" class="badge my-1 badge--custom">{{ $category }}</a>
                </div>

                @endforeach
            </div>
        </div>
    </div>

    <!-- DATA -->

    <div class="row">

        <div class="col">

            @foreach ($categories as $category)
            <div x-show="'{{ $category }}' == selectedRaceCat" class="row">
                <table class="table table-stats table--races table--races-striped">
                    <thead>
                        <tr>
                            <th style="width:10%">#</th>
                            <th>Name</th>
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
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <a href="/athletes?country={{$entry['countryCode']}}">{{$entry['countryName']}}</a>
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
