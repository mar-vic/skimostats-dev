@extends('layouts.main', [
'selectedPage' => 'athletes',
'title' => $athlete->firstName . ' ' .$athlete->lastName,
'og_image' => $athlete->getImageUrl(),
'meta_keywords' => $athlete->firstName . ' ' .$athlete->lastName
.', ' . $athlete->lastName . ' ' .$athlete->firstName
.', ' . $athlete->lastName
.', skimo ' . $athlete->firstName . ' ' .$athlete->lastName
.', ski mountaineering ' . $athlete->firstName . ' ' .$athlete->lastName
.', skimo athlete'
.', ski mountaineering athlete',
'meta_description' => 'Skimo athlete ' . $athlete->firstName . ' ' .$athlete->lastName . ($athlete->country ? ' (' . $athlete->country->name . ')' : '') . ' on SkiMo Stats.'
])
@section('content')

<div class="athletes-top athletes-top--detail pt-0 pt-md-5 pb-5 overflow-hidden">

    <div class="container pt-4 pt-md-5 pb-5 position-relative">
        <div class="athlete__name-placeholder h1 text-uppercase font-weight-bold">
            {{$athlete->lastName}}<br>
            {{$athlete->firstName}}
        </div>
        <div class="row">
            <div class="col-md-5 text-center text-md-right">
                <img src="{{ $athlete->getImageUrl() }}" class="athlete__profile-image" alt="{{ $athlete->name }}">
            </div>
            <div class="col-md-7">
                <h1 class="font-weight-bold mb-0 font-size-xlarge">{{ $athlete->firstName }} <span class="text-uppercase">{{$athlete->lastName}}</span>
                    @if($athlete->country)
                    <a href="{{ route('athletes') }}?country={{$athlete->country->code}}">
                        <img src="{{ App\Country::getImagePath($athlete->country) }}" style="position:absolute;top:14px;outline:none;" class="d-none d-md-inline ml-2" />
                    </a>
                    @endif
                </h1>
                <div class="text-uppercase font-weight-bold">
                    Athlete ranking: @if($athlete->rank) <a href="{{ $athlete->rank->url }}">{{ $athlete->rank->rank }}</a> @else N/A @endif

                    |
                    Career Wins: <a href="#">{{ $athlete->getCareerWins()->count() }}</a>

                </div>

                <div class="athlete__social-links pt-2">
                    @if($athlete->hasSocialLink('strava'))
                    <a href="{{ $athlete->getSocialLink('strava') }}" target="_blank" title="Strava">
                        <i class="fab fa-strava" aria-hidden="true"></i>
                    </a>
                    @endif

                    @if($athlete->hasSocialLink('facebook'))
                    <a href="{{ $athlete->getSocialLink('facebook') }}" target="_blank" title="Facebook">
                        <i class="fab fa-facebook-f" aria-hidden="true"></i>
                    </a>
                    @endif

                    @if($athlete->hasSocialLink('instagram'))
                    <a href="{{ $athlete->getSocialLink('instagram') }}" target="_blank" title="Instagram">
                        <i class="fab fa-instagram" aria-hidden="true"></i>
                    </a>
                    @endif

                    @if($athlete->hasSocialLink('twitter'))
                    <a href="{{ $athlete->getSocialLink('twitter') }}" target="_blank" title="Twitter">
                        <i class="fab fa-twitter" aria-hidden="true"></i>
                    </a>
                    @endif

                    @if($athlete->hasSocialLink('web'))
                    <a href="{{ $athlete->getSocialLink('web') }}" target="_blank" title="Website">
                        <i class="fas fa-globe-europe" aria-hidden="true"></i>
                    </a>
                    @endif
                </div>

                <div class="horizontal-line my-4"></div>

                <div class="row">
                    <div class="col-6 col-md-4 mb-3">
                        <div>
                            <div>
                                Date of birth
                            </div>
                            <span class='text-uppercase font-weight-bold'>
                                @if ($athlete->dateOfBirth)
                                {{ $athlete->dateOfBirthFormatted() }} ({{ $athlete->age }})
                                @else
                                Not provided
                                @endif
                            </span>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 mb-3">
                        <div>
                            <div>
                                Nationality
                            </div>
                            <span class='text-uppercase font-weight-bold'>
                                @if ($athlete->country)
                                {{ $athlete->country->name }}
                                @else
                                Not provided
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="col-md-5 mb-3">
                        <div>
                            <div>
                                Place of birth
                            </div>
                            <span class='text-uppercase font-weight-bold'>
                                @if ($athlete->placeOfBirth)
                                {{ $athlete->placeOfBirth }}
                                @else
                                Not provided
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="col-6 col-md-4 mb-3">
                        <div>
                            <div>
                                Weight
                            </div>
                            <span class='text-uppercase font-weight-bold'>
                                @if ($athlete->weight)
                                {{ $athlete->weight }} kg
                                @else
                                Not provided
                                @endif
                            </span>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 mb-3">
                        <div>
                            <div>
                                Height
                            </div>
                            <span class='text-uppercase font-weight-bold'>
                                @if ($athlete->height)
                                {{ number_format($athlete->height/100, 2) }} m
                                @else
                                Not provided
                                @endif
                            </span>
                        </div>
                    </div>
                    <div class="col-6 col-md-5 mb-3">
                        <div>
                            <div>
                                Visits this week
                            </div>
                            <div class='text-uppercase font-weight-bold'>
                                {{ $athlete->weeklyVisits }}
                                <span class='fa @if ($athlete->weeklyVisitsRising) fa-caret-up text-success @else  fa-caret-down text-danger @endif' title='Last week visits: {{ $athlete->lastWeekVisits }}'></span>
                            </div>

                            {{-- <br>{{$athlete->getIsmfPoints()}} pts
                            <br>{{$athlete->getIsmfRank()}} place --}}
                        </div>
                    </div>

                </div>

                <div class="horizontal-line mb-4 mt-2"></div>

                @if (count($topResults) > 0)

                <!-- TOP RESULTS AND POINTS PER SPECIALTY ROW -->
                <div class="row">

                    <div class="col-md-6 mb-4 mb-md-0">
                        <h2 class="h1 text-uppercase font-weight-bold">Top results</h2>

                        @foreach ($allRankCounts as $raceName => $rankCounts)
                        <div class="row">
                            <!-- RACE NAME -->
                            <div class="col-auto font-size-xsmall" style="font-size:13px;">
                                <strong>{{ $raceName }}: </strong>
                            </div>

                            <!-- MEDALS -->
                            <div class="col font-size-xsmall px-1" style="font-size:13px;">
                                <!-- GOLDEN -->
                                @if (isset($rankCounts[1]))
                                {{ $rankCounts[1] }}<span style="padding-left:1px;padding-right:1px;font-size:14px;">×</span><i class="fa fa-medal" style="color:#9b870c;padding-right:4px;"></i>
                                @endif

                                <!-- SILVER -->
                                @if (isset($rankCounts[2]))
                                {{ $rankCounts[2] }}<span style="padding-left:1px;padding-right:1px;font-size:14px;">×</span><span class="fa fa-medal" style="color:#a9a9a9;padding-right:4px"></span>
                                @endif

                                <!-- BRONZE -->
                                @if (isset($rankCounts[3]))
                                {{ $rankCounts[3] }}<span style="padding-left:1px;padding-right:1px;font-size:14px;">×</span><span class="fa fa-medal" style="color:#a57164;padding-right:4px"></span>
                                @endif
                            </div>
                        </div>
                        @endforeach

                    </div>


                    <!-- POINTS PER SPECIALTY -->
                    @if(count($pointsPerSpecialty))
                    <div class="col-md-6">
                        <h2 class="h1 text-uppercase font-weight-bold">Points per specialty</h2>
                        @foreach($pointsPerSpecialty as $item)
                        <div class="d-flex align-items-center flex-wrap mb-2">
                            <div style="width:80px;" class="text-nowrap pr-2">
                                {{str_replace(" race", "", $item->raceTypeName)}}
                            </div>
                            <div style="height:4px;background:#000;" class="flex-grow-1">
                                <div style="height:4px;background:#fff;width:{{ ($item->maxPoints ? (($item->points/$item->maxPoints) * 100) : 0) }}%"></div>
                            </div>
                            <div style="width:40px;" class="pl-2">
                                {{ $item->points }}
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
                @endif
                <!-- END TOP RESULTS AND PPS -->

                @if (count($topResultsNew) > 0)
                @endif
                <!-- END NEW TOP RESULTS -->

            </div>
        </div>
    </div>
</div>
<div id="athlete-vm-container">


</div>

{{-- @if($athlete->hasSocialLink('instagram'))
    <div class="instafeed text-nowrap overflow-hidden" id="instafeed"></div>
@endif --}}

@endsection

@push('scripts')
<script src="{{mix('js/athlete.js')}}"></script>
<script>
    athleteVM.init({{$athlete->id}})
</script>
@endpush
