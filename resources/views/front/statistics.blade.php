@extends('layouts.main', [
    'selectedPage' => 'statistics',
    'title' => 'Statistics',
    'meta_keywords' => 'skimo statistics, skimo stats, skimo results, skimountaineering statistics, skimo results, pierra menta results, ismf world cup, ismf world cup results, skimo countries, skimo racers, popular athletes, oldest skimo athlete, youngest skimo athlete, tallest skimo athlete, heaviest skimo athlete, lightest skimo athlete'
])
@section('content')
    <div class="container">
        <h1 class="page-heading font-weight-bold text-uppercase text-blue py-5">Statistics</h1>
        <div class="row mb-5">
            <div class="col-md-6 col-lg-4 col-xl-3">
                <h2 class="font-weight-bold mb-3">Racers per country</h2>
                <div class="table--shrinked">
                    <a href="#" class="table__unshrink">Show more...</a>
                    <div class="table-responsive">
                        <table class="table table--races table--races-striped text-uppercase">
                            <thead>
                                <tr>
                                    <th>Country</th>
                                    <th class="text-right">Racers</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($racersPerCountry as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="/images/flags/flags-mini/{{ strtolower($item->countryCode) }}.png" class="flag-icon--micro mr-1" alt="{{ $item->countryCode }}" />
                                            <div class="font-weight-bold">
                                                <a href="{{ route('athletes') }}?country={{$item->countryCode}}">
                                                    {{ $item->countryName }}
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-right">{{ $item->athleteCount }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 col-xl-3">
                <h2 class="font-weight-bold mb-3">Racers per age</h2>
                <div class="table--shrinked">
                    <a href="#" class="table__unshrink">Show more...</a>
                    <div class="table-responsive">
                        <table class="table table--races table--races-striped text-uppercase">
                            <thead>
                                <tr>
                                    <th>Age</th>
                                    <th class="text-right">Racers</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($racersPerAge as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($item->age)
                                            <div class="font-weight-bold">{{ $item->age }} y. o.</div>
                                            @else
                                                N/A
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-right">{{ $item->athleteCount }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4 col-xl-3">
                <h2 class="font-weight-bold mb-3">Oldest athletes</h2>
                <div class="table--shrinked">
                    <a href="#" class="table__unshrink">Show more...</a>
                    <div class="table-responsive">
                        <table class="table table--races table--races-striped">
                            <thead>
                                <tr>
                                    <th class="text-uppercase">Athlete</th>
                                    <th class="text-right text-uppercase">Age</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($oldestAthletes as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="/images/flags/flags-mini/{{ strtolower($item->country->code) }}.png" class="flag-icon--micro mr-1" alt="{{ $item->country->code }}" />
                                            <div class="font-weight-bold">
                                                <a href="{{ route('athletes.detail.slug', $item->slug) }}">
                                                    {{ $item->firstName }}
                                                    <span class="text-uppercase">{{ $item->lastName }}</span>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-right">{{ $item->age }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 col-xl-3">
                <h2 class="font-weight-bold mb-3">Youngest athletes</h2>
                <div class="table--shrinked">
                    <a href="#" class="table__unshrink">Show more...</a>
                    <div class="table-responsive">
                        <table class="table table--races table--races-striped">
                            <thead>
                                <tr>
                                    <th class="text-uppercase">Athlete</th>
                                    <th class="text-right text-uppercase">Age</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($youngestAthletes as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="/images/flags/flags-mini/{{ strtolower($item->country->code) }}.png" class="flag-icon--micro mr-1" alt="{{ $item->country->code }}" />
                                            <div class="font-weight-bold">
                                                <a href="{{ route('athletes.detail.slug', $item->slug) }}">
                                                    {{ $item->firstName }}
                                                    <span class="text-uppercase">{{ $item->lastName }}</span>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-right">{{ $item->age }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-5">
            <div class="col-md-12 col-lg-6 col-xl-6">
                <h2 class="font-weight-bold mb-3">Popular athletes</h2>
                <div class="table--shrinked">
                    <a href="#" class="table__unshrink">Show more...</a>
                    <div class="table-responsive">
                        <table class="table table--races table--races-striped">
                            <thead>
                                <tr>
                                    <th class="text-uppercase">Athlete</th>
                                    <th class="text-right text-uppercase">Clicks</th>
                                    {{-- <th class="text-right text-uppercase">Visits last week</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($mostPopular as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($item->country)
                                                <img src="/images/flags/flags-mini/{{ strtolower($item->country->code) }}.png" class="flag-icon--micro mr-1" alt="{{ $item->country->name }}" />
                                            @endif
                                            <div class="font-weight-bold">
                                                <a href="{{ route('athletes.detail.slug', $item->slug) }}">
                                                    {{ $item->firstName }}
                                                    <span class="text-uppercase">{{ $item->lastName }}</span>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-right">{{ $item->clicks ?? $item->visits }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 col-xl-3">
                <h2 class="font-weight-bold mb-3">Tallest athletes</h2>
                <div class="table--shrinked">
                    <a href="#" class="table__unshrink">Show more...</a>
                    <div class="table-responsive">
                        <table class="table table--races table--races-striped">
                            <thead>
                                <tr>
                                    <th class="text-uppercase">Athlete</th>
                                    <th class="text-right text-uppercase">Height</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tallestAthletes as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="/images/flags/flags-mini/{{ strtolower($item->country->code) }}.png" class="flag-icon--micro mr-1" alt="{{ $item->country->code }}" />
                                            <div class="font-weight-bold">
                                                <a href="{{ route('athletes.detail.slug', $item->slug) }}">
                                                    {{ $item->firstName }}
                                                    <span class="text-uppercase">{{ $item->lastName }}</span>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-right">{{ $item->height }}cm</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 col-xl-3">
                <h2 class="font-weight-bold mb-3">Shortest athletes</h2>
                <div class="table--shrinked">
                    <a href="#" class="table__unshrink">Show more...</a>
                    <div class="table-responsive">
                        <table class="table table--races table--races-striped">
                            <thead>
                                <tr>
                                    <th class="text-uppercase">Athlete</th>
                                    <th class="text-right text-uppercase">Height</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($shortestAthletes as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="/images/flags/flags-mini/{{ strtolower($item->country->code) }}.png" class="flag-icon--micro mr-1" alt="{{ $item->country->code }}" />
                                            <div class="font-weight-bold">
                                                <a href="{{ route('athletes.detail.slug', $item->slug) }}">
                                                    {{ $item->firstName }}
                                                    <span class="text-uppercase">{{ $item->lastName }}</span>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-right">{{ $item->height }}cm</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 col-xl-3">
                <h2 class="font-weight-bold mb-3">Heaviest athletes</h2>
                <div class="table--shrinked">
                    <a href="#" class="table__unshrink">Show more...</a>
                    <div class="table-responsive">
                        <table class="table table--races table--races-striped">
                            <thead>
                                <tr>
                                    <th class="text-uppercase">Athlete</th>
                                    <th class="text-right text-uppercase">weight</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($heaviestAthletes as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="/images/flags/flags-mini/{{ strtolower($item->country->code) }}.png" class="flag-icon--micro mr-1" alt="{{ $item->country->code }}" />
                                            <div class="font-weight-bold">
                                                <a href="{{ route('athletes.detail.slug', $item->slug) }}">
                                                    {{ $item->firstName }}
                                                    <span class="text-uppercase">{{ $item->lastName }}</span>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-right">{{ $item->weight }}kg</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 col-xl-3">
                <h2 class="font-weight-bold mb-3">Lightest athletes</h2>
                <div class="table--shrinked">
                    <a href="#" class="table__unshrink">Show more...</a>
                    <div class="table-responsive">
                        <table class="table table--races table--races-striped">
                            <thead>
                                <tr>
                                    <th class="text-uppercase">Athlete</th>
                                    <th class="text-right text-uppercase">Weight</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lightestAthletes as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="/images/flags/flags-mini/{{ strtolower($item->country->code) }}.png" class="flag-icon--micro mr-1" alt="{{ $item->country->code }}" />
                                            <div class="font-weight-bold">
                                                <a href="{{ route('athletes.detail.slug', $item->slug) }}">
                                                    {{ $item->firstName }}
                                                    <span class="text-uppercase">{{ $item->lastName }}</span>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-right">{{ $item->weight }}kg</td>
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
