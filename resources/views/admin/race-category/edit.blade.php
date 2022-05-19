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
                        &raquo;
                        <a href='{{ route('admin.race-types.list', $race) }}'>Race types</a> &raquo;
                        <a href='{{ route('admin.race-types.edit', $raceType) }}'>{{ $raceType->name }}</a> &raquo;
                        <a href='{{ route('admin.' . $slug . '.list', $raceType) }}'>Race categories</a>
                        &raquo;
                        @if($edit)
                            Edit
                        @else
                            Add
                        @endif
                    </div>
                </div>

                <div class="card-body">
                <form method='POST' enctype="multipart/form-data" action='{{ route('admin.' . $slug . '.' . ($edit ? 'update' : 'create'), $edit ? [$raceType, $entry] : $raceType) }}'>

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
                                <td></td>
                                <td>
                                    <label><input type='checkbox' name='isMain' value='1' {{ old('isMain', $entry->isMain) ? " checked='checked'" : "" }} /> Main category</label>
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>
                                    <label><input type='checkbox' name='isTeam' value='1' {{ old('isTeam', $entry->isTeam) ? " checked='checked'" : "" }} /> Team category (teams of two and more)</label>
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
