
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    Categories
                </div>

                <div class="card-body">
                    @include('layouts.errors')
                    @include('layouts.success')

                    <form action="{{ route('admin.categories.create') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <table class="table">
                                <tr>
                                    <td>
                                        <input type="text" class="form-control" name="name" placeholder="Category name">
                                    </td>
                                    <td>
                                        <select name="age" class="form-control">
                                            <option value="">- Age (optional) -</option>
                                            @foreach(['adult','junior','cadet','master'] as $age)
                                                <option value="{{$age}}">{{$age}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select name="gender" class="form-control">
                                            <option value="">- Gender (optional) -</option>
                                            @foreach(['male','female','both'] as $gender)
                                                <option value="{{$gender}}">{{$gender}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <button class="btn btn-success" type="submit">Add category</button>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </form>

                    <table class='table table-striped'>
                        <thead>
                            <tr>
                                <th>
                                    Category
                                </th>
                                <th>Number of events</th>
                                <th>
                                    Priority
                                </th>
                                <th>
                                    Gender
                                </th>
                                <th>
                                    Age
                                </th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($entries as $entry)
                                <tr>
                                    <td>
                                        ({{$entry->id}}) <b>{{ $entry->name }}</b>

                                    </td>
                                    <td>
                                        {{ $entry->raceEvents->count() }}
                                    </td>
                                    <td style="width:50px">
                                        {{ $entry->priority }}
                                    </td>
                                    <td style="width:50px">
                                        {{ $entry->gender }}
                                    </td>
                                    <td style="width:50px">
                                        {{ $entry->age }}
                                    </td>
                                    <td style="width:50px">
                                        <a href="{{ route('admin.categories.edit', $entry) }}" class="btn btn-sm btn-primary">edit</a>
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
