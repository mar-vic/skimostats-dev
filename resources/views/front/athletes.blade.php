@extends('layouts.main', ['selectedPage' => 'athletes', 'title' => 'Athletes' ])
@section('content')

<div class="athletes-top">
    <div class="container pt-5 pb-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="mb-3">
                    <div class="float-right text-center">
                        <div class="h1 text-uppercase page-heading mb-0 font-weight-bold">
                            {{ str_pad($actualPage, 2, "0", STR_PAD_LEFT) }}/{{ str_pad($pageCount, 2, "0", STR_PAD_LEFT) }}
                        </div>
                            <div class="font-size-xsmall">{{__('displaying page of')}}</div>
                    </div>
                    <div class="text-uppercase font-weight-bold">
                        <h1 class=" page-heading mb-0 font-weight-bold">
                            {{__('Athletes')}} ({{ $athleteCount }})
                        </h1>
                                <div>{{__('Filter by country')}}</div>
                    </div>
                </div>

                <div class="horizontal-line mb-3"></div>

                <div class="row">
                    @foreach($countries as $country)
                    <div class="col-6 col-md-3 mb-2">
                        <a href="?country={{ $country->code }}" class="d-inline-flex align-items-center {{ $selectedCountry == $country->code ? 'font-weight-bold' : ''}}">
                            <img
                                src="{{ asset('images/flags/flags-mini/'.strtolower($country->code).'.png') }}"
                                alt="{{ $country->code }}"
                                class="d-block mt-1px flag-icon--mini mr-2">

                            {{ $country->name }} ({{ $country->athleteCount }})
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<div class="athlete-list" id="athlete-list">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10 py-5">
                <div class="pt-5 mb-4">
                    <div class="d-flex align-items-center justify-content-between">
                        {{-- <div>
                            <span class='font-weight-bold text-uppercase mr-3'>Display order</span>
                            <a href="javascript:;" class="badge badge--custom badge-active text-uppercase">Alphabetical</a>
                        </div> --}}
                        <div>
                            {{-- TODO: Remove later --}}
                            {{--<span class='font-weight-bold text-uppercase mr-3'>Filter</span>--}}
                            {{--<a @if(Request::get('find')=="Lausanne 2020") href="?#athlete-list" @else href="?find=Lausanne+2020#athlete-list" @endif class="badge badge--custom @if(Request::get('find')=="Lausanne 2020") badge-active @endif text-uppercase">Lausanne 2020</a>--}}
                        </div>
                    </div>
                </div>

                <form action="#athlete-list" method="GET">
                    <div class="input-group mb-4">
                                           <input type="text" name="find" class="form-control" value="{{Request::get('find')}}" placeholder="{{__('Search athletes ...')}}" />
                        <div class="input-group-append">
                                           <button class="btn btn-primary" type="submit">{{__('Search')}}</button>
                        </div>
                    </div>
                </form>
                <div class="latest-results__race-results justify-content-between d-flex flex-wrap">
                    @foreach($entries as $entry)
                        <div class="latest-results__race-category d-flex">
                            <a href="{{ route('athletes.detail.slug', $entry->slug) }}" class="latest-results__first flex-grow-1 mb-4">
                                <div class="latest-results__photo position-relative"
                                    style="background-image:url({{ App\Athlete::getAthleteImageUrl($entry->image, $entry->gender) }});">
                                </div>
                                <div class="latest-results__info">
                                    <div class="font-weight-bold">{{ $entry->name }}</div>
                                    {{-- <div class="small mb-2">{{ firstEntry.timeFormatted }}</div> --}}
                                    <div class="d-flex justify-content-between align-items-center">
                                        @if ($entry->countryCode)
                                        <img
                                            class="latest-results__mini-flag"
                                            src="/images/flags/flags-mini/{{ strtolower($entry->countryCode) }}.png"
                                            alt="{{ $entry->countryName  }}"
                                            />
                                        @endif
                                           <div class="latest-results__view-profile">{{__('View profile')}}</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>


                <div class="d-flex justify-content-center align-items-center flex-wrap text-center pt-5">
                    @if ($pageCount < 10)
                        @for($i = 1; $i <= $pageCount; $i++)
                            <a href="?find={{Request::get('find')}}&amp;country={{ $selectedCountry }}&amp;page={{$i}}#athlete-list" class="pagination__link {{ $i == $actualPage ? 'pagination__link--active' : '' }}">{{$i}}</a>
                        @endfor
                    @elseif ($actualPage > 3 && $actualPage < $pageCount - 2)
                        <a href="?find={{Request::get('find')}}&amp;country={{ $selectedCountry }}&amp;page=1#athlete-list" class="pagination__link">1</a>
                        @if($actualPage - 3 != 1)
                            <div>&bull; &bull; &bull;</div>
                        @endif
                        @for($i = $actualPage - 2; $i <= $actualPage + 2; $i++)
                            <a href="?find={{Request::get('find')}}&amp;country={{ $selectedCountry }}&amp;page={{$i}}#athlete-list" class="pagination__link {{ $i == $actualPage ? 'pagination__link--active' : '' }}">{{$i}}</a>
                        @endfor
                        @if($actualPage + 3 != $pageCount)
                            <div>&bull; &bull; &bull;</div>
                        @endif
                        <a href="?find={{Request::get('find')}}&amp;country={{ $selectedCountry }}&amp;page={{$pageCount}}#athlete-list" class="pagination__link">{{$pageCount}}</a>
                    @else
                        @for($i = 1; $i <= 4; $i++)
                            <a href="?find={{Request::get('find')}}&amp;country={{ $selectedCountry }}&amp;page={{$i}}#athlete-list" class="pagination__link {{ $i == $actualPage ? 'pagination__link--active' : '' }}">{{$i}}</a>
                        @endfor
                        <div>&bull; &bull; &bull;</div>
                        @for($i = $pageCount - 3; $i <= $pageCount; $i++)
                            <a href="?find={{Request::get('find')}}&amp;country={{ $selectedCountry }}&amp;page={{$i}}#athlete-list" class="pagination__link {{ $i == $actualPage ? 'pagination__link--active' : '' }}">{{$i}}</a>
                        @endfor
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
