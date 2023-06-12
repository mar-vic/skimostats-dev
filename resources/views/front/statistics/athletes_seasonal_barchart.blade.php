@extends('front.hypermedia_statistics')


@section('content')
@parent

@if ($data->isNotEmpty())
<div x-data="{
          data: {{ $data }},
          selectedRaceCat: '{{ $categories->first() }}',
          }" x-init="$store.chart.selectedRaceCat=selectedRaceCat;$store.chart.data=data;" class="container position-relative pb-5">

    <!-- FILTERS -->
    <div class="row mb-4">

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

        <div class="col-auto col-md-8">
            <div class="row justify-content-right">

                @foreach ($categories as $category)

                <div class="col col-auto ml-1 mr-1 pl-0 pr-0">
                    <a @click.prevent="selectedRaceCat = $event.target.innerHTML; $store.chart.selectedRaceCat=selectedRaceCat; $dispatch('categoryChanged')" href="#" :class="selectedRaceCat == $el.innerHTML ? 'badge-active' : ''" class="badge my-1 badge--custom">{{ $category }}</a>
                </div>

                @endforeach

            </div>
        </div>

        <div class="col-auto col-md-2 d-flex justify-content-end">
          <label class="switch btn-switch">
            <input @click="$dispatch('modeChanged')" type="checkbox" name="time_mode" id="time_mode" value="1">
            <label for="time_mode" data-on="AVG" data-off="SUM" class="btn-switch-inner"></label>
          </label>
        </div>

    </div>

    <!-- DATA -->

    <div class="row">
        <div class="col">
            <div id="chart_div" style="width: 100%; height: 500px;"></div>
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
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script defer type="text/javascript">
  function drawChart(selectedData) {
    google.charts.load('current', {'packages':['bar']});
    google.charts.setOnLoadCallback(draw);

    function draw() {
      if (Alpine.store('chart').mode == 'SUM') {
        var data = new google.visualization.arrayToDataTable([
          ['Age', 'Points (Total)'],
          ["18-25", selectedData['18-25']['pointsSum']],
          ["26-35", selectedData['26-35']['pointsSum']],
          ["36-45", selectedData['36-45']['pointsSum']],
          ["46+", selectedData['46+']['pointsSum']],
        ]);
      } else {
        var data = new google.visualization.arrayToDataTable([
          ['Age', 'Points (Average)'],
          ["18-25", selectedData['18-25']['pointsSum'] / selectedData['18-25']['athletesCount']],
          ["26-35", selectedData['26-35']['pointsSum'] / selectedData['26-35']['athletesCount']],
          ["36-45", selectedData['36-45']['pointsSum'] / selectedData['36-45']['athletesCount']],
          ["46+", selectedData['46+']['pointsSum'] / selectedData['46+']['athletesCount']],
        ]);
      }

      var options = {
        bars: 'vertical',
        axes: { x: { 0: { side: 'bottom', label: 'Age'} } },
        bar: { groupWidth: "90%" }
      };

      var chart = new google.charts.Bar(document.getElementById('chart_div'));
      chart.draw(data, options);
    }
  }

  document.addEventListener('alpine:init', () => { Alpine.store('chart', { selectedRaceCat: '', data: null, mode: 'SUM', getSelectedData() { return Alpine.store('chart').data[Alpine.store('chart').selectedRaceCat]; } }) } );

  document.addEventListener('alpine:initialized', () => {
    drawChart(Alpine.store('chart').getSelectedData());
  })

  document.addEventListener('categoryChanged', () => {
    drawChart(Alpine.store('chart').getSelectedData());
  });

  document.addEventListener('modeChanged', () => {
    if (Alpine.store('chart').mode == 'SUM') {
      Alpine.store('chart').mode = 'AVG'
    } else {
      Alpine.store('chart').mode = 'SUM'
    }
    drawChart(Alpine.store('chart').getSelectedData());
  });
</script>
@endpush
