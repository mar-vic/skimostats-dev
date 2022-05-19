@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        <a href='{{ route('admin.athletes.list') }}'>Athletes</a>
                        &raquo;
                        @if($edit)
                            Edit
                        @else
                            Add
                        @endif
                    </div>
                    <div>
                        <a href="{{route('admin.athletes.races', $entry) }}" class="btn btn-sm btn-primary">Athlete races</a>
                    </div>
                </div>

                <div class="card-body">
                <form method='POST' enctype="multipart/form-data" action='{{ route('admin.athletes.' . ($edit ? 'update' : 'create'), $entry) }}'>

                    @include('layouts.errors')
                    @include('layouts.success')

                    <table class='table'>
                        <tbody>
                            <tr>
                                <td style='width: 250px'>First name:</td>
                                <td>
                                    <input type='text' name='firstName' value='{{ old('firstName', $entry->firstName) }}' class='form-control' />
                                </td>
                            </tr>

                            <tr>
                                <td>Last name:</td>
                                <td>
                                    <input type='text' name='lastName' value='{{ old('lastName', $entry->lastName) }}' class='form-control' />
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
                                <td>Gender:</td>
                                <td><select name='gender' class='form-control'>
                                    <option>- select gender -</option>
                                    @foreach(['male', 'female', 'other'] as $gender)
                                        <option value='{{ $gender }}' @if($gender == $entry->gender) selected='selected' @endif>{{ $gender }}</option>
                                    @endforeach
                                </select></td>
                            </tr>

                            <tr>
                                <td>Category:</td>
                                <td>
                                    <input type='text' name='category' value='{{ old('category', $entry->category) }}' class='form-control' />
                                </td>
                            </tr>

                            <tr>
                                <td>Place of birth:</td>
                                <td>
                                    <input type='text' name='placeOfBirth' value='{{ old('placeOfBirth', $entry->placeOfBirth) }}' class='form-control' />
                                </td>
                            </tr>

                            <tr>
                                <td>Date of birth:</td>
                                <td>
                                    <input type='text' name='dateOfBirth' value='{{ old('dateOfBirth', $entry->dateOfBirth) }}' class='form-control' />
                                </td>
                            </tr>

                            <tr>
                                <td>Weight (kg)</td>
                                <td>
                                    <input type='text' name='weight' value='{{ old('weight', $entry->weight) }}' class='form-control' />
                                </td>
                            </tr>
                            <tr>
                                <td>Height (cm)</td>
                                <td>
                                    <input type='text' name='height' value='{{ old('height', $entry->height) }}' class='form-control' />
                                </td>
                            </tr>
                            <tr>
                                <td>Favorite race name:</td>
                                <td>
                                    <input type='text' name='favoriteRace' value='{{ old('favoriteRace', $entry->favoriteRace) }}' class='form-control' />
                                </td>
                            </tr>
                            <tr>
                                <td>Favorite race ID:</td>
                                <td>
                                    <input type='number' name='favoriteRaceId' value='{{ old('favoriteRaceId', $entry->favoriteRaceId) }}' class='form-control' />
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
                            <tr>
                                <td>
                                    Social links
                                </td>
                                <td>
                                    <table style="width:100%">
                                        @foreach($socialPlatforms as $platform)
                                        <tr>
                                            <td>{{ $platform }}</td>
                                            <td><input type='text' name='links[{{$platform}}]' value='{{ old('links.'.$platform, $socialLinks[$platform] ?? '') }}' class="form-control" /></td>
                                        </tr>
                                        @endforeach
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Top results
                                </td>
                                <td>
                                    <table style="width:100%">
                                        @for($i=0; $i<5; $i++)
                                        <tr>
                                            <td>{{ $i + 1 }}.</td>
                                            <td><input type='text' placeholder="Top result text" name='topresult[{{$i}}]' value='{{ old('topresult.'.$i, $topresults[$i] ?? '') }}' class="form-control" /></td>
                                            <td><input type='number' min="1" placeholder="Place" name='topresultplace[{{$i}}]' value='{{ old('topresultplace.'.$i, $topresultplaces[$i] ?? 1) }}' class="form-control" /></td>
                                        </tr>
                                        @endfor
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <label>
                                        <input type="checkbox" name="attendsLausanne" value="1" id="lsn" @if(old('attendsLausanne', $entry->attendsLausanne)) checked="checked" @endif>
                                        Lausanne 2020
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <label>
                                        <input type="checkbox" name="show_in_api" value="1" id="lsn" @if(old('show_in_api', $entry->show_in_api)) checked="checked" @endif>
                                        Zobrazovať v API
                                    </label>
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
