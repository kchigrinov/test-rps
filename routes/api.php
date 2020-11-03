<?php

Route::group(['namespace' => 'Api'], function () {

    Route::group(['prefix' => 'rps'], function () {
        Route::get('game/{game}', ['uses' => 'RPSController@getGame']);
        Route::get('list', ['uses' => 'RPSController@getList']);
        Route::get('history', ['uses' => 'RPSController@getHistory']);
    });

    Route::group(['prefix' => 'rps', 'middleware' => 'api-auth'], function () {
        Route::post('create', ['uses' => 'RPSController@postCreate']);
        Route::post('join/{game}', ['uses' => 'RPSController@postJoin']);
        Route::post('gesture/{game}', ['uses' => 'RPSController@postGesture']);
        Route::post('quit/{game}', ['uses' => 'RPSController@postQuit']);
    });

});
