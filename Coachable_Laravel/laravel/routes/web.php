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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/athlete', 'AthleteController@index')->name('athlete');
Route::get('/parent', 'ParentController@index')->name('parent');
Route::get('/coach', 'CoachController@index')->name('coach');
Route::get('/head', 'HeadCoachController@index')->name('head');

Route::get('/settings', 'SettingsController@index')->name('settings');
Route::post('/settings', 'SettingsController@manageDevice');

Route::get('/run/{userid}/{runid}', 'RunController@index')->name('run');

Route::get('/event/{eventid}/{userid}', 'EventController@index')->name('event');

Route::get('/compare/{userid}/{runid}', 'CompareController@index')->name('compare');

Route::get('/users/logout', 'Auth\LoginController@userLogout')->name('user.logout');
Route::get('/dashboard', 'DashboardController@index');
