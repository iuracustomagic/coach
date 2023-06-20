<?php

use Illuminate\Support\Facades\Route;

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

Route::group([
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
    'namespace'  => 'App\Http\Controllers\Admin',
], function () { // custom admin routes
    Route::crud('company', 'CompanyCrudController');
    Route::crud('division', 'DivisionCrudController');
    Route::crud('supervisor', 'SupervisorCrudController');
    Route::crud('status', 'StatusCrudController');
    Route::crud('region', 'RegionCrudController');
    Route::crud('branch', 'BranchCrudController');
    Route::crud('locality', 'LocalityCrudController');
    Route::crud('profession', 'ProfessionCrudController');
    Route::crud('course', 'CourseCrudController');
    Route::crud('lesson', 'LessonCrudController');
    Route::crud('quiz', 'QuizCrudController');
    Route::crud('attempt', 'AttemptCrudController');
    Route::crud('report', 'ReportCrudController');
    Route::crud('report/branch', 'BranchReportCrudController');
    Route::crud('report/user', 'UserReportCrudController');
    Route::crud('evaluation-paper', 'EvaluationPaperCrudController');
    Route::crud('evaluation-criteria', 'EvaluationCriteriaCrudController');
    Route::get('evaluation/view/{id}', 'EvaluationController@view');
    Route::crud('reports/branch', 'BranchReportCrudController');
    Route::get('reports/branch-summary/{branch_id}', 'ReportsController@branchSummary');
    Route::get('reports/branch-evaluations/{branch_id}', 'ReportsController@branchEvaluations');
    Route::get('reports/view-evaluations/{employee_id}', 'ReportsController@viewEvaluations');
    // Service
    Route::get('service/fix', 'ServiceController@fix');
    Route::get('service/fix-order', 'ServiceController@fixOrder');
    Route::crud('skill', 'SkillCrudController');
}); // this should be the absolute last line of this file