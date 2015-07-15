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
Route::get('user/me/reply', 'UserController@myReply');
Route::get('user/me/praise', 'UserController@myPraise');
Route::get('user/me/resetpass', 'UserController@resetPass');
Route::get('user/me/followers', 'UserController@myFollowers');
Route::get('user/me/followings', 'UserController@myFollowings');
Route::get('user/search', 'UserController@search');
Route::get('user/{id}/follow', 'UserController@follow');
Route::get('user/{id}/followers', 'UserController@followers');
Route::get('user/{id}/followings', 'UserController@followings');
Route::resource('user', 'UserController');
/* USER END*/

/* POST START*/
Route::resource('post', 'PostController');
Route::get('post/{id}/praise', 'PostController@praise');
Route::delete('post/reply/{id}', 'PostController@disableReply');
/* POST END*/

/* TRADE START*/
Route::resource('trade', 'TradeController');
/* TRADE END*/

/* ACTIVITIES START*/
Route::get('activity/{id}/follow', 'ActivitiesController@follow');
Route::post('activity/{id}/sign', 'ActivitiesController@sign');
Route::get('activity/{id}/signers', 'ActivitiesController@signers');
Route::get('activity/{id}/signer/{sid}/confirm', 'ActivitiesController@confirmSignedUser');
Route::resource('activity', 'ActivitiesController');
/* ACTIVITIES END*/

/* VERIFY START*/
Route::get('verify/code', 'VerificationController@getVCode');
/* VERIFY END*/

/* DATA DICTIONARY START*/
Route::get('dic/school', 'DicController@getSchools');
Route::get('dic/city', 'DicController@getCities');
/* DATA DICTIONARY END*/
