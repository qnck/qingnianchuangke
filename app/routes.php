<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/
Route::get('/', 'HomeController@index');

/* USER START*/
Route::resource('user', 'UserController');
/* USER END*/

/* POST START*/
Route::resource('post', 'PostController');
Route::get('post/praise/{id}', 'PostController@praise');
Route::delete('post/reply/{id}', 'PostController@disableReply');
/* POST END*/

/* ACTIVITIES START*/
Route::resource('activities', 'ActivitiesController');
/* ACTIVITIES END*/

/* VERIFY START*/
Route::get('verify/code', 'VerificationController@getVCode');
/* VERIFY END*/
