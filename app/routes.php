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

/* VERIFY START*/
Route::get('verify/code', 'VerificationController@getVCode');
/* VERIFY END*/
