<?php

// play ground
// 

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
Route::get('user/me', 'UserController@me');
Route::get('user/me/posts', 'UserController@myPosts');
Route::get('user/me/resetpass', 'UserController@resetPass');
Route::get('user/{id}/followers', 'UserController@followers');
Route::get('user/{id}/followings', 'UserController@followings');
Route::resource('user', 'UserController');
/* USER END*/

/* POST START*/
Route::resource('post', 'PostController');
Route::get('post/{id}/praise', 'PostController@praise');
Route::delete('post/reply/{id}', 'PostController@disableReply');
/* POST END*/

/* ACTIVITIES START*/
Route::resource('activity', 'ActivitiesController');
Route::get('activity/{id}/sign', 'ActivitiesController@sign');
/* ACTIVITIES END*/

/* VERIFY START*/
Route::get('verify/code', 'VerificationController@getVCode');
/* VERIFY END*/
