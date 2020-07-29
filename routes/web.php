<?php

use Illuminate\Support\Facades\Route;

Route::get('ll/{id}','MainController@debug');

Route::group(['middleware' => 'auth'], function(){
    Route::group(['prefix' => 'api/crash'], function(){
	    Route::any('bet', 'MainController@newBet');
	    Route::any('stop', 'MainController@stopBet');
	    Route::any('cancel', 'MainController@cancelBet');
	});
	Route::group(['prefix' => 'api/skins'], function(){
	    Route::any('get', 'SkinsController@get');
	    Route::any('buy', 'SkinsController@buy');
	});
});



//AUTH
Route::get('auth/steam', 'SteamController@redirectToSteam')->name('login');
Route::get('auth/steam/handle', 'SteamController@handle');
Route::get('auth/logout', 'SteamController@logout')->name('logout');

Route::get('bb', 'MainController@bb');

Route::any('api/{all}', function () {
    return abort(404);
})->where('all', '.*');

//SPA
Route::any('{all}', 'MainController@index')->where('all', '.*');
