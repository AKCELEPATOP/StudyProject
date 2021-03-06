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


Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/url_home', 'HomeController@url')->name('url_home');


Route::resource('/task', 'TaskController',
    ['only' => ['index']]);

Route::resource('/url', 'UrlController',
    ['except' => ['create', 'show', 'edit']]);
