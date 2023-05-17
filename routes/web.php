<?php

use Illuminate\Http\Request;

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

Route::group(['prefix' => 'public', 'as' => 'saidy.voyager.survey.', 'middleware' => ['web']], function () {
    Route::get('/survey/{survey_key}', ['uses' => 'Saidy\\VoyagerSurvey\\Http\\Controllers\\PublicSurveyController@index', 'as' => 'public_view']);
    Route::get('/survey/{survey_key}/{id}/thanks', ['uses' => 'Saidy\\VoyagerSurvey\\Http\\Controllers\\PublicSurveyController@thanks', 'as' => 'public_view_thanks']);
    Route::post('/survey/{survey_key}', ['uses' => 'Saidy\\VoyagerSurvey\\Http\\Controllers\\PublicSurveyController@store_survey', 'as' => 'public_view_store']);
    Route::put('/survey/{survey_key}', ['uses' => 'Saidy\\VoyagerSurvey\\Http\\Controllers\\PublicSurveyController@update', 'as' => 'public_view_update']);
});
