<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'api'], function () {
    Route::group(['prefix' => 'v1'], function () {
        Route::post('generate-multiple-choices', 'GenerateController@generateMultipleChoices');
    });
});
