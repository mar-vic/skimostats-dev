
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <div>
                        <a href='{{ route('admin.races.list') }}'>Races</a> &raquo;
                        <a href='{{ route('admin.races.edit', $race) }}'>{{ $race->name }}</a> &raquo; Race types
                    </div>
                    <a href='{{ route('admin.' . $slug . '.add', $race->getKey()) }}' class='ml-auto btn btn-sm btn-success'>+ Add</a>
                </div>

                <div class="card-body">
                    @include('layouts.success')

                    <form method='GET'>
                        <div class='input-group'>
                            <input type='text' name='filter' value='{{ request('filter') }}' placeholder="Search..." class='form-control' />
                            <div class="input-group-append">
                                <input type='submit' value='Filter' class='btn btn-outline-primary' />
                            </div>
                        </div>
                    </form>

                    <table class='table table-striped'>
                        <thead>
                            <tr>
                                <th>
                                    Name
                                </th>
                                <th style='width: 250px;text-align:right'>
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($entries as $entry)
                                <tr>
                                    <td>
                                        <a href='{{ route('admin.race-category.list', $entry) }}'>{{ $entry->name }}</a>

                                        @if($entry->hasStages())
                                            <div class='badge badge-warning'>Has stages</div>
                                        @endif
                                        @if($entry->isTeamType())
                                            <div class='badge badge-secondary'>Team type</div>
                                        @endif
                                    </td>
                                    <td style='text-align: right;'>
                                        <a href='{{ route('admin.race-category.list', $entry) }}' class='btn btn-sm btn-primary'>Race categories</a>
                                        <a href='{{ route('admin.' . $slug . '.edit', $entry) }}' class='btn btn-sm btn-primary'>Edit</a>
                                        <a href='{{ route('admin.' . $slug . '.delete', $entry) }}'
                                            class='btn btn-sm btn-danger'
                                            onclick="return confirm('Really delete {{ $entry->name }}?')">Delete</a>
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
