<?php

// play ground
// 

Route::get('/', 'HomeController@index');

/* USER START*/
Route::get('user/search', 'UserController@search');
Route::get('user/{id}/follow', 'UserController@follow');
Route::get('user/{id}/followers', 'UserController@followers');
Route::get('user/{id}/followings', 'UserController@followings');
Route::resource('user', 'UserController');
/* USER END*/

/* ME START*/
Route::get('user/me', 'MeController@me');
Route::get('user/me/posts', 'MeController@myPosts');
Route::get('user/me/reply', 'MeController@myReply');
Route::get('user/me/praise', 'MeController@myPraise');
Route::get('user/me/resetpass', 'MeController@resetPass');
Route::get('user/me/followers', 'MeController@myFollowers');
Route::get('user/me/followings', 'MeController@myFollowings');
Route::post('user/me/booth', 'MeController@newBooth');
Route::get('user/me/booth', 'MeController@boothList');
/* ME END*/

/* ME FRIEND START*/
Route::get('user/me/friend', 'MeFriendController@index');
Route::get('user/me/friend/remove', 'MeFriendController@remove');
Route::get('user/me/friend/confirm', 'MeFriendController@confirm');
Route::post('user/me/friend/invite', 'MeFriendController@invite');
Route::get('user/me/friend/invite', 'MeFriendController@indexInvite');
Route::get('user/me/friend/invite/remove', 'MeFriendController@removeInvite');
Route::get('user/me/friend/check', 'MeFriendController@check');
/* ME FRIEND END*/

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
Route::get('dic/bank', 'DicController@getBanks');
/* DATA DICTIONARY END*/

/* LBS START*/
Route::get('lbs/nearby/user', 'LocationController@getNearbyUsers');
Route::get('lbs/nearby/store', 'LocationController@getNearbyStores');
Route::get('lbs/nearby/act', 'LocationController@getNearbyActivities');
/* LBS END*/
