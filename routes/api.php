<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'crash'], function(){
    Route::group(['middleware' => 'secret'], function () {
		Route::any('getCurrentGame', 'MainController@getCurrentGame');
		Route::any('newGame', 'MainController@newGame');
		Route::any('finishGame', 'MainController@finishGame');
		Route::any('startGame', 'MainController@startGame');
    });
});