<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', 'Admin\HomeController@welcome');

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;

use Illuminate\Http\Request;

Auth::routes(['register' => false]);

// Set locale
Route::get('language/{locale}', function ($locale) {
    app()->setLocale($locale);
    session()->put('locale', $locale);

    return redirect()->back();
});

// Exposing laravel localization assets to JS / Vue
Route::get('/js/lang.js', function () {
    $strings = Cache::rememberForever('lang.js', function () {
        $lang = config('app.locale');

        $files   = glob(resource_path('lang/' . $lang . '.json'));
        $strings = [];

        foreach ($files as $file) {
            $name           = basename($file, '.json');
            $strings[$name] = json_decode(file_get_contents($file));
        }

        return $strings;
    });

    // dd($strings);

    $lang = config('app.locale');
    // error_log('LANG: ' . $lang);

    $files   = glob(resource_path('lang/' . $lang . '.json'));
    $strings = json_decode((file_get_contents(($files[0]))));

    // foreach ($files as $file) {
    //     $name = basename($file, '.json');
    //     $strings[$name] =  json_decode(file_get_contents($file));
    // }
    // dd($strings);

    // $lang = config('app.locale');
    // error_log(($lang));

    // $files = glob(resource_path('lang/' . $lang . '.json'));
    // foreach ($files as $file) {
    //     error_log(basename($file, '.json'));
    // }

    // foreach ($strings as $string) {
    //     error_log($string);
    // }

    header('Content-Type: text/javascript');
    echo ('window.i18n = ' . json_encode($strings) . ';');
    exit();
})->name('assets.lang');

// Route::middleware('auth')->group(function() {
Route::get('/', 'FrontController@preview')->name('landing');
Route::get('/contributions', 'FrontController@contributions')->name('contributions');
Route::get('/partners', 'FrontController@partners')->name('partners');
Route::get('/sitemap.xml', 'SitemapController@sitemap')->name('sitemap');
Route::get('/search', 'SearchController@search')->name('search');
Route::get('/cookies', 'FrontController@cookies')->name('cookies');
Route::get('/privacy-policy', 'FrontController@privacyPolicy')->name('privacy-policy');

// Athletes
Route::get('/athletes', 'FrontController@athletes')->name('athletes');
Route::get('/athletes/{athlete}', 'FrontController@athleteDetailRedirect')->name('athletes.detail');
Route::get('/athlete/{slug}', 'FrontController@athleteDetail')->name('athletes.detail.slug');
Route::get('/v1/popular-athletes', 'AthleteController@getPopularAthletes');
Route::get('/v1/athlete/{athlete}', 'AthleteController@getAthlete');
Route::post('/v1/athlete/predict', 'AthleteController@athletePredictions');
Route::get('/v1/athlete/{athlete}/race-year-list', 'AthleteController@raceYearList');
// Route::get('/v1/athlete/{athlete}/races', 'AthleteController@races');
Route::get('/v1/athlete/{athlete}/races/{year}', 'AthleteController@races');
Route::get('/v1/athlete/{athlete}/ranking-per-season', 'AthleteController@rankingPerSeason');
Route::get('/v1/athlete/{athlete}/races-per-country', 'AthleteController@racesPerCountry');
// NEW STUFF BY MV
Route::get('/v1/athlete/{athlete}/career-wins', 'AthleteController@careerWins'); // Athlete's wins throughout their career
Route::get('/v1/athlete/{athlete}/top-results', 'AthleteController@topResults');  // Top results of an athlete
Route::get('/v1/athlete/{athlete}/race-days/{year}', 'AthleteController@raceDays');  // Days raced in a season
Route::get('/v1/athlete/{athlete}/elevation/{year}', 'AthleteController@elevation'); // Elevation overcome by an athlete during a season
Route::get('/v1/athlete/{athlete}/seasonSummary/{year}', 'AthleteController@seasonSummary'); // summary stats for a season <elevation, raceDays, points>
// Statistics
Route::get('/v1/statistics/years', 'StatisticsController@years');
Route::get('/v1/statistics/mostWins/{year?}', 'StatisticsController@mostWins');
Route::get('/v1/statistics/mostRaceDays/{year?}', 'StatisticsController@mostRaceDays');
Route::get('/v1/statistics/mostVerticalMeters/{year?}', 'StatisticsController@mostVerticalMeters');
Route::get('/v1/statistics/mostGrandeCourseWins/{year?}', 'StatisticsController@mostGrandeCourseWins');
Route::get('/v1/statistics/mostWorldCupWins/{year?}', 'StatisticsController@mostWorldCupWins');
Route::get('/v1/statistics/mostChocolates/{year?}', 'StatisticsController@mostChocolates');
Route::get('/v1/statistics/mostTopTens/{year?}', 'StatisticsController@mostTopTens');
Route::get('/v1/statistics/mostPointsPerRaceDay/{year?}', 'StatisticsController@mostPointsPerRaceDay');
Route::get('/v1/statistics/activeAthletes/{raceCat?}', 'StatisticsController@activeAthletes');
Route::get('/v1/statistics/pointsPerMonth/{year?}/{month?}', 'StatisticsController@pointsPerMonth');
Route::get('/v1/statistics/youngestAthletes/{raceCat?}', 'StatisticsController@youngestAthletes');
Route::get('/v1/statistics/oldestAthletes/{raceCat?}', 'StatisticsController@oldestAthletes');
Route::get('/v1/statistics/winsByCountries/{year?}', 'StatisticsController@mostWinsByCountries');
Route::get('/v1/statistics/winnersByCountries/{year?}', 'StatisticsController@mostWinnersByCountries');
Route::get('/v1/statistics/mostNationsRacedIn/{year?}', 'StatisticsController@mostNationsRacedIn');

// Rankings
Route::get('/rankings', 'RankingController@ranking')->name('rankings');
Route::get('/rankings/skimostats/all-time/{category}', 'RankingController@rankingAllTime')->name('rankings.all-time');
Route::get('/rankings/ismf/all-time/{category}', 'RankingController@rankingAllTimeISMF')->name('rankings.all-time-ismf');
Route::get('/rankings/youthwc/all-time/{category}', 'RankingController@rankingAllTimeYouthWc')->name('rankings.all-time-youthwc');
Route::get('/rankings/skimostats/all-time/type/{raceType}/{category}', 'RankingController@rankingRaceType')->name('rankings.type.category');
Route::get('/rankings/ismf/all-time/type/{raceType}/{category}', 'RankingController@rankingRaceTypeISMF')->name('rankings.type.category.ismf');
Route::get('/rankings/youthwc/all-time/type/{raceType}/{category}', 'RankingController@rankingRaceTypeYouthWc')->name('rankings.type.category.youthwc');
Route::get('/rankings/skimostats/{year}/type/{raceType}/{category}', 'RankingController@rankingRaceTypeYear')->name('rankings.type.year');
Route::get('/rankings/ismf/{year}/type/{raceType}/{category}', 'RankingController@rankingRaceTypeYearISMF')->name('rankings.type.year.ismf');
Route::get('/rankings/youthwc/{year}/type/{raceType}/{category}', 'RankingController@rankingRaceTypeYearYouthWc')->name('rankings.type.year.youthwc');
Route::get('/rankings/{rankingType}/{category}', 'RankingController@rankingCategorySlug')->name('rankings.category');
Route::get('/rankings/{rankingType}/{year}/{category}', 'RankingController@rankingYear')->name('rankings.year');
// End rankings

Route::get('/races', 'FrontController@races')->name('races');
Route::get('/races/{year}', 'FrontController@races')->name('races.year');
Route::get('/races/{year}/{month}', 'FrontController@races')->name('races.month');

// STATISTICS
// FOR ATHLETES FILTERED BY SEASON
Route::get('/statistics/', 'StatisticsController@victories')->name('statistics');
Route::get('/statistics/victories/{year?}', 'StatisticsController@victories')->name('statistics.victories')->where('year', '^(|all-seasons|\b(19|20)\d{2}\b)$');
Route::get('/statistics/race-days/{year?}', 'StatisticsController@raceDays')->name('statistics.raceDays')->where('year', '^(|all-seasons|\b(19|20)\d{2}\b)$');
Route::get('/statistics/points-per-raceday/{year?}', 'StatisticsController@pointsPerRaceday')->name('statistics.pointsPerRaceday')->where('year', '^(|all-seasons|\b(19|20)\d{2}\b)$');
Route::get('/statistics/grand-course-victories/{year?}', 'StatisticsController@grandCourseVictories')->name('statistics.grandCourseVictories')->where('year', '^(|all-seasons|\b(19|20)\d{2}\b)$');
Route::get('/statistics/world-cup-victories/{year?}', 'StatisticsController@worldCupVictories')->name('statistics.worldCupVictories')->where('year', '^(|all-seasons|\b(19|20)\d{2}\b)$');
Route::get('/statistics/chocolates/{year?}', 'StatisticsController@chocolates')->name('statistics.chocolates')->where('year', '^(|all-seasons|\b(19|20)\d{2}\b)$');
Route::get('/statistics/top-tens/{year?}', 'StatisticsController@topTens')->name('statistics.topTens')->where('year', '^(|all-seasons|\b(19|20)\d{2}\b)$');
Route::get('/statistics/countries-raced-in/{year?}', 'StatisticsController@countriesRacedIn')->name('statistics.countriesRacedIn')->where('year', '^(|all-seasons|\b(19|20)\d{2}\b)$');

// ATHLETES BY SEASON WITH HISTOGRAM
Route::get('/statistics/points-per-age/{year?}', 'StatisticsController@pointsPerAge')->name('statistics.pointsPerAge')->where('year', '^(\b(19|20)\d{2}\b)$');

// ATHLETES BY RANKING CATEGORY
Route::get('/statistics/active-athletes/{rankingCategory?}', 'StatisticsController@activeAthletes')->name('statistics.activeAthletes')->where('rankingCategory', '^(all|world-cup|grand-course)$');
Route::get('/statistics/youngest-athletes/{rankingCategory?}', 'StatisticsController@youngestAthletes')->name('statistics.youngestAthletes')->where('rankingCategory', '^(all|world-cup|grand-course)$');
Route::get('/statistics/oldest-athletes/{rankingCategory?}', 'StatisticsController@oldestAthletes')->name('statistics.oldestAthletes')->where('rankingCategory', '^(all|world-cup|grand-course)$');

// ATHLETES MONTHLY
Route::get('/statistics/points-per-month/{monthYear?}', 'StatisticsController@pointsPerMonth')->name('statistics.pointPerMonth')->where('monthYear', '\b(0[1-9]|1[0-2])-(19|20)\d{2}\b');

// COUNTRIES BY SEASON
Route::get('/statistics/wins-per-countries/{year?}', 'StatisticsController@winsPerCountries')->name('statistics.winsPerCountries')->where('year', '^(|all-seasons|\b(19|20)\d{2}\b)$');
Route::get('/statistics/winners-per-countries/{year?}', 'StatisticsController@winnersPerCountry')->name('statistics.winnersPerCountry')->where('year', '^(|all-seasons|\b(19|20)\d{2}\b)$');
Route::get('/statistics/points-per-country/{year?}', 'StatisticsController@pointsPerCountry')->name('statistics.pointsPerCountry')->where('year', '^(|all-seasons|\b(19|20)\d{2}\b)$');
// END STATISTICS

Route::get('/about-us', 'FrontController@aboutUs')->name('about-us');
Route::get('/get-instafeed', 'FrontController@getInstafeed')->name('get-instafeed');
Route::get('/race-event/{event}', 'FrontController@raceEventDetailRedirect')->name('raceEventDetail');
Route::get('/event/{slug}', 'FrontController@raceEventDetail')->name('raceEventDetail.slug');
Route::get('/event/{slug}/{category}', 'FrontController@raceEventDetail')->name('raceEventDetail.slug.category');
Route::get('/event/{slug}/{category}/gc', 'FrontController@raceEventDetail')->name('raceEventDetail.slug.category.gc');

Route::post('/rankings/homepage', 'RankingController@getHomepageRankings');
Route::post('/rankings/homepage/nations', 'RankingController@getHomepageRankingsNations');

Route::get('/world-cup', 'FrontController@worldCup')->name('world-cup');
Route::get('/world-cup/{year}', 'FrontController@worldCup')->name('world-cup.year');

Route::get('/race/{slug}', 'FrontController@raceOverview')->name('race-overview');
Route::get('/race/{slug}/{year}', 'FrontController@raceOverview')->name('race-overview.year');

// TESTING
Route::get('/test', 'FrontController@test')->name('test');
Route::get('/testlist', function () {
    return ['Laravel', 'Vue', 'PHP', 'Javascript', 'Tooling'];
});

// Testing issuing tokens with Sanctum
Route::post("/tokens/create", function (Request $request) {
    $token = $request->user()->createToken($request->token_name);

    return ["token" => $token->plainTextToken];
});

// Artisan
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/artisan/migrate', 'Admin\ArtisanController@migrate');
    Route::get('/artisan/dump-autoload', 'Admin\ArtisanController@dumpAutoload');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {

    Route::get('/', 'Admin\HomeController@index')->name('home');

    Route::get('/users', 'Admin\ApiUsageController@index')->name('users.list');
    Route::post('/users', 'Admin\ApiUsageController@create')->name('users.create');
    Route::delete("/users/{id}", "Admin\ApiUsageController@delete")->name("users.delete");

    $adminControllers = [
        'athletes' => 'Admin\AthleteController',
        'teams' => 'Admin\TeamController',
        'races' => 'Admin\RaceController',
        'ranking-categories' => 'Admin\RankingCategoryController',
        'partner-categories' => 'Admin\PartnerCategoryController',
    ];

    foreach ($adminControllers as $slug => $controller) {
        Route::prefix($slug)->name($slug . '.')->group(function () use ($controller) {
            Route::get('/', $controller . '@list')->name('list');
            Route::get('add', $controller . '@add')->name('add');
            Route::get('edit/{entry}', $controller . '@edit')->name('edit');

            Route::post('create', $controller . '@create')->name('create');
            Route::post('update/{entry}', $controller . '@update')->name('update');
            Route::get('delete/{entry}', $controller . '@delete')->name('delete');
        });
    }

    Route::post('partner-categories/add-entry/{entry}', 'Admin\PartnerCategoryController@createEntry')->name('partner-categories.add-entry');
    Route::post('partner-categories/save-entry-order/{entry}', 'Admin\PartnerCategoryController@saveEntryOrder')->name('partner-categories.save-entry-order');
    Route::get('partner-categories/delete-entry/{entry}/{index}', 'Admin\PartnerCategoryController@deleteEntry')->name('partner-categories.delete-entry');

    Route::get('athletes/{athlete}/races', 'Admin\AthleteController@athleteRaces')->name('athletes.races');
    Route::post('athletes/top-results', 'Admin\AthleteController@updateTopResults')->name('athletes.top-results.update');

    Route::get('categories', 'Admin\CategoryController@index')->name('categories.list');
    Route::post('categories', 'Admin\CategoryController@create')->name('categories.create');
    Route::get('categories/{category}', 'Admin\CategoryController@edit')->name('categories.edit');
    Route::post('categories/{category}', 'Admin\CategoryController@update')->name('categories.update');

    Route::get('race/{race}/events', 'Admin\RaceEventController@list')->name('race-events.list');
    Route::get('race-event/{raceEvent}/events', 'Admin\RaceEventController@listSubevents')->name('race-events.list-subevents');
    Route::get('race/{race}/events/add', 'Admin\RaceEventController@add')->name('race-events.add');
    Route::get('race-event/{raceEvent}/events/add', 'Admin\RaceEventController@addSubevent')->name('race-events.add-subevent');
    Route::get('race-event/{raceEvent}/events/edit/{entry}', 'Admin\RaceEventController@editSubevent')->name('race-events.edit-subevent');
    Route::get('race-event/edit/{entry}', 'Admin\RaceEventController@edit')->name('race-events.edit');

    Route::post('race/{race}/events/create', 'Admin\RaceEventController@create')->name('race-events.create');
    Route::post('race-event/update/{entry}', 'Admin\RaceEventController@update')->name('race-events.update');
    Route::get('race-event/delete/{entry}', 'Admin\RaceEventController@delete')->name('race-events.delete');

    Route::get('race-event/{entry}', 'Admin\RaceEventController@show')->name('race-events.show');
    Route::get('race-event/{entry}/stage/{stage}', 'Admin\RaceEventController@show')->name('race-events.showStage');
    Route::get('race-event-stage/{stage}/delete', 'Admin\RaceEventController@deleteStage')->name('race-events.deleteStage');
    Route::get('race-event/{entry}/add-stage', 'Admin\RaceEventController@newStage')->name('race-events.newStage');
    Route::post('race-event/{raceEvent}/import', 'Admin\RaceEventController@import')->name('race-events.import');
    Route::get('race-event/{raceEvent}/{category}/export-entries/csv', 'Admin\RaceEventController@exportEntriesToCsv')->name('race-events.export-entries.csv');
    Route::get('race-event/{entry}/{categoryId}/{stageId}/deleteAll', 'Admin\RaceEventController@deleteAllResults')->name('race-events.deleteAllResults');

    // Added by MV
    Route::get("race-event/{raceEvent}/generateGCResults", "Admin\RaceEventController@generateGCResults")->name("race-events.generateGCResults");

    Route::post('race-event/{event}/c/{category}/add', 'Admin\RaceEventEntryController@add')->name('race-events.add-entry');
    Route::get('race-event-entry/{entry}/delete', 'Admin\RaceEventEntryController@delete')->name('race-event-entry.delete');
    Route::post('race-event-entry/{entry}/update', 'Admin\RaceEventEntryController@updateSingle')->name('race-event-entry.update');
    Route::post('race-event-entry/{entry}/update-team', 'Admin\RaceEventEntryController@updateSingleTeam')->name('race-event-entry.update-team');
    Route::post('race-event-entry/add-team-entry', 'Admin\RaceEventEntryController@addSingleTeam')->name('race-event-entry.add-team');

    Route::get('countries', 'Admin\CountryController@list')->name('countries');

    Route::post('team/{team}/add-athlete', 'Admin\TeamController@addAthlete')->name('team.add-athlete');
    Route::get('team/remove-athlete/{teamAthlete}', 'Admin\TeamController@removeAthlete')->name('team.remove-athlete');

    Route::get('rankings/refresh/ismf', 'Admin\RankingController@refreshAllIsmfRankings')->name('rankings.refresh.ismf');
    Route::get('rankings/refresh/youthwc', 'Admin\RankingController@refreshAllIsmfYouthWcRankings')->name('rankings.refresh.youthwc');
    Route::get('rankings/refresh/skimostats', 'Admin\RankingController@refreshAllSkimostatsRankings')->name('rankings.refresh.skimostats');

    Route::get('rankings', 'Admin\RankingController@index')->name('rankings.index');

    Route::get('rankings/table/update', 'Admin\RankingController@updateRankingTable')->name('rankings.table.update');
    Route::get('rankings/table/update/{type}', 'Admin\RankingController@updateRankingTable')->name('rankings.table.update.type');
    Route::get('rankings/table/update/{type}/{year}', 'Admin\RankingController@updateRankingTable')->name('rankings.table.update.year');
});
