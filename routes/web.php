<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::redirect('/', '/admin');
Route::redirect('/admin/dashboard', '/admin/report');

Route::get('/admin/evaluation/{employee_id}/start', 'App\Http\Controllers\Admin\EvaluationController@start');
Route::get('/admin/evaluation/{employee_id}/list', 'App\Http\Controllers\Admin\EvaluationController@list');
Route::post('/admin/evaluation/{employee_id}/save', 'App\Http\Controllers\Admin\EvaluationController@save');

//Route::get('/api/fix-db', 'App\Http\Controllers\Api\CompanyController@fixDb');
Route::get('/api/company', 'App\Http\Controllers\Api\CompanyController@index');
Route::get('/api/division', 'App\Http\Controllers\Api\DivisionController@index');
Route::get('/api/locality', 'App\Http\Controllers\Api\LocalityController@index');
Route::get('/api/lesson', 'App\Http\Controllers\Api\LessonController@index');
Route::get('/api/branch', 'App\Http\Controllers\Api\BranchController@index');
Route::get('/api/criterias', 'App\Http\Controllers\Api\CriteriasController@index');
Route::get('/api/profession', 'App\Http\Controllers\Api\ProfessionController@index');
Route::get('/api/supervisor', 'App\Http\Controllers\Api\SupervisorController@index');

Route::get('/api/report', 'App\Http\Controllers\Api\ReportController@index');

Route::middleware([\App\Http\Middleware\TrackLastActiveAt::class])->get('/my-courses', 'App\Http\Controllers\MyCoursesController@list');
Route::middleware([\App\Http\Middleware\TrackLastActiveAt::class])->get('/my-courses/course/{course_id}', 'App\Http\Controllers\MyCoursesController@view');
Route::middleware([\App\Http\Middleware\TrackLastActiveAt::class])->get('/my-courses/course/{course_id}/lesson/{lesson_id}', 'App\Http\Controllers\MyCoursesController@lesson');

Route::middleware([\App\Http\Middleware\TrackLastActiveAt::class])->get('/quizzes/start-quiz/{quiz_id}', 'App\Http\Controllers\QuizController@start');
Route::post('/quizzes/verify-quiz/{attempt_id}', 'App\Http\Controllers\QuizController@verify');

Route::get('/quizzes/fix', 'App\Http\Controllers\QuizController@fix');
Route::get('/quizzes/fix-reports', 'App\Http\Controllers\QuizController@fixReports');