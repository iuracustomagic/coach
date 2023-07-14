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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/auth/login', '\App\Http\Controllers\Api\AuthController@login');
Route::get('/unauthorized', '\App\Http\Controllers\Api\AuthController@unauthorized');

Route::middleware('auth:api')->get('/ping', '\App\Http\Controllers\Api\AuthController@ping');
Route::middleware('auth:api')->get('/pre-sync', '\App\Http\Controllers\Api\AuthController@preSync');
