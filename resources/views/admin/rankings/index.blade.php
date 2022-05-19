
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    Rankings

                    <a href='{{ route('admin.ranking-categories.list') }}' class='ml-auto btn btn-sm btn-primary'>Categories</a>
                </div>

                <div class="card-body">
                    <div id="rankings-manager-app"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ mix('js/admin/rankings-manager.js') }}"></script>
@endpush
