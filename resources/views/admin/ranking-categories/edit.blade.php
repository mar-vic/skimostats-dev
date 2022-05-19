@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <div>
                        <a href='{{ route('admin.ranking-categories.list') }}'>Ranking categories</a> &raquo;
                        @if($edit)
                            Edit
                        @else
                            Add
                        @endif
                    </div>
                </div>

                <div class="card-body">
                <form method='POST' enctype="multipart/form-data" action='{{ route('admin.' . $slug . '.' . ($edit ? 'update' : 'create'), $edit ? [$entry] : []) }}'>

                    @include('layouts.errors')
                    @include('layouts.success')

                    <table class='table'>
                        <tbody>
                            <tr>
                                <td style='width: 250px'>Name:</td>
                                <td>
                                    <input type='text' name='name' value='{{ old('name', $entry->name) }}' class='form-control' />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Rank-Point map
                                </td>
                                <td>
                                    <table class="table map-table">
                                        <thead>
                                            <tr>
                                                <th>Rank</th>
                                                <th>Points</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            @if(count($ranks))
                                                @foreach($ranks as $key=>$rank)
                                                <tr>
                                                    <td>
                                                        <input type="text" class="map-rank form-control" name="mapRank[]" value="{{ $rank }}">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="map-point form-control" name="mapPoint[]" value="{{ isset($points[$key]) ? $points[$key] : '' }}">
                                                    </td>
                                                    <td>
                                                        <a href="#" class="remove-row btn btn-sm btn-danger">&times;</a>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td>
                                                        <input type="text" class="map-rank form-control" name="mapRank[]" value="1">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="map-point form-control" name="mapPoint[]">
                                                    </td>
                                                    <td>
                                                        <a href="#" class="remove-row btn btn-sm btn-danger">&times;</a>
                                                    </td>
                                                </tr>
                                            @endif

                                        </tbody>
                                        <tfoot>
                                            <tr style="display:none;" id="row-to-copy">
                                                <td>
                                                    <input type="text" class="map-rank form-control" name="mapRank[]">
                                                </td>
                                                <td>
                                                    <input type="text" class="map-point form-control" name="mapPoint[]">
                                                </td>
                                                <td>
                                                    <a href="#" class="remove-row btn btn-sm btn-danger">&times;</a>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                    <a href="#" class="add-map-row btn btn-success">+ Add row</a>
                                </td>
                            </tr>

                        </tbody>
                    </table>

                    @csrf
                    <button type="submit" class='btn btn-primary'>Save</button>
                </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    $(function(){
        if(!Boolean($('.map-table tbody tr:last-child .map-rank').val())) {
            $('.map-table tbody tr:last-child').remove()
        }
        $('.add-map-row').click(function(e) {
            e.preventDefault()

            var $tr = $('#row-to-copy').clone()

            var lastRankNumber = $('.map-table tbody tr:last-child .map-rank').val() || 0
            $tr.find('.map-rank').val(Number(lastRankNumber) + 1)

            $('.map-table tbody').append($tr)
            $tr.show()
            $tr.find('.map-point').val("").focus()
        })

        $(document).on('click', '.remove-row', function(e) {
            e.preventDefault()
            $(this).parent().parent().remove()
        })
    })
</script>
@endpush
