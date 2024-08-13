@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    API Users
                </div>

                <div class="card-body">

                    <div class="mb-2 text-muted"><strong>Add New API User</strong></div>
                    <form class="mb-5" action="{{ route('admin.users.create') }}" method="POST">
                        @csrf
                        <div class="d-flex">
                            <input type="text" name="name" placeholder="Name" class="form-control mr-2" />
                            <input type="text" name="email" placeholder="Email" class="form-control mr-2" />
                            <button class="btn btn-success">Add</button>
                        </div>
                        @include('layouts.errors')
                        @include('layouts.success')
                    </form>


                    <div class="mb-2 text-muted"><strong>Active API Users</strong></div>
                    <table class='table table-striped' style="display:block;overflow-x:auto;white-space:nowrap;">
                        <thead>
                            <tr>
                                <th>
                                    Name
                                </th>
                                <th>
                                    Email
                                </th>
                                <th>
                                    Token
                                </th>
                                <th style='text-align:center'>
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($entries as $entry)
                            <tr>
                                <td>{{ $entry->name }}</td>
                                <td>{{ $entry->email }}</td>
                                <td>
                                    <span id="{{ 'token_' . $entry->id }}" onclick="copyToClipboard('token_{{ $entry->id }}')" data-toggle="tooltip" data-placement="top" title="Copy the token into clipboard" style="cursor:pointer;">{{ $entry->api_token }}</span>
                                </td>
                                <td style='text-align:center'>
                                    {{-- <a href='{{ route('admin.' . $slug . '.delete', $entry) }}'
                                    class='btn btn-sm btn-danger'
                                    onclick="return confirm('Really delete {{ $entry->name }}?')">
                                    Delete
                                    </a> --}}
                                    <form action="{{ route('admin.' . $slug . '.delete', $entry->id) }}" method="post">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <script>
                        function copyToClipboard(tokenSpanId) {
                            // Get the text field
                            var copyToken = document.getElementById(tokenSpanId);

                            // Copy the text inside the span into clipboard
                            navigator.clipboard.writeText(copyToken.textContent);
                        }
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
