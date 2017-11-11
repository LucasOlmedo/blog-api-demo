<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'web', 'prefix' => 'post', 'namespace' => 'Modules\Post\Http\Controllers'], function()
{
    Route::get('/', 'PostController@index');
    Route::post('/', 'PostController@store')->middleware('author');
    Route::get('/{id}', 'PostController@show');
    Route::patch('/{id}', 'PostController@update')->middleware('author');
    Route::delete('/{id}', 'PostController@destroy')->middleware('author');
});
