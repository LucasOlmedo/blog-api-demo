<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'category', 'namespace' => 'Modules\Category\Http\Controllers'], function()
{
    Route::get('/', 'CategoryController@index');
    Route::get('/{id}', 'CategoryController@show');

    Route::group(['middleware' => ['author', 'admin']], function (){
        Route::post('/', 'CategoryController@store');
        Route::patch('/{id}', 'CategoryController@update');
        Route::delete('/{id}', 'CategoryController@destroy');
    });
});
