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

Route::group(['as' => 'saidy.voyager.survey.', 'middleware' => ['web']], function () {
    Route::get('/{slug}/{id}/survey', ['uses' => 'Saidy\\VoyagerSurvey\\Http\\Controllers\\SurveyController@surveyAction', 'as' => 'survey_action']);
    Route::post('/{slug}/{id}/survey', ['uses' => 'Saidy\\VoyagerSurvey\\Http\\Controllers\\SurveyController@StoreSurveyAction', 'as' => 'store_survey_action']);
    Route::get('/survey/target_columns/{slug}', ['uses' => 'Saidy\\VoyagerSurvey\\Http\\Controllers\\SurveyController@get_table_columns_by_slug', 'as' => 'survey_table_columns_by_slug']);
});
Route::group(['prefix' => 'public', 'as' => 'saidy.voyager.survey.', 'middleware' => ['web']], function () {
    Route::get('/survey/{survey_key}', ['uses' => 'Saidy\\VoyagerSurvey\\Http\\Controllers\\PublicSurveyController@index', 'as' => 'public_view']);
    Route::get('/survey/{survey_key}/{id}/thanks', ['uses' => 'Saidy\\VoyagerSurvey\\Http\\Controllers\\PublicSurveyController@thanks', 'as' => 'public_view_thanks']);
    Route::post('/survey/{survey_key}', ['uses' => 'Saidy\\VoyagerSurvey\\Http\\Controllers\\PublicSurveyController@store_survey', 'as' => 'public_view_store']);
    Route::put('/survey/{survey_key}', ['uses' => 'Saidy\\VoyagerSurvey\\Http\\Controllers\\PublicSurveyController@update', 'as' => 'public_view_update']);
});
