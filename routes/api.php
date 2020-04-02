<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('register', 'Api\AuthController@register');
Route::post('login', 'Api\AuthController@login');
Route::post('logout', 'Api\AuthController@logout');
Route::get('filter-event', 'Api\EventController@filter');

Route::middleware('auth:api')->group(function () {
    Route::post('event', 'Api\EventController@store');
    Route::delete('event/{id?}', 'Api\EventController@delete');
    Route::put('event/{id}', 'Api\EventController@update');
});

