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
          }" x-init="highlightedRowId=selectedRaceCat + '-1';$store.chart.selectedRaceCat=selectedRaceCat;$store.chart.data=data" class="container position-relative pb-5">

    <!-- FILTERS -->
    <div class="row mb-4">

        <!-- SEASON FILTER -->
        <div class="col-6 col-md-2 mt-1">
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

        <div class="col-7 col-md-9">
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
      <div class="col">
          @include('front/partials/histogram')
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script src="{{mix('js/statistics.js')}}"></script>

<script type="text/javascript">
  alert(Alpine.store('chart').data)
</script>
@endpush
