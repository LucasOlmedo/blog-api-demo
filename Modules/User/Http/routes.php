<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:api','prefix' => 'user', 'namespace' => 'Modules\User\Http\Controllers'], function()
{
    Route::get('/', 'UserController@index')->middleware('admin');
    Route::post('/', 'UserController@store')->middleware('admin');
    Route::get('/{id}', 'UserController@show');
    Route::patch('/{id}', 'UserController@update');
    Route::delete('/{id}', 'UserController@destroy')->middleware('admin');
});

Route::post('user/register', 'Modules\User\Http\Controllers\UserController@register')->middleware('web');