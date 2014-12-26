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

Route::group(array('before' => 'auth'), function()
{
  Route::get('/user', 'HomeController@showUser');  
  Route::get('/save-token', 'HomeController@saveGoogleToken');
});

Route::group(array('before' => 'guest'), function()
{
  Route::post('/login', 'HomeController@doLogin');  
});
