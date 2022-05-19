@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card mb-3">
                <div class="card-header d-flex align-items-center">
                    <div>
                        <a href='{{ route('admin.teams.list') }}'>Teams</a>
                        &raquo;
                        @if($edit)
                            Edit
                        @else
                            Add
                        @endif
                    </div>
                </div>

                <div class="card-body">
                <form method='POST' enctype="multipart/form-data" action='{{ route('admin.teams.' . ($edit ? 'update' : 'create'), $entry) }}'>

                    @include('layouts.errors')
                    @include('layouts.success')

                    <table class='table'>
                        <tbody>
                            <tr>
                                <td style='width: 250px'>Team name</td>
                                <td>
                                    <input type='text' name='name' value='{{ old('name', $entry->name) }}' class='form-control' />
                                </td>
                            </tr>

                            <tr>
                                <td>Country:</td>
                                <td><select name='countryId' class='form-control'>
                                    <option value='0'>- select country -</option>
                                    @foreach($countries as $country)
                                        <option value='{{ $country->id }}' @if($country->id == $entry->countryId) selected='selected' @endif>{{ $country->name }}</option>
                                    @endforeach
                                </select></td>
                            </tr>

                            <tr>
                                <td>Description:</td>
                                <td>
                                    <textarea name='description' class='form-control'>{{ old('description', $entry->description)}}</textarea>
                                </td>
                            </tr>
                            <tr>
                                <td>Profile picture: (empty if same)</td>
                                <td>
                                    @if($entry->image)
                                        <img src='{{ $entry->imagePath }}' class='img-responsive mb-2' style="max-width: 300px;" />
                                    @endif
                                    <input type='file' name='image' class='form-control' />
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    @csrf
                    <button type='submit' class='btn btn-primary'>Save</button>
                </form>
                </div>
            </div>


            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <div>
                        Team athletes
                    </div>
                </div>

                <div class='card-body'>
                    <form method='POST' action='{{ route('admin.team.add-athlete', $entry) }}'>
                        @csrf
                        <table class="mb-3">
                            <tr>
                                <td>Athlete ID:</td>
                                <td><input type='number' name='athleteId' class="form-control" placeholder='Athlete ID' /></td>
                            </tr>
                            <tr>
                                <td>Custom name</td>
                                <td><input type='text' name='customName' class="form-control" placeholder='Custom name (Leave empty if the same)' /></td>
                            </tr>
                            <tr>
                                <td>Position name</td>
                                <td><input type='text' name='positionName' class="form-control" placeholder='Position name' /></td>
                            </tr>
                            <tr>
                                <td>Order:</td>
                                <td><input type='number' name='order' class="form-control" placeholder='Order ' /></td>
                            </tr>
                            <tr>
                                <td><input type='submit' value='Add athlete' class="btn btn-success" /></td>
                            </tr>
                        </table>
                    </form>


                    <table class="table table-striped">
                        <tbody>
                            @foreach($entry->athletes as $athlete)
                                <tr>
                                    <td>
                                        @if ($athlete->positionName)
                                            [{{ $athlete->positionName }}]
                                        @endif
                                        <a href='{{ route('admin.athletes.edit', $athlete->athleteId) }}'>{{$athlete->name}}</a>
                                        @if ($athlete->athlete->name != $athlete->name)
                                            ({{ $athlete->athlete->name }})
                                        @endif
                                    </td>
                                    <td style="text-align:right;">
                                        <a href='{{ route('admin.team.remove-athlete', $athlete) }}' class="btn btn-sm btn-danger">Delete</a>
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
