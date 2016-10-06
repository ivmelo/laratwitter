<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', 'PostController@index');

Route::get('/post', 'PostController@index');
Route::post('/post', 'PostController@store');

Route::get('/u/{username}', 'UserController@show');
Route::post('u/{user_id}/follow', 'UserController@follow');
Route::post('u/{user_id}/unfollow', 'UserController@unfollow');

Auth::routes();

Route::get('/home', 'HomeController@index');
