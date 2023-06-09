<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\BranchRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class BranchCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class BranchReportCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Branch::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/report/branch');
        CRUD::setEntityNameStrings(trans('nav.branch'), trans('nav.branches'));
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        // Deny operations
        $this->crud->removeAllButtons();

        //$this->crud->enableDetailsRow();

        $this->crud->addButtonFromModelFunction('line', 'reportSummary', 'reportSummary', 'beginning');
        $this->crud->addButtonFromModelFunction('line', 'reportEvaluations', 'reportEvaluations', 'beginning');
       
        CRUD::column('company_id')->label(trans('labels.company'));
        CRUD::column('division_id')->label(trans('labels.division'));
        CRUD::column('name')->label(trans('labels.name'));
        CRUD::column('region_id')->label(trans('labels.region'));
        CRUD::column('locality_id')->label(trans('labels.locality'));
        CRUD::column('address')->label(trans('labels.address'));
        //CRUD::column('total_employees')->label(trans('labels.total_employees'));
        CRUD::addColumn([
            'label' => trans('labels.total_employees'),
            'name' => 'total_employees',
            'orderable'  => true,
            'orderLogic' => function ($query, $column, $columnDirection) {
                return $query->withCount('employees')
                ->orderBy('employees_count', $columnDirection);
            }
        ]);

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']); 
         */
    }

    protected function showDetailsRow($id)
    {
        $branch = \App\Models\Branch::find($id);
        $courses = [];
        if($branch->employees->count() > 0){
            foreach($branch->employees as $employee){
                if($employee->courses->count() > 0){
                    foreach($employee->courses as $course){
                        if(!isset($courses[$course->id])){
                            $courses[$course->id] = [
                                'name' => $course->name,
                                'employees' => 1,
                                'involved' => [],
                                'passed_sum' => 0,
                                'passed_qty' => 0,
                                'total_sum' => 0,
                                'total_qty' => 0 
                            ];
                        } else {
                            $courses[$course->id]['employees'] += 1;
                        }
                    }
                }
                if($employee->reports->count() > 0){
                    foreach($employee->reports as $report){
                        if(null != $report->quiz){
                            $courseId = $report->quiz->course->id;
                            if(isset($courses[$courseId])){
                                $courses[$courseId]['involved'][$employee->id] = true;
                                if($report->passed){
                                    $courses[$courseId]['passed_sum'] += $report->best_mark;
                                    $courses[$courseId]['passed_qty'] += 1;
                                }
                                $courses[$courseId]['total_sum'] += $report->avg_mark;
                                $courses[$courseId]['total_qty'] += 1;
                            }
                        }
                    }
                }
                //var_dump($employee->reports[0]);
            }
        }
        /*$courses = [];
        if($branch->employees->count() > 0){
            foreach($branch->employees as $employee){
                if($employee->courses->count() > 0){
                    foreach($employee->courses as $course){
                        if(!isset($courses[$course->id])){
                            $courses[$course->id] = [
                                'name' => $course->name,
                                'employees' => 1,
                                'attempts' => 0
                            ];
                        } else {
                            $courses[$course->id]['employees'] += 1;
                        }

                        $quizzes = \App\Models\Quiz::where('course_id', $course->id)->get()->pluck('id')->toArray();
                        //var_dump($quizzes);
                        $attempts = \App\Models\Attempt::where('user_id', $employee->id)->whereIn('quiz_id', $quizzes)->first();
                        if(!empty($attempts)){
                            $courses[$course->id]['attempts'] += 1;
                        }
                    }
                }
            }
        }*/

        $markup = "<table class='table table-striped table-hover'>";
        if(!empty($courses)){
            $markup .= "<tr>";
                $markup .= "<th>Курс</th>";
                $markup .= "<th>На курсе</th>";
                $markup .= "<th>Активных</th>";
                $markup .= "<th>AVG (Пройдено)</th>";
                $markup .= "<th>AVG (Общая)</th>";
            $markup .= "</tr>";
            foreach($courses as $course){
                $markup .= "<tr>";
                    $markup .= "<td>";
                        $markup .= $course['name'];
                    $markup .= "</td>";
                    $markup .= "<td>";
                        $markup .= $course['employees'];
                    $markup .= "</td>";
                    $markup .= "<td>";
                        $markup .= count($course['involved']);
                    $markup .= "</td>";
                    $markup .= "<td>";
                        $markup .= $course['passed_qty'] > 0 ? round($course['passed_sum'] / $course['passed_qty'], 2) : 0;
                    $markup .= "</td>";
                    $markup .= "<td>";
                        $markup .= $course['total_qty'] > 0 ? round($course['total_sum'] / $course['total_qty'], 2) : 0;
                    $markup .= "</td>";
                $markup .= "</tr>";
            }
        }
        $markup .= "</table>";
        return $markup;
        //$quizzes = \App\Models\Quiz::where('course_id', $course->id)->get()->pluck('id')->toArray();
        /*$branch = \App\Models\Branch::find($id);
        if($branch->employees->count() > 0){
            $results = [];
            $markup = "<div class='container-fluid'><div class='row'>";
            foreach($branch->employees as $employee){
                $markup .= "<div class='card col-md-3'>";
                $markup .= "<h5>" . $employee->name . "</h5>";
                $markup .= "<h6>" . $employee->personal_phone . "</h6>";
                $markup .= "<h6>" . $employee->business_phone . "</h6>";
                $results[$employee->id] = [
                    'name' => $employee->name,
                    'phone_p' => $employee->personal_phone,
                    'phone_b' => $employee->business_phone,
                    'totalCourses' => $employee->courses->count(),
                    'passed' => 0, 
                    'failed' => 0
                ];
                if($employee->courses->count() > 0){
                    foreach($employee->courses as $course){
                        $results[$employee->id]['courses'][$course->id]['name'] = $course->name;
                        $markup .= "<p>" . $course->name . "</p>";
                        if($course->lessons->count() > 0){
                            foreach($course->lessons as $lesson){
                                $results[$employee->id]['courses'][$course->id]['lessons'][$lesson->id]['name'] = $lesson->name;
                                $markup .= "<p>--" . $lesson->name . "</p>";
                                if(null != $lesson->quiz){
                                    $report = \App\Models\Report::where(['quiz_id' => $lesson->quiz->id, 'user_id' => $employee->id])->first();
                                    if(!empty($report)){
                                        $results[$employee->id]['courses'][$course->id]['lessons'][$lesson->id]['passed'] = $report->passed;
                                    } else {
                                        $results[$employee->id]['courses'][$course->id]['lessons'][$lesson->id]['passed'] = null;
                                    } 
                                } else {
                                    $results[$employee->id]['courses'][$course->id]['lessons'][$lesson->id]['passed'] = null;
                                }
                            }
                        }
                    } 
                }
                $markup .= "</div>";
            }
            $markup .= "</div></div>";
            return $markup;
        }*/
    }
}
