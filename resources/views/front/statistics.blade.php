@extends('layouts.main', [
'selectedPage' => 'statistics',
'title' => 'Statistics',
'meta_keywords' => 'skimo statistics, skimo stats, skimo results, skimountaineering statistics, skimo results, pierra menta results, ismf world cup, ismf world cup results, skimo countries, skimo racers, popular athletes, oldest skimo athlete, youngest skimo athlete, tallest skimo athlete, heaviest skimo athlete, lightest skimo athlete'
])
@section('content')
<div id="statistics-vm-container">


</div>
@endsection

@push('scripts')
<script src="{{mix('js/statistics.js')}}"></script>
<script>
    statisticsVM.init()
</script>
@endpush
