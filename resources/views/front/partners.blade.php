@extends('layouts.main', ['selectedPage' => 'partners', 'title' => 'Partners'])
@section('content')

<div class="container py-5">
    @foreach($partnerCategories as $category)
    <h1 class="mb-3 mb-md-5 text-uppercase text-blue font-weight-bold">{{ $category->name }}</h1>

    <div class="row mb-3 align-items-center">
        @foreach($category->entries as $entry)
        <div class="col-8 col-md-4 col-lg-3 mb-4">
            <a href="{{ $entry->url }}" target="_blank" title="{{ $entry->name }}">
                <img src="{{ asset('uploads/'.$entry->image) }}" style="max-width:100%;" alt="">
            </a>
        </div>
        @endforeach
    </div>

    @endforeach

</div>

@endsection
