@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <div>
                        <a href='{{ route('admin.' . $slug . '.list') }}'>{{ $entityName }}</a>
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
                    <a href='{{ route('admin.race-events.list', $entry) }}' class='btn btn-outline-secondary'>Race events</a>
                </div>
                @endif

                <form method='POST' enctype="multipart/form-data" action='{{ route('admin.' . $slug . '.' . ($edit ? 'update' : 'create'), $entry) }}'>

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
                            @if($entry->slug)
                            <tr>
                                <td>Slug:</td>
                                <td>
                                    <b>{{ $entry->slug }}</b>
                                    (<a href="{{route('race-overview', $entry->slug) }}" target="_blank">link to overview</a>)
                                </td>
                            </tr>
                            @endif
                            <tr>
                                <td>Ranking Category</td>
                                <td>
                                    <select name="rankingCategoryId" class="form-control">
                                        <option value="">- Select category -</option>
                                        @foreach($rankingCategories as $rankingCategory)
                                            <option value="{{$rankingCategory->id}}" @if($entry->rankingCategoryId == $rankingCategory->id) selected="selected" @endif>{{$rankingCategory->name}}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>BG text (in race overview):</td>
                                <td>
                                    <input type='text' name='bgText' value='{{ old('bgText', $entry->bgText) }}' class='form-control' />
                                </td>
                            </tr>
                            <tr>
                                <td>Alternative name (in race overview):</td>
                                <td>
                                    <input type='text' name='alternativeName' value='{{ old('alternativeName', $entry->alternativeName) }}' class='form-control' />
                                </td>
                            </tr>
                            <tr>
                                <td>Place:</td>
                                <td>
                                    <input type='text' name='place' value='{{ old('place', $entry->place) }}' class='form-control' />
                                </td>
                            </tr>
                            <tr>
                                <td>Description:</td>
                                <td><textarea name='description' class='form-control' rows='4'>{{ old('description', $entry->description) }}</textarea></td>
                            </tr>
                            <tr>
                                <td>Meta keywords:</td>
                                <td><textarea name='metaKeywords' class='form-control' rows='4'>{{ old('metaKeywords', $entry->metaKeywords) }}</textarea></td>
                            </tr>
                            <tr>
                                <td>Meta description:</td>
                                <td><textarea name='metaDescription' class='form-control' rows='4'>{{ old('metaDescription', $entry->metaDescription) }}</textarea></td>
                            </tr>
                            <tr>
                                <td>Year start:</td>
                                <td>
                                    <input type='text' name='yearStart' value='{{ old('yearStart', $entry->yearStart ?? '2019-01-01 00:00:00') }}' class='form-control' />
                                </td>
                            </tr>
                            <tr>
                                <td>Year end:</td>
                                <td>
                                    <input type='text' name='yearEnd' value='{{ old('yearEnd', $entry->yearEnd ?? '2019-12-31 23:59:59') }}' class='form-control' />
                                </td>
                            </tr>
                            <tr>
                                <td>Race picture: (empty if same)</td>
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
        </div>
    </div>
</div>
@endsection
