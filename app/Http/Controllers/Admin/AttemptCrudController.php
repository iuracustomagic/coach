<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\AttemptRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class AttemptCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class AttemptCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Attempt::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/attempt');
        CRUD::setEntityNameStrings(trans('nav.attempt'), trans('nav.attempts'));
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
        $this->crud->addButtonFromView('top', 'user_results', 'user_results', 'beginning');

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

        //CRUD::column('user_company')->label(trans('labels.company'));

        CRUD::column('user_division')->label(trans('labels.division'));

        CRUD::column('user_branch')->label(trans('labels.branch'));

        CRUD::column('user_id')->label(trans('labels.user'))->searchLogic(function ($query, $column, $searchTerm) {
            $users = \App\Models\User::where('name', 'like', '%'.$searchTerm.'%')->get('id');
            $query->whereIn('user_id', $users);
        });

        CRUD::column('course')->limit(30)->label(trans('labels.course'));

        CRUD::column('lesson')->limit(30)->label(trans('labels.lesson'));

        CRUD::addColumn([
            'name'    => 'status',
            'label'   => trans('labels.status'),
            'type'    => 'text',
            'wrapper' => [
                'element' => 'span',
                'class' => function ($crud, $column, $entry, $related_key) {
                    switch ($column['text']) {
                        case 'STARTED':
                            return 'badge badge-info';
                            break;

                        case 'PASSED':
                            return 'badge badge-success';
                            break;

                        case 'FAILED':
                            return 'badge badge-danger';
                            break;
                    }
                },
            ]
        ]);

        CRUD::addColumn([
            'name'    => 'mark',
            'label'   => trans('labels.mark'),
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

        CRUD::column('duration')->label(trans('labels.duration'));

        CRUD::column('started')->label(trans('labels.started'));

        CRUD::column('finished')->label(trans('labels.finished'));

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
        CRUD::setValidation(AttemptRequest::class);

        CRUD::field('id');
        CRUD::field('quiz_id');
        CRUD::field('user_id');
        CRUD::field('answers');
        CRUD::field('status');
        CRUD::field('mark');
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
        $attempt = \App\Models\Attempt::find($id);
        if(!empty($attempt) && !empty($attempt->answers)){
            $answers = json_decode($attempt->answers, true);

            $markup = "<table>";
            $markup .= "<tr><th>Вопрос</th><th>Выбранная опция</th><th>Затрачено, сек</th><th>Результат</th></tr>";

            foreach($answers as $answer){
                $markup .= "<tr><td>" . $answer['question'] . "</td><td>";
                foreach($answer['answer'] as $selectedOption){
                    $markup .= $selectedOption . "<br>";
                }
                $markup .= "</td><td>" . $answer['duration'] . "</td>";
                $isTrue = $answer['is_true'] ? '<span class="badge badge-success"><i class="las la-check-square"></i></span>' : '<span class="badge badge-danger"><i class="las la-times-circle"></i></span>';
                $markup .= "<td>" . $isTrue . "</td></tr>";
            }

            $markup .= "</table>";
            return $markup;
        } else {
            return "Нет доступных данных";
        }
    }
}
