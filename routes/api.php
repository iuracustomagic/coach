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

Route::middleware('auth:api')->get('/logout', '\App\Http\Controllers\Api\AuthController@logout');

Route::middleware('auth:api')->get('/user-info', '\App\Http\Controllers\Api\mobile\UserInfoController@index');
Route::middleware('auth:api')->get('/courses', '\App\Http\Controllers\Api\mobile\CourseInfoController@list');
Route::middleware('auth:api')->get('/courses/course/{course_id}', '\App\Http\Controllers\Api\mobile\CourseInfoController@view');
Route::middleware('auth:api')->get('/reports', '\App\Http\Controllers\Api\mobile\ReportInfoController@list');
