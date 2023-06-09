<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ReportRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ReportCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ReportCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Report::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/report');
        CRUD::setEntityNameStrings(trans('nav.report'), trans('nav.reports'));
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        $this->crud->removeAllButtons();

        $this->crud->enableDetailsRow();

        /* Get only attempts of current employee */
        if(backpack_user()->hasRole('Employee')){
            $this->crud->addClause('where', 'user_id', '=', backpack_user()->id);
        }

        /* Get only attempts of admins division */
        if(backpack_user()->hasRole('Admin')){
            $employees = [];
            if(!empty(backpack_user()->divisions)){
                foreach(backpack_user()->divisions as $division){
                    if(!empty($division->employees)){
                        foreach($division->employees as $employee){
                            $employees[] = $employee->id;
                        }
                    }
                }
            }

            $this->crud->addClause('whereIn', 'user_id', $employees);
        }

        /* Get only attempts of managers branches */
        if(backpack_user()->hasRole('Manager')){
            $employees = [];
            if(!empty(backpack_user()->branches)){
                foreach(backpack_user()->branches as $branch){
                    if(!empty($branch->employees)){
                        foreach($branch->employees as $employee){
                            $employees[] = $employee->id;
                        }
                    }
                }
            }

            $this->crud->addClause('whereIn', 'user_id', $employees);
        }

        if(!backpack_user()->hasRole('Employee')){
            CRUD::column('user_id')->label(trans('labels.user'))->searchLogic(function ($query, $column, $searchTerm) {
                $users = \App\Models\User::where('name', 'like', '%'.$searchTerm.'%')->get('id');
                $query->whereIn('user_id', $users, 'or');
            });
        }

        CRUD::column('course')->limit(255)->label(trans('labels.course'))->searchLogic(function ($query, $column, $searchTerm) {
            $courses = \App\Models\Course::where('name', 'like', '%'.$searchTerm.'%')->get('id');
            $quizzes = \App\Models\Quiz::whereIn('course_id', $courses)->get('id');
            $query->whereIn('quiz_id', $quizzes, 'or');
        });

        CRUD::column('lesson')->limit(255)->label(trans('labels.lesson'))->searchLogic(function ($query, $column, $searchTerm) {
            $lessons = \App\Models\Lesson::where('name', 'like', '%'.$searchTerm.'%')->get('id');
            $quizzes = \App\Models\Quiz::whereIn('lesson_id', $lessons)->get('id');
            $query->whereIn('quiz_id', $quizzes, 'or');
        });

        CRUD::addColumn([
            'name'    => 'passed',
            'label'   => trans('labels.passed'),
            'type'     => 'closure',
            'function' => function($entry) {
                return $entry->passed == 1 ? '<span class="badge badge-success"><i class="las la-check-circle"></i></span>' : '<span class="badge badge-danger"><i class="las la-times-circle"></i></span>';
            }
        ]);

        CRUD::addColumn([
            'name'    => 'best_mark',
            'label'   => trans('labels.best_mark'),
            'type'    => 'text',
            'wrapper' => [
                'element' => 'span',
                'class' => function ($crud, $column, $entry, $related_key) {
                    if($column['text'] >= 8){
                        return 'badge badge-success';
                    } elseif($column['text'] == 0) {
                        return 'badge badge-warning';
                    } else {
                        return 'badge badge-danger';
                    }
                },
            ]
        ]);

        CRUD::addColumn([
            'name'    => 'total_attempts',
            'label'   => trans('labels.total_attempts'),
            'type'    => 'text',
            'wrapper' => [
                'element' => 'span',
                'class' => 'badge badge-info',
            ]
        ]);

        CRUD::addColumn([
            'name'    => 'successful_attempts',
            'label'   => trans('labels.successful_attempts'),
            'type'    => 'text',
            'wrapper' => [
                'element' => 'span',
                'class' => function ($crud, $column, $entry, $related_key) {
                    if($column['text'] > 0){
                        return 'badge badge-success';
                    } else {
                        return 'badge badge-danger';
                    }
                },
            ]
        ]);

        CRUD::addColumn([
            'name'    => 'failed_attempts',
            'label'   => trans('labels.failed_attempts'),
            'type'    => 'text',
            'wrapper' => [
                'element' => 'span',
                'class' => function ($crud, $column, $entry, $related_key) {
                    if($column['text'] > 0){
                        return 'badge badge-danger';
                    } else {
                        return 'badge badge-success';
                    }
                },
            ]
        ]);

        CRUD::addColumn([
            'name'    => 'started_attempts',
            'label'   => trans('labels.started_attempts'),
            'type'    => 'text',
            'wrapper' => [
                'element' => 'span',
                'class' => function ($crud, $column, $entry, $related_key) {
                    if($column['text'] == 0){
                        return 'badge badge-success';
                    } else {
                        return 'badge badge-warning';
                    }
                },
            ]
        ]);

        CRUD::addColumn([
            'name'    => 'avg_mark',
            'label'   => trans('labels.avg_mark'),
            'type'    => 'text',
            'wrapper' => [
                'element' => 'span',
                'class' => function ($crud, $column, $entry, $related_key) {
                    if($column['text'] >= 8){
                        return 'badge badge-success';
                    } elseif($column['text'] == 0) {
                        return 'badge badge-warning';
                    } else {
                        return 'badge badge-danger';
                    }
                },
            ]
        ]);

        CRUD::column('avg_time_formatted')->label(trans('labels.avg_time'));

        /* FILTERS */
        $this->crud->addFilter([
            'name'  => 'locality',
            'type'  => 'select2',
            'label' => trans('labels.locality')
        ], function () {
            return \App\Models\Locality::all()->keyBy('id')->pluck('name', 'id')->toArray();
        }, function ($value) {
            $branches = \App\Models\Branch::where('locality_id', $value)->get('id');
            $users = \App\Models\UserBranches::whereIn('branch_id', $branches)->get('user_id');
            $this->crud->addClause('whereIn', 'user_id', $users);
        });

        $this->crud->addFilter([
            'name'  => 'user_division',
            'type'  => 'select2',
            'label' => trans('labels.division')
        ], function () {
            return \App\Models\Division::all()->keyBy('id')->pluck('name', 'id')->toArray();
        }, function ($value) {
            $users = \App\Models\UserDivisions::where('division_id', $value)->get('user_id');
            $this->crud->addClause('whereIn', 'user_id', $users);
        });

        $this->crud->addFilter([
            'name'  => 'user_branches',
            'type'  => 'select2',
            'label' => trans('labels.branch')
        ], function () {
            return \App\Models\Branch::all()->keyBy('id')->pluck('address', 'id')->toArray();
        }, function ($value) {
            $users = \App\Models\UserBranches::where('branch_id', $value)->get('user_id');
            $this->crud->addClause('whereIn', 'user_id', $users);
        });

        $this->crud->addFilter([
          'type'  => 'text',
          'name'  => 'user_id',
          'label' => trans('labels.user')
        ], 
        false, 
        function($value) {
            $users = \App\Models\User::where('name', 'like', '%'.$value.'%')->get('id');
            $this->crud->addClause('whereIn', 'user_id', $users);
        });

        $this->crud->addFilter([
            'name'  => 'profession',
            'type'  => 'select2',
            'label' => trans('labels.profession')
        ], function () {
            return \App\Models\Profession::all()->keyBy('id')->pluck('name', 'id')->toArray();
        }, function ($value) {
            $courses = \App\Models\Course::where('profession_id', $value)->get('id');
            $quizzes = \App\Models\Quiz::whereIn('course_id', $courses)->get('id');
            $this->crud->addClause('whereIn', 'quiz_id', $quizzes);
        });

        $this->crud->addFilter([
            'name'  => 'course',
            'type'  => 'select2',
            'label' => trans('labels.course')
        ], function () {
            return \App\Models\Course::all()->keyBy('id')->pluck('name', 'id')->toArray();
        }, function ($value) {
            $quizzes = \App\Models\Quiz::where('course_id', $value)->get('id');
            $this->crud->addClause('whereIn', 'quiz_id', $quizzes);
        });

        $this->crud->addFilter([
            'name'  => 'lesson',
            'type'  => 'select2',
            'label' => trans('labels.lesson')
        ], function () {
            return \App\Models\Lesson::all()->keyBy('id')->pluck('name', 'id')->toArray();
        }, function ($value) {
            $quizzes = \App\Models\Quiz::where('lesson_id', $value)->get('id');
            $this->crud->addClause('whereIn', 'quiz_id', $quizzes);
        });

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']); 
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(ReportRequest::class);

        CRUD::field('quiz_id');
        CRUD::field('user_id');
        CRUD::field('avg_mark');
        CRUD::field('avg_time');
        CRUD::field('total_attempts');
        CRUD::field('created_at');
        CRUD::field('updated_at');

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number'])); 
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    protected function showDetailsRow($id)
    {
        $report = \App\Models\Report::find($id);
        if(!empty($report)){

            $attempts = \App\Models\Attempt::where(['quiz_id' => $report->quiz_id, 'user_id' => $report->user_id])->get();

            $markup = "<table>";
    
            if(!empty($attempts)){
                $markup .= "<tr><th>Попытка</th><th>Статус</th><th>Результат</th><th>Затраченно</th><th>Начало</th><th>Конец</th></tr>";
                $a = 1;
                foreach($attempts as $attempt){
                    $markup .= "<tr>";
                        $markup .= "<td>".$a."</td>";
                        switch ($attempt->status) {
                            case 'PASSED':
                                $class = "badge badge-success";
                                break;
                                
                            case 'FAILED':
                                $class = "badge badge-danger";
                                break;
                            
                            case 'STARTED':
                                $class = "badge badge-warning";
                                break;
                        }
                        $markup .= "<td><span class='".$class."'>".$attempt->status."</span></td>";
                        if($attempt->mark == 0){
                            $class = "badge badge-warning";
                        } else if($attempt->mark < 8) {
                            $class = "badge badge-danger";
                        } else {
                            $class = "badge badge-success";
                        }
                        $markup .= "<td><span class='".$class."'>".$attempt->mark."</span></td>";
                        $markup .= "<td>".$attempt->duration."</td>";
                        $markup .= "<td>".$attempt->started."</td>";
                        $markup .= "<td>".$attempt->finished."</td>";
                    $markup .= "</tr>";
                    $a++;
                }
            } else {
                return "Нет доступных данных";
            }

            $markup .= "</table>";
            return $markup;
        } else {
            return "Нет доступных данных";
        }
    }
}
