@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card mb-5">
                <div class="card-header d-flex align-items-center">
                    <div>
                        <a href='{{ route('admin.'.$slug.'.list') }}'>Partner categories</a>
                        &raquo;
                        @if($edit)
                            Edit
                        @else
                            Add
                        @endif
                    </div>
                </div>

                <div class="card-body">
                <form method='POST' enctype="multipart/form-data" action='{{ route('admin.' . $slug . '.' . ($edit ? 'update' : 'create'), $edit ? [$entry] : []) }}'>

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
                                <td style='width: 250px'>Position:</td>
                                <td>
                                    <input type='text' name='position' value='{{ old('position', $entry->position ?? 0) }}' class='form-control' />
                                </td>
                            </tr>

                        </tbody>
                    </table>



                    @csrf
                    <button type='submit' class='btn btn-primary'>Save</button>
                </form>
                </div>
            </div>

            @if($edit)
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center">
                    <div>
                        Entries
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('admin.partner-categories.add-entry', $entry->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td>Title</td>
                                    <td><input type="text" name="entryName" class="form-control" value='{{ old('entryName') }}'></td>
                                </tr>
                                <tr>
                                    <td>Link</td>
                                    <td><input type="text" name="url" placeholder="https://" value='{{ old('url') }}' class="form-control"></td>
                                </tr>
                                <tr>
                                    <td>Image</td>
                                    <td>
                                        <input type="file" class="form-control" name="image">
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <button type="submit" class="btn btn-success">Add entry</button>
                    </form>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.partner-categories.save-entry-order', [$entry->id]) }}">
                @csrf
                <div class="row" id="sortable">
                    @foreach($entry->entries as $index=>$partner)
                    @if($partner)
                    <div class="col-md-4 mb-4">
                        <div class="card  h-100">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <div>
                                    {{ $partner->name }}
                                    <input type="hidden" name="order[]" value="{{ $index }}">
                                </div>
                                <div>
                                    <a href="{{ route('admin.partner-categories.delete-entry', [$entry->id, $index]) }}" class="btn btn-sm btn-danger">&times</a>
                                </div>
                            </div>

                            <div class="card-body">
                                <img src="{{ asset("uploads/" . $partner->image) }}" style="max-width:100%" class="d-block mb-2" alt="">
                                <div class="overflow-hidden text-ellipsis">
                                    <b>Url:</b> <i>{{ $partner->url }}</i>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    @endforeach

                </div>
                <button type="submit" class="btn btn-success">Save order</button>
            </form>

            @endif
<br>

<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
    $(function() {
        $( "#sortable" ).sortable();
        $( "#sortable" ).disableSelection();
    } );
</script>
@endpush
