<?php

use Illuminate\Support\Facades\Route;

//manual login debug route
Route::get('ll/{id}','MainController@debug');

//user api
Route::group(['middleware' => 'auth'], function(){
    Route::group(['prefix' => 'api/crash'], function(){
	    Route::get('bet', 'MainController@newBet');
	    Route::get('stop', 'MainController@stopBet');
	    Route::get('getWin', 'MainController@getWin');
	    Route::get('cancel', 'MainController@cancelBet');
	});
	Route::group(['prefix' => 'api/skins'], function(){
	    Route::get('get', 'SkinsController@get');
	    Route::get('buy', 'SkinsController@buy');
	});
	Route::group(['prefix' => 'api/chat'], function(){
	    Route::get('send', 'ChatController@send');
	});
	Route::group(['prefix' => 'api/ticket'], function(){
	    Route::get('new', 'TicketController@new');
	});
	Route::group(['prefix' => 'api/draw'], function(){
	    Route::get('take', 'DrawController@take');
	});
});

Route::group(['middleware' => 'admin', 'prefix' => 'admin'], function(){
	Route::get('/', 'AdminController@index');
});

//steam auth
Route::get('auth/steam', 'SteamController@redirectToSteam')->name('login');
Route::get('auth/steam/handle', 'SteamController@handle');
Route::get('auth/logout', 'SteamController@logout')->name('logout');

//SPA
Route::any('api/{all}', function () {
    return abort(404);
})->where('all', '.*');
Route::any('{all}', 'MainController@index')->where('all', '.*');
