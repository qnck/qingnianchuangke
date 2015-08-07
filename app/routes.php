<?php

// play ground
//

Route::get('/', 'HomeController@index');

/* ME START*/
Route::get('user/me', 'MeController@me');
Route::get('user/me/posts', 'MeController@myPosts');
Route::get('user/me/reply', 'MeController@myReply');
Route::get('user/me/praise', 'MeController@myPraise');
Route::get('user/me/resetpass', 'MeController@resetPass');
Route::get('user/me/followers', 'MeController@myFollowers');
Route::get('user/me/followings', 'MeController@myFollowings');
/* ME END*/

/* ME PROFILE START*/
Route::get('user/me/profile/check', 'MeController@profileCheck');
Route::post('user/me/profile/detail', 'MeController@postDetail');
Route::post('user/me/profile/contact', 'MeController@postContact');
Route::post('user/me/profile/card', 'MeController@postCard');
Route::get('user/me/profile/detail', 'MeController@getDetail');
Route::get('user/me/profile/contact', 'MeController@getContact');
Route::get('user/me/profile/card', 'MeController@getCard');
/* ME PROFILE END*/

/* ME BOOTH START*/
Route::post('user/me/booth', 'MeController@postBooth');
Route::get('user/me/booth', 'MeController@listBooth');
Route::get('user/me/booth/{id}', 'MeController@booth');
Route::put('user/me/booth/{id}/desc', 'MeController@putBoothDesc');
Route::put('user/me/booth/{id}/status', 'MeController@putBoothStatus');
Route::post('user/me/product', 'MeController@postProduct');
Route::get('user/me/product', 'MeController@getProducts');
Route::get('user/me/product/{id}', 'MeController@getProduct');
Route::put('user/me/product/{id}', 'MeController@updateProduct');
Route::get('user/me/product/{id}/on', 'MeController@productOn');
Route::post('user/me/product/sort', 'MeController@updateProductSort');
Route::post('user/me/product/discount', 'MeController@updateProductDiscount');
/* ME BOOTH END*/

/* ME FRIEND START*/
Route::get('user/me/friend', 'MeFriendController@index');
Route::get('user/me/friend/remove', 'MeFriendController@remove');
Route::get('user/me/friend/confirm', 'MeFriendController@confirm');
Route::post('user/me/friend/invite', 'MeFriendController@invite');
Route::get('user/me/friend/invite', 'MeFriendController@indexInvite');
Route::get('user/me/friend/invite/remove', 'MeFriendController@removeInvite');
Route::get('user/me/friend/check', 'MeFriendController@check');
/* ME FRIEND END*/

/* USER START*/
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

/* MAKER START*/
Route::get('market/hot', 'MarketController@index');
Route::get('market/convenient', 'MarketController@convenient');
Route::get('market/maker', 'MarketController@maker');
Route::get('market/booth', 'MarketController@listBooth');
Route::get('market/booth/{id}', 'MarketController@getBooth');
Route::post('market/booth/{id}/follow', 'MarketController@postBoothFollow');
Route::get('market/product', 'MarketController@listProduct');
Route::get('market/product/{id}', 'MarketController@getProduct');
Route::post('market/product/{id}/reply', 'MarketController@postProductReply');
Route::post('market/promotion/{id}/praise', 'MarketController@postPromoPraise');
Route::get('market/cart', 'MarketController@listCarts');
Route::post('market/cart', 'MarketController@postCart');
Route::put('market/cart/{id}', 'MarketController@putCart');
Route::delete('market/cart/{id}', 'MarketController@delCart');
/* MAKER END*/

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
Route::post('lbs/user/{id}', 'LocationController@updateUser');
Route::post('lbs/booth/{id}', 'LocationController@updateBooth');
/* LBS END*/
