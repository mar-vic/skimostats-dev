<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->group(function () {
    Route::get('home', 'FrontController@getHomeData');
    Route::get('athletes', 'AthleteController@getApiV1List');
    Route::get('rankings', 'RankingController@getRankingsV1List');
    Route::get('races', 'FrontController@getRacesV1List');
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:api')->group(function () {
    Route::get('/athletes', 'AthleteController@getApiList');
});


// Third party data api
Route::prefix("v2")->group(function () {
    Route::group(["middleware" => ["auth:sanctum"]], function () {
        Route::get("athletes", "AthleteController@getAthletesDataByName");
    });
});
