
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    Users
                </div>

                <div class="card-body">
                    @include('layouts.success')

                    <form action="{{ route('admin.users.create') }}" method="POST">
                        @csrf
                        <input type="text" name="name" class="form-control" />
                        <button class="btn btn-success">Add</button>
                    </form>

                    <table class='table table-striped'>
                        <thead>
                            <tr>
                                <th style="width: 45px;">ID</th>
                                <th>
                                    Name
                                </th>
                                <th>
                                    Token
                                </th>
                                {{-- <th style='width: 150px;text-align:right'>
                                    Action
                                </th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($entries as $entry)
                                <tr>
                                    <td>{{ $entry->id }}</td>
                                    <td>{{ $entry->name }}</td>
                                    <td>
                                        {{ $entry->api_token }}
                                        {{-- <a href='{{ route('admin.' . $slug . '.create-', $entry) }}' class='btn btn-sm btn-primary'>Edit</a>
                                        <a href='{{ route('admin.' . $slug . '.delete', $entry) }}'
                                            class='btn btn-sm btn-danger'
                                            onclick="return confirm('Really delete {{ $entry->name }}?')">Delete</a> --}}
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
