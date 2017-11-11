<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'tag', 'namespace' => 'Modules\Tag\Http\Controllers'], function()
{
    Route::get('/', 'TagController@index');
    Route::get('/{id}', 'TagController@show');
    Route::delete('/{id}', 'TagController@destroy')->middleware(['author', 'admin']);
    Route::post('/', 'TagController@store')->middleware(['author', 'admin']);
    Route::patch('/{id}', 'TagController@update')->middleware(['author', 'admin']);
});
