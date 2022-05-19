
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card mb-5">
                <div class="card-header d-flex align-items-center">
                    <a href="{{ route('admin.categories.list') }}">Categories</a> &raquo; Edit category
                </div>

                <div class="card-body">
                    @include('layouts.errors')
                    @include('layouts.success')

                    <form action="{{ route('admin.categories.update', ['category' => $category]) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <table class="table">
                                <tr>
                                    <td>Name</td>
                                    <td>
                                        <input type="text" class="form-control" value="{{ old('name', $category->name) }}" name="name" placeholder="Category name">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Age</td>
                                    <td>
                                        <select name="age" class="form-control">
                                            <option value="">- Age (optional) -</option>
                                            @foreach(['adult','junior','cadet','master'] as $age)
                                                <option value="{{$age}}" @if($age == old('age', $category->age)) selected="selected" @endif>{{$age}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Gender</td>
                                    <td>
                                        <select name="gender" class="form-control">
                                            <option value="">- Gender (optional) -</option>
                                            @foreach(['male','female','both'] as $gender)
                                                <option value="{{$gender}}" @if($gender == old('gender', $category->gender)) selected="selected" @endif>{{$gender}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    
                                </tr>
                                <tr>
                                    <td>Priority</td>
                                    <td>
                                        <input type="number" name="priority" class="form-control" value="{{ old('priority', $category->priority) }}" />
                                    </td>
                                </tr>
                            </table>

                            <button class="btn btn-success" type="submit">Edit category</button>
                        </div>
                    </form>

                </div>
            </div>

            <div class="card mb-5">
                <div class="card-header d-flex align-items-center">
                    Events
                </div>

                <div class="card-body">
                    <table class="table table-striped">
                        <tbody>
                            @foreach($category->raceEvents as $event)
                                <tr>
                                    <td>
                                        {{ $event->startDate }}
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.race-events.edit', $event) }}">{{ $event->name }}</a>
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
