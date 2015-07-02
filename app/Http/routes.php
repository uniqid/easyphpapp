<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

//ApiController
Route::controller('/api/', 'ApiController');

//AppController
Route::get('/menutree/{id}', 'AppController@get_menutree')->where('id', '[0-9]+');
Route::get('/menuedit/{id}', 'AppController@any_menuedit')->where('id', '[0-9]+');
Route::controller('/', 'AppController');

