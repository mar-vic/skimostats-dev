<div class="d-none d-md-block container position-relative pt-5">
    <div class="d-none d-md-block bg-text">{{__('Statistics')}}</div>
    <h1 class="d-none d-md-block page-heading font-weight-bold text-uppercase pt-0 pt-md-5 text-blue mb-4">{{ str_replace('-', ' ', $statsCategorySlug ) }}</h1>
    <div class="d-none d-md-block font-weight-bold text-blue">{{__('Select your category')}}:</div>
    <div class="d-none d-md-flex row stats-menu mb-5 pb-2 pt-2" style="border-bottom:2px dotted; border-top:2px dotted;">

        <!-- FIRST MENU COLUMN -->
        <div class="col mt-2 mb-2">
            <ul class="navbar-nav">

                <li {!! $statsCategorySlug=='victories' ? 'class=" nav-item selected"' : 'class="nav-item"' !!}>
                    <a class="nav-link pl-3" href=" /statistics/victories">Victories</a>
                </li>

                <li {!! $statsCategorySlug=='race-days' ? 'class=" nav-item selected"' : 'class="nav-item"' !!}>
                    <a class="nav-link pl-3" href="/statistics/race-days">Race days</a>
                </li>

                <li {!! $statsCategorySlug=='points-per-month' ? 'class=" nav-item selected"' : 'class="nav-item"' !!}>
                    <a class="nav-link pl-3" href="/statistics/points-per-month">Points per month</a>
                </li>

                <li {!! $statsCategorySlug=='points-per-age' ? 'class=" nav-item selected"' : 'class="nav-item"' !!}>
                    <a class="nav-link pl-3" href="/statistics/points-per-age">Points per age</a>
                </li>

                <li {!! $statsCategorySlug=='grand-course-victories' ? 'class=" nav-item selected"' : 'class="nav-item"' !!}>
                    <a class="nav-link pl-3" href="/statistics/grand-course-victories">Grand Course victories</a>
                </li>

            </ul>
        </div>

        <!-- SECOND MENU COLUMN -->
        <div class="col mt-2 mb-2">
            <ul class="navbar-nav">
                <li {!! $statsCategorySlug=='world-cup-victories' ? 'class=" nav-item selected"' : 'class="nav-item"' !!}>
                    <a class="nav-link pl-3" href="/statistics/world-cup-victories">World Cup victories</a>
                </li>
                <li {!! $statsCategorySlug=='countries-by-wins' ? 'class=" nav-item selected"' : 'class="nav-item"' !!}>
                    <a class="nav-link pl-3" href="/statistics/countries-by-wins">Countries by wins</a>
                </li>
                <li {!! $statsCategorySlug=='chocolates' ? 'class=" nav-item selected"' : 'class="nav-item"' !!}>
                    <a class="nav-link pl-3" href="/statistics/chocolates">Chocolates</a>
                </li>
                <li {!! $statsCategorySlug=='top-tens' ? 'class=" nav-item selected"' : 'class="nav-item"' !!}>
                    <a class="nav-link pl-3" href="/statistics/top-tens">Top 10 finishes</a>
                </li>
                <li {!! $statsCategorySlug=='countries-skimo-scores' ? 'class=" nav-item selected"' : 'class="nav-item"' !!}>
                    <a class="nav-link pl-3" href="/statistics/countries-skimo-scores">Countries SKIMO scores</a>
                </li>
            </ul>
        </div>

        <!-- THIRD MENU COLUMN -->
        <div class="col mt-2 mb-2">
            <ul class="navbar-nav">
                <li {!! $statsCategorySlug=='active-athletes' ? 'class=" nav-item selected"' : 'class="nav-item"' !!}>
                    <a class="nav-link pl-3" href="/statistics/active-athletes">Active athletes</a>
                </li>
                <li {!! $statsCategorySlug=='countries-by-winners' ? 'class=" nav-item selected"' : 'class="nav-item"' !!}>
                    <a class="nav-link pl-3" href="/statistics/countries-by-winners">Countries by winners</a>
                </li>
                <li {!! $statsCategorySlug=='countries-raced-in' ? 'class=" nav-item selected"' : 'class="nav-item"' !!}>
                    <a class="nav-link pl-3" href="/statistics/countries-raced-in">Countries raced in</a>
                </li>
                <li {!! $statsCategorySlug=='youngest-athletes' ? 'class=" nav-item selected"' : 'class="nav-item"' !!}>
                    <a class="nav-link pl-3" href="/statistics/youngest-athletes">Youngest athletes</a>
                </li>
                <li {!! $statsCategorySlug=='oldest-athletes' ? 'class=" nav-item selected"' : 'class="nav-item"' !!}>
                    <a class="nav-link pl-3" href="/statistics/oldest-athletes">Oldest athletes</a>
                </li>
            </ul>
        </div>

        <!-- FOURTH MENU COLUMN -->
        <div class="col mt-2 mb-2">
            <ul class="navbar-nav">
                <li {!! $statsCategorySlug=='points-per-raceday' ? 'class=" nav-item selected"' : 'class="nav-item"' !!}>
                    <a class="nav-link pl-3" href="/statistics/points-per-raceday">Points per raceday</a>
                </li>
            </ul>
        </div>
    </div>

</div>

<nav class="stats-menu-mob d-block d-md-none container navbar navbar-expand-lg navbar-light mb-5 mt-3">
    <div class="container-fluid">
        <div class="d-none d-md-block bg-text">{{__('Statistics')}}</div>
        <h1 class="font-weight-bold fs-2 text-uppercase pt-0 pt-md-5 text-blue mb-0">{{ str_replace('-', ' ', $statsCategorySlug ) }}</h1>
        <button id="statsnav-toggler" class="navbar-toggler pl-0 pr-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">

            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 mt-3">
                <li {!! $statsCategorySlug=='victories' ? 'class=" nav-item selected"' : 'class="nav-item"' !!}>
                    <a class="nav-link pl-3" href=" /statistics/victories">Victories</a>
                </li>

                <li {!! $statsCategorySlug=='race-days' ? 'class=" nav-item selected"' : 'class="nav-item"' !!}>
                    <a class="nav-link pl-3" href="/statistics/race-days">Race days</a>
                </li>

                <li {!! $statsCategorySlug=='points-per-month' ? 'class=" nav-item selected"' : 'class="nav-item"' !!}>
                    <a class="nav-link pl-3" href="/statistics/points-per-month">Points per month</a>
                </li>

                <li {!! $statsCategorySlug=='points-per-age' ? 'class=" nav-item selected"' : 'class="nav-item"' !!}>
                    <a class="nav-link pl-3" href="/statistics/points-per-age">Points per age</a>
                </li>

                <li {!! $statsCategorySlug=='grand-course-victories' ? 'class=" nav-item selected"' : 'class="nav-item"' !!}>
                    <a class="nav-link pl-3" href="/statistics/grand-course-victories">Grand Course victories</a>
                </li>

                <li {!! $statsCategorySlug=='world-cup-victories' ? 'class=" nav-item selected"' : 'class="nav-item"' !!}>
                    <a class="nav-link pl-3" href="/statistics/world-cup-victories">World Cup victories</a>
                </li>
                <li {!! $statsCategorySlug=='countries-by-wins' ? 'class=" nav-item selected"' : 'class="nav-item"' !!}>
                    <a class="nav-link pl-3" href="/statistics/countries-by-wins">Countries by wins</a>
                </li>
                <li {!! $statsCategorySlug=='chocolates' ? 'class=" nav-item selected"' : 'class="nav-item"' !!}>
                    <a class="nav-link pl-3" href="/statistics/chocolates">Chocolates</a>
                </li>
                <li {!! $statsCategorySlug=='top-tens' ? 'class=" nav-item selected"' : 'class="nav-item"' !!}>
                    <a class="nav-link pl-3" href="/statistics/top-tens">Top 10 finishes</a>
                </li>
                <li {!! $statsCategorySlug=='countries-skimo-scores' ? 'class=" nav-item selected"' : 'class="nav-item"' !!}>
                    <a class="nav-link pl-3" href="/statistics/countries-skimo-scores">Countries SKIMO scores</a>
                </li>

                <li {!! $statsCategorySlug=='world-cup-victories' ? 'class=" nav-item selected"' : 'class="nav-item"' !!}>
                    <a class="nav-link pl-3" href="/statistics/world-cup-victories">World Cup victories</a>
                </li>
                <li {!! $statsCategorySlug=='wins-by-countries' ? 'class=" nav-item selected"' : 'class="nav-item"' !!}>
                    <a class="nav-link pl-3" href="/statistics/wins-by-countries">Wins by countries</a>
                </li>
                <li {!! $statsCategorySlug=='chocolates' ? 'class=" nav-item selected"' : 'class="nav-item"' !!}>
                    <a class="nav-link pl-3" href="/statistics/chocolates">Chocolates</a>
                </li>
                <li {!! $statsCategorySlug=='top-tens' ? 'class=" nav-item selected"' : 'class="nav-item"' !!}>
                    <a class="nav-link pl-3" href="/statistics/top-tens">Top 10 finishes</a>
                </li>
                <li {!! $statsCategorySlug=='nations-skimo-scores' ? 'class=" nav-item selected"' : 'class="nav-item"' !!}>
                    <a class="nav-link pl-3" href="/statistics/nations-skimo-scores">Nations SKIMO scores</a>
                </li>

                <li {!! $statsCategorySlug=='points-per-raceday' ? 'class=" nav-item selected"' : 'class="nav-item"' !!}>
                    <a class="nav-link pl-3" href="/statistics/points-per-raceday">Points per raceday</a>
                </li>

            </ul>
        </div>
    </div>
</nav>
