<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'admin', 'prefix' => 'role', 'namespace' => 'Modules\Role\Http\Controllers'], function()
{
    Route::get('/', 'RoleController@index');
    Route::get('/{id}', 'RoleController@show');
});
