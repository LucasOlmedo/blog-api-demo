<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => '/post/{id}/comment', 'namespace' => 'Modules\Comment\Http\Controllers'], function()
{
    Route::get('/', 'CommentController@index');
    Route::post('/', 'CommentController@store')->middleware('auth:api');
    Route::get('/{commentId}', 'CommentController@show')->middleware('auth:api');
    Route::patch('/{commentId}', 'CommentController@update')->middleware('auth:api');
    Route::delete('/{commentId}', 'CommentController@destroy')->middleware('auth:api');
});
