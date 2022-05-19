@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <div>
                        <a href='{{ route('admin.races.list') }}'>Races</a> &raquo;
                        <a href='{{ route('admin.races.edit', $entry->raceId ?? $race) }}'>{{ $race->name ?? $entry->race->name }}</a>
                        &raquo;
                        <a href='{{ route('admin.' . $slug . '.list', $entry->raceId ?? $race) }}'>Race types</a>
                        &raquo;
                        @if($edit)
                            Edit
                        @else
                            Add
                        @endif
                    </div>
                </div>

                <div class="card-body">

                    @if($edit)
                    <div class='mb-3'>
                        <a href='{{ route('admin.race-category.list', $entry) }}' class='btn btn-outline-secondary'>Race categories</a>
                    </div>
                    @endif

                    <form method='POST' enctype="multipart/form-data" action='{{ route('admin.' . $slug . '.' . ($edit ? 'update' : 'create'), $edit ? $entry : $race) }}'>

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
                                    <td>Position:</td>
                                    <td>
                                        <input type='number' min='0' name='position' value='{{ old('position', $entry->position ?? $lastPosition) }}' class='form-control' />
                                    </td>
                                </tr>
                                <tr>
                                    <td>Type:</td>
                                    <td>
                                        <select class="form-control" name='type'>
                                            <option>Other race</option>
                                            @foreach (App\RaceType::getDefaultTypes() as $item)
                                                <option value="{{$item['type']}}" @if($item['type']==$entry->type) selected="selected" @endif>{{$item['name']}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>
                                        <label><input type='checkbox' name='isTeam' value='1' {{ old('isTeam', $entry->isTeam) ? " checked='checked'" : "" }} /> Team race (teams of two and more)</label>
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>
                                        <label><input type='checkbox' name='hasStages' value='1' {{ old('hasStages', $entry->hasStages) ? " checked='checked'" : "" }} /> Has stages (etapy)</label>
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
</div>
@endsection
