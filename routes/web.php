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
    return redirect()->route('movies.index');
});

Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login')->name('login.attempt');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

Route::get('lang/{locale}', 'LocaleController@switch')->name('lang.switch');

Route::middleware('auth')->group(function () {
    Route::get('movies', 'MovieController@index')->name('movies.index');
    Route::get('movies/search', 'MovieController@search')->name('movies.search');
    Route::get('movies/{imdbId}', 'MovieController@show')->name('movies.show');

    Route::get('favorites', 'FavoriteController@index')->name('favorites.index');
    Route::post('favorites', 'FavoriteController@store')->name('favorites.store');
    Route::delete('favorites/{imdbId}', 'FavoriteController@destroy')->name('favorites.destroy');
});
