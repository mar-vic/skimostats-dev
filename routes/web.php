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

use App\Livewire\HelloWorld;
use App\Livewire\Todos;
use App\Livewire\Counter;
use App\Livewire\CreatePost;
use App\Livewire\CreateRaceEvent;
use App\Livewire\Posts;
use App\Livewire\Races;
use App\Livewire\SprintKnockoutsAdmin;

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
Route::get('/', 'App\Http\Controllers\FrontController@preview')->name('landing');
Route::get('/contributions', 'App\Http\Controllers\FrontController@contributions')->name('contributions');
Route::get('/partners', 'App\Http\Controllers\FrontController@partners')->name('partners');
Route::get('/sitemap.xml', 'App\Http\Controllers\SitemapController@sitemap')->name('sitemap');
Route::get('/search', 'App\Http\Controllers\SearchController@search')->name('search');
Route::get('/cookies', 'App\Http\Controllers\FrontController@cookies')->name('cookies');
Route::get('/privacy-policy', 'App\Http\Controllers\FrontController@privacyPolicy')->name('privacy-policy');

// Athletes
Route::get('/athletes', 'App\Http\Controllers\FrontController@athletes')->name('athletes');
Route::get('/athletes/{athlete}', 'App\Http\Controllers\FrontController@athleteDetailRedirect')->name('athletes.detail');
Route::get('/athlete/{slug}', 'App\Http\Controllers\FrontController@athleteDetail')->name('athletes.detail.slug');
Route::get('/v1/popular-athletes', 'App\Http\Controllers\AthleteController@getPopularAthletes');
Route::get('/v1/athlete/{athlete}', 'App\Http\Controllers\AthleteController@getAthlete');
Route::post('/v1/athlete/predict', 'App\Http\Controllers\AthleteController@athletePredictions');
Route::get('/v1/athlete/{athlete}/race-year-list', 'App\Http\Controllers\AthleteController@raceYearList');
// Route::get('/v1/athlete/{athlete}/races', 'AthleteController@races');
Route::get('/v1/athlete/{athlete}/races/{year}', 'App\Http\Controllers\AthleteController@races');
Route::get('/v1/athlete/{athlete}/ranking-per-season', 'App\Http\Controllers\AthleteController@rankingPerSeason');
Route::get('/v1/athlete/{athlete}/races-per-country', 'App\Http\Controllers\AthleteController@racesPerCountry');
// NEW STUFF BY MV
Route::get('/v1/athlete/{athlete}/career-wins', 'App\Http\Controllers\AthleteController@careerWins'); // Athlete's wins throughout their career
Route::get('/v1/athlete/{athlete}/top-results', 'App\Http\Controllers\AthleteController@topResults');  // Top results of an athlete
Route::get('/v1/athlete/{athlete}/race-days/{year}', 'App\Http\Controllers\AthleteController@raceDays');  // Days raced in a season
Route::get('/v1/athlete/{athlete}/elevation/{year}', 'App\Http\Controllers\AthleteController@elevation'); // Elevation overcome by an athlete during a season
Route::get('/v1/athlete/{athlete}/seasonSummary/{year}', 'App\Http\Controllers\AthleteController@seasonSummary'); // summary stats for a season <elevation, raceDays, points>
// Statistics
Route::get('/v1/statistics/years', 'App\Http\Controllers\StatisticsController@years');
Route::get('/v1/statistics/mostWins/{year?}', 'App\Http\Controllers\StatisticsController@mostWins');
Route::get('/v1/statistics/mostRaceDays/{year?}', 'App\Http\Controllers\StatisticsController@mostRaceDays');
Route::get('/v1/statistics/mostVerticalMeters/{year?}', 'App\Http\Controllers\StatisticsController@mostVerticalMeters');
Route::get('/v1/statistics/mostGrandeCourseWins/{year?}', 'App\Http\Controllers\StatisticsController@mostGrandeCourseWins');
Route::get('/v1/statistics/mostWorldCupWins/{year?}', 'App\Http\Controllers\StatisticsController@mostWorldCupWins');
Route::get('/v1/statistics/mostChocolates/{year?}', 'App\Http\Controllers\StatisticsController@mostChocolates');
Route::get('/v1/statistics/mostTopTens/{year?}', 'App\Http\Controllers\StatisticsController@mostTopTens');
Route::get('/v1/statistics/mostPointsPerRaceDay/{year?}', 'App\Http\Controllers\StatisticsController@mostPointsPerRaceDay');
Route::get('/v1/statistics/activeAthletes/{raceCat?}', 'App\Http\Controllers\StatisticsController@activeAthletes');
Route::get('/v1/statistics/pointsPerMonth/{year?}/{month?}', 'App\Http\Controllers\StatisticsController@pointsPerMonth');
Route::get('/v1/statistics/youngestAthletes/{raceCat?}', 'App\Http\Controllers\StatisticsController@youngestAthletes');
Route::get('/v1/statistics/oldestAthletes/{raceCat?}', 'App\Http\Controllers\StatisticsController@oldestAthletes');
Route::get('/v1/statistics/winsByCountries/{year?}', 'App\Http\Controllers\StatisticsController@mostWinsByCountries');
Route::get('/v1/statistics/winnersByCountries/{year?}', 'App\Http\Controllers\StatisticsController@mostWinnersByCountries');
Route::get('/v1/statistics/mostNationsRacedIn/{year?}', 'App\Http\Controllers\StatisticsController@mostNationsRacedIn');

// Rankings
Route::get('/rankings', 'App\Http\Controllers\RankingController@ranking')->name('rankings');
Route::get('/rankings/skimostats/all-time/{category}', 'App\Http\Controllers\RankingController@rankingAllTime')->name('rankings.all-time');
Route::get('/rankings/ismf/all-time/{category}', 'App\Http\Controllers\RankingController@rankingAllTimeISMF')->name('rankings.all-time-ismf');
Route::get('/rankings/youthwc/all-time/{category}', 'App\Http\Controllers\RankingController@rankingAllTimeYouthWc')->name('rankings.all-time-youthwc');
Route::get('/rankings/skimostats/all-time/type/{raceType}/{category}', 'App\Http\Controllers\RankingController@rankingRaceType')->name('rankings.type.category');
Route::get('/rankings/ismf/all-time/type/{raceType}/{category}', 'App\Http\Controllers\RankingController@rankingRaceTypeISMF')->name('rankings.type.category.ismf');
Route::get('/rankings/youthwc/all-time/type/{raceType}/{category}', 'App\Http\Controllers\RankingController@rankingRaceTypeYouthWc')->name('rankings.type.category.youthwc');
Route::get('/rankings/skimostats/{year}/type/{raceType}/{category}', 'App\Http\Controllers\RankingController@rankingRaceTypeYear')->name('rankings.type.year');
Route::get('/rankings/ismf/{year}/type/{raceType}/{category}', 'App\Http\Controllers\RankingController@rankingRaceTypeYearISMF')->name('rankings.type.year.ismf');
Route::get('/rankings/youthwc/{year}/type/{raceType}/{category}', 'App\Http\Controllers\RankingController@rankingRaceTypeYearYouthWc')->name('rankings.type.year.youthwc');
Route::get('/rankings/{rankingType}/{category}', 'App\Http\Controllers\RankingController@rankingCategorySlug')->name('rankings.category');
Route::get('/rankings/{rankingType}/{year}/{category}', 'App\Http\Controllers\RankingController@rankingYear')->name('rankings.year');
// End rankings

Route::get('/races', 'App\Http\Controllers\FrontController@races')->name('races');
Route::get('/races/{year}', 'App\Http\Controllers\FrontController@races')->name('races.year');
Route::get('/races/{year}/{month}', 'App\Http\Controllers\FrontController@races')->name('races.month');

// STATISTICS
// FOR ATHLETES FILTERED BY SEASON
Route::get('/statistics/', 'App\Http\Controllers\StatisticsController@victories')->name('statistics');
Route::get('/statistics/victories/{year?}', 'App\Http\Controllers\StatisticsController@victories')->name('statistics.victories')->where('year', '^(|all-seasons|\b(19|20)\d{2}\b)$');
Route::get('/statistics/race-days/{year?}', 'App\Http\Controllers\StatisticsController@raceDays')->name('statistics.raceDays')->where('year', '^(|all-seasons|\b(19|20)\d{2}\b)$');
Route::get('/statistics/points-per-raceday/{year?}', 'App\Http\Controllers\StatisticsController@pointsPerRaceday')->name('statistics.pointsPerRaceday')->where('year', '^(|all-seasons|\b(19|20)\d{2}\b)$');
Route::get('/statistics/grand-course-victories/{year?}', 'App\Http\Controllers\StatisticsController@grandCourseVictories')->name('statistics.grandCourseVictories')->where('year', '^(|all-seasons|\b(19|20)\d{2}\b)$');
Route::get('/statistics/world-cup-victories/{year?}', 'App\Http\Controllers\StatisticsController@worldCupVictories')->name('statistics.worldCupVictories')->where('year', '^(|all-seasons|\b(19|20)\d{2}\b)$');
Route::get('/statistics/chocolates/{year?}', 'App\Http\Controllers\StatisticsController@chocolates')->name('statistics.chocolates')->where('year', '^(|all-seasons|\b(19|20)\d{2}\b)$');
Route::get('/statistics/top-tens/{year?}', 'App\Http\Controllers\StatisticsController@topTens')->name('statistics.topTens')->where('year', '^(|all-seasons|\b(19|20)\d{2}\b)$');
Route::get('/statistics/countries-raced-in/{year?}', 'App\Http\Controllers\StatisticsController@countriesRacedIn')->name('statistics.countriesRacedIn')->where('year', '^(|all-seasons|\b(19|20)\d{2}\b)$');

// ATHLETES BY SEASON WITH HISTOGRAM
Route::get('/statistics/points-per-age/{year?}', 'App\Http\Controllers\StatisticsController@pointsPerAge')->name('statistics.pointsPerAge')->where('year', '^(\b(19|20)\d{2}\b)$');

// ATHLETES BY RANKING CATEGORY
Route::get('/statistics/active-athletes/{rankingCategory?}', 'App\Http\Controllers\StatisticsController@activeAthletes')->name('statistics.activeAthletes')->where('rankingCategory', '^(all|world-cup|grand-course)$');
Route::get('/statistics/youngest-athletes/{rankingCategory?}', 'App\Http\Controllers\StatisticsController@youngestAthletes')->name('statistics.youngestAthletes')->where('rankingCategory', '^(all|world-cup|grand-course)$');
Route::get('/statistics/oldest-athletes/{rankingCategory?}', 'App\Http\Controllers\StatisticsController@oldestAthletes')->name('statistics.oldestAthletes')->where('rankingCategory', '^(all|world-cup|grand-course)$');

// ATHLETES MONTHLY
Route::get('/statistics/points-per-month/{monthYear?}', 'App\Http\Controllers\StatisticsController@pointsPerMonth')->name('statistics.pointPerMonth')->where('monthYear', '\b(0[1-9]|1[0-2])-(19|20)\d{2}\b');

// COUNTRIES BY SEASON
Route::get('/statistics/wins-per-countries/{year?}', 'App\Http\Controllers\StatisticsController@winsPerCountries')->name('statistics.winsPerCountries')->where('year', '^(|all-seasons|\b(19|20)\d{2}\b)$');
Route::get('/statistics/winners-per-countries/{year?}', 'App\Http\Controllers\StatisticsController@winnersPerCountry')->name('statistics.winnersPerCountry')->where('year', '^(|all-seasons|\b(19|20)\d{2}\b)$');
Route::get('/statistics/points-per-country/{year?}', 'App\Http\Controllers\StatisticsController@pointsPerCountry')->name('statistics.pointsPerCountry')->where('year', '^(|all-seasons|\b(19|20)\d{2}\b)$');
// END STATISTICS

Route::get('/about-us', 'App\Http\Controllers\FrontController@aboutUs')->name('about-us');
Route::get('/get-instafeed', 'App\Http\Controllers\FrontController@getInstafeed')->name('get-instafeed');
Route::get('/race-event/{event}', 'App\Http\Controllers\FrontController@raceEventDetailRedirect')->name('raceEventDetail');
Route::get('/event/{slug}', 'App\Http\Controllers\FrontController@raceEventDetail')->name('raceEventDetail.slug');
Route::get('/event/{slug}/{category}', 'App\Http\Controllers\FrontController@raceEventDetail')->name('raceEventDetail.slug.category');
Route::get('/event/{slug}/{category}/gc', 'App\Http\Controllers\FrontController@raceEventDetail')->name('raceEventDetail.slug.category.gc');

Route::post('/rankings/homepage', 'App\Http\Controllers\RankingController@getHomepageRankings');
Route::post('/rankings/homepage/nations', 'App\Http\Controllers\RankingController@getHomepageRankingsNations');

Route::get('/world-cup', 'App\Http\Controllers\FrontController@worldCup')->name('world-cup');
Route::get('/world-cup/{year}', 'App\Http\Controllers\FrontController@worldCup')->name('world-cup.year');

Route::get('/race/{slug}', 'App\Http\Controllers\FrontController@raceOverview')->name('race-overview');
Route::get('/race/{slug}/{year}', 'App\Http\Controllers\FrontController@raceOverview')->name('race-overview.year');

// TESTING
// Route::get('/test', 'FrontController@test')->name('test');
// Route::get('/testlist', function () {
//     return ['Laravel', 'Vue', 'PHP', 'Javascript', 'Tooling'];
// });

// Testing issuing tokens with Sanctum
Route::post("/tokens/create", function (Request $request) {
    $token = $request->user()->createToken($request->token_name);

    return ["token" => $token->plainTextToken];
});

// Artisan
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/artisan/migrate', 'App\Http\Controllers\Admin\ArtisanController@migrate');
    Route::get('/artisan/dump-autoload', 'App\Http\Controllers\Admin\ArtisanController@dumpAutoload');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {

    Route::get('/', 'App\Http\Controllers\Admin\HomeController@index')->name('home');

    Route::get('/users', 'App\Http\Controllers\Admin\ApiUsageController@index')->name('users.list');
    Route::post('/users', 'App\Http\Controllers\Admin\ApiUsageController@create')->name('users.create');
    Route::delete("/users/{id}", "App\Http\Controller\Admin\ApiUsageController@delete")->name("users.delete");

    $adminControllers = [
        'athletes' => 'App\Http\Controllers\Admin\AthleteController',
        'teams' => 'App\Http\Controllers\Admin\TeamController',
        'races' => 'App\Http\Controllers\Admin\RaceController',
        'ranking-categories' => 'App\Http\Controllers\Admin\RankingCategoryController',
        'partner-categories' => 'App\Http\Controllers\Admin\PartnerCategoryController',
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

    Route::post('partner-categories/add-entry/{entry}', 'App\Http\Controllers\Admin\PartnerCategoryController@createEntry')->name('partner-categories.add-entry');
    Route::post('partner-categories/save-entry-order/{entry}', 'App\Http\Controllers\Admin\PartnerCategoryController@saveEntryOrder')->name('partner-categories.save-entry-order');
    Route::get('partner-categories/delete-entry/{entry}/{index}', 'App\Http\Controllers\Admin\PartnerCategoryController@deleteEntry')->name('partner-categories.delete-entry');

    Route::get('athletes/{athlete}/races', 'App\Http\Controllers\Admin\AthleteController@athleteRaces')->name('athletes.races');
    Route::post('athletes/top-results', 'App\Http\Controllers\Admin\AthleteController@updateTopResults')->name('athletes.top-results.update');

    Route::get('categories', 'App\Http\Controllers\Admin\CategoryController@index')->name('categories.list');
    Route::post('categories', 'App\Http\Controllers\Admin\CategoryController@create')->name('categories.create');
    Route::get('categories/{category}', 'App\Http\Controllers\Admin\CategoryController@edit')->name('categories.edit');
    Route::post('categories/{category}', 'App\Http\Controllers\Admin\CategoryController@update')->name('categories.update');

    Route::get('race/{race}/events', 'App\Http\Controllers\Admin\RaceEventController@list')->name('race-events.list');
    Route::get('race-event/{raceEvent}/events', 'App\Http\Controllers\Admin\RaceEventController@listSubevents')->name('race-events.list-subevents');
    Route::get('race/{race}/events/add', 'App\Http\Controllers\Admin\RaceEventController@add')->name('race-events.add');
    Route::get('race-event/{raceEvent}/events/add', 'App\Http\Controllers\Admin\RaceEventController@addSubevent')->name('race-events.add-subevent');
    Route::get('race-event/{raceEvent}/events/edit/{entry}', 'App\Http\Controllers\Admin\RaceEventController@editSubevent')->name('race-events.edit-subevent');
    Route::get('race-event/edit/{entry}', 'App\Http\Controllers\Admin\RaceEventController@edit')->name('race-events.edit');

    Route::post('race/{race}/events/create', 'App\Http\Controllers\Admin\RaceEventController@create')->name('race-events.create');
    Route::post('race-event/update/{entry}', 'App\Http\Controllers\Admin\RaceEventController@update')->name('race-events.update');
    Route::get('race-event/delete/{entry}', 'App\Http\Controllers\Admin\RaceEventController@delete')->name('race-events.delete');

    Route::get('race-event/{entry}', 'App\Http\Controllers\Admin\RaceEventController@show')->name('race-events.show');
    Route::get('race-event/{entry}/stage/{stage}', 'App\Http\Controllers\Admin\RaceEventController@show')->name('race-events.showStage');
    Route::get('race-event-stage/{stage}/delete', 'App\Http\Controllers\Admin\RaceEventController@deleteStage')->name('race-events.deleteStage');
    Route::get('race-event/{entry}/add-stage', 'App\Http\Controllers\Admin\RaceEventController@newStage')->name('race-events.newStage');
    Route::post('race-event/{raceEvent}/import', 'App\Http\Controllers\Admin\RaceEventController@import')->name('race-events.import');
    Route::get('race-event/{raceEvent}/{category}/export-entries/csv', 'App\Http\Controllers\Admin\RaceEventController@exportEntriesToCsv')->name('race-events.export-entries.csv');
    Route::get('race-event/{entry}/{categoryId}/{stageId}/deleteAll', 'App\Http\Controllers\Admin\RaceEventController@deleteAllResults')->name('race-events.deleteAllResults');


    // Added by MV
    // Sprint heats config
    Route::get("race-event/edit/{raceEvent}/{category}/knockouts", "App\Http\Controllers\Admin\RaceEventController@knockouts")->name("knockouts");

    // GC results genration
    Route::get("race-event/{raceEvent}/generateGCResults", "App\Http\Controllers\Admin\RaceEventController@generateGCResults")->name("race-events.generateGCResults");

    Route::post('race-event/{event}/c/{category}/add', 'App\Http\Controllers\Admin\RaceEventEntryController@add')->name('race-events.add-entry');
    Route::get('race-event-entry/{entry}/delete', 'App\Http\Controllers\Admin\RaceEventEntryController@delete')->name('race-event-entry.delete');
    Route::post('race-event-entry/{entry}/update', 'App\Http\Controllers\Admin\RaceEventEntryController@updateSingle')->name('race-event-entry.update');
    Route::post('race-event-entry/{entry}/update-team', 'App\Http\Controllers\Admin\RaceEventEntryController@updateSingleTeam')->name('race-event-entry.update-team');
    Route::post('race-event-entry/add-team-entry', 'App\Http\Controllers\Admin\RaceEventEntryController@addSingleTeam')->name('race-event-entry.add-team');

    Route::get('countries', 'App\Http\Controllers\Admin\CountryController@list')->name('countries');

    Route::post('team/{team}/add-athlete', 'App\Http\Controllers\Admin\TeamController@addAthlete')->name('team.add-athlete');
    Route::get('team/remove-athlete/{teamAthlete}', 'App\Http\Controllers\Admin\TeamController@removeAthlete')->name('team.remove-athlete');

    Route::get('rankings/refresh/ismf', 'App\Http\Controllers\Admin\RankingController@refreshAllIsmfRankings')->name('rankings.refresh.ismf');
    Route::get('rankings/refresh/youthwc', 'App\Http\Controllers\Admin\RankingController@refreshAllIsmfYouthWcRankings')->name('rankings.refresh.youthwc');
    Route::get('rankings/refresh/skimostats', 'App\Http\Controllers\Admin\RankingController@refreshAllSkimostatsRankings')->name('rankings.refresh.skimostats');

    Route::get('rankings', 'App\Http\Controllers\Admin\RankingController@index')->name('rankings.index');

    Route::get('rankings/table/update', 'App\Http\Controllers\Admin\RankingController@updateRankingTable')->name('rankings.table.update');
    Route::get('rankings/table/update/{type}', 'App\Http\Controllers\Admin\RankingController@updateRankingTable')->name('rankings.table.update.type');
    Route::get('rankings/table/update/{type}/{year}', 'App\Http\Controllers\Admin\RankingController@updateRankingTable')->name('rankings.table.update.year');

});

// Experimenting with livewire
// Route::get("/counter", "App\Livewire\Counter");
// Route::get("/hello", "App\Livewire\HelloWorld");
// Route::get("/todos", "App\Livewire\Todos");

Route::get("/todos", Todos::class);
Route::get("/counter", Counter::class);
Route::get("/hello", HelloWorld::class);
Route::get("/posts", Posts::class);
Route::get("/races", Races::class);
Route::get("/create-post", CreatePost::class);
Route::get("/create-race-event", CreateRaceEvent::class);
Route::get("/sprint-knockouts-admin", SprintKnockoutsAdmin::class);
Route::get("/livewire",  function () { return view("welcomeLiveWire"); });
