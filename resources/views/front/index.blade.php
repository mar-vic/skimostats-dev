@extends('layouts.main')
@section('content')

    <div id='landing-page-container'></div>

    <div class="container pb-5">
        @foreach($partners as $category)
            <h1 class="mb-3 mb-md-5 text-uppercase text-blue font-weight-bold">{{ $category->name }}</h1>

            <div class="row mb-3 align-items-center">
                @foreach($category->entries as $entry)
                    <div class="col-6 col-sm-6 col-md-3 col-lg-2 mb-4">
                        <a href="{{ $entry->url }}" target="_blank" title="{{ $entry->name }}">
                            <img src="{{ asset('uploads/'.$entry->image) }}" style="max-width:100%;" alt="">
                        </a>
                    </div>
                @endforeach
            </div>

        @endforeach

    </div>
@endsection

@push('scripts')
    <script src="{{ mix('js/landing.js')}}"></script>
    <script>
        window.landingPageVM.setData({!! json_encode($data) !!})
    </script>
@endpush
