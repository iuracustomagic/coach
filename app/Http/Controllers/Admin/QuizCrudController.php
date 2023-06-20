<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\QuizRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class QuizCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class QuizCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CloneOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Quiz::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/quiz');
        CRUD::setEntityNameStrings(trans('nav.quizz'), trans('nav.quizzes'));
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        /* Get only quizzes assigned to courses from admins or managers division */
        if(backpack_user()->hasRole('Admin') || backpack_user()->hasRole('Manager')){

            $this->crud->denyAccess('delete');
            $this->crud->denyAccess('clone');

            if(backpack_user()->hasRole('Manager')){
                $this->crud->denyAccess('update');
            }

            $courses = [];
            if(!empty(backpack_user()->divisions)){
                foreach(backpack_user()->divisions as $division){
                    if(!empty($division->courses)){
                        foreach($division->courses as $course){
                            $courses[] = $course->id;
                        }
                    }
                }
            }

            $this->crud->addClause('whereIn', 'course_id', $courses);
        }

        CRUD::column('course_id')->label(trans('labels.course'));
        CRUD::column('lesson_id')->label(trans('labels.lesson'));
        CRUD::column('total_questions')->label(trans('labels.total_questions'));
        CRUD::column('questions_to_show')->label(trans('labels.questions_to_show'));
        CRUD::column('final')->label(trans('labels.is_final'))->escaped(false)->limit(-1);

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
        CRUD::setValidation(QuizRequest::class);

        //CRUD::field('id');

        CRUD::addField([  // Select2
           'label'     => trans('labels.course'),
           'type'      => 'select2',
           'name'      => 'course_id', // the db column for the foreign key

           // optional
           'entity'    => 'course', // the method that defines the relationship in your Model
           'model'     => "App\Models\Course", // foreign key model
           'attribute' => 'name', // foreign key attribute that is shown to user
           'default'   => 1, // set the default value of the select2

            // also optional
           'options'   => (function ($query) {
                return $query->orderBy('name', 'ASC')->get();
            }), // force the related options to be a custom query, instead of all(); you can use this to filter the results show in the select
           'wrapper'   => [
               'class' => 'form-group col-md-4'
            ]
        ]);
        CRUD::addField([   // 1-n relationship
            'label'       => trans('labels.lesson'), // Table column heading
            'type'        => "select2_from_ajax_customized",
            'name'        => 'lesson_id', // the column that contains the ID of that connected entity
            'entity'      => 'lesson', // the method that defines the relationship in your Model
            'attribute'   => "name", // foreign key attribute that is shown to user
            'data_source' => url("api/lesson"), // url to controller search function (with /{id} should return model)

            // OPTIONAL
            'delay' => 500, // the minimum amount of time between ajax requests when searching in the field
            'placeholder'             => trans('labels.select_lesson'), // placeholder for the select
            'minimum_input_length'    => 0, // minimum characters to type before querying results
            'model'                   => "App\Models\Lesson", // foreign key model
            'dependencies'            => ['course_id'], // when a dependency changes, this select2 is reset to null
            // 'method'                  => 'GET', // optional - HTTP method to use for the AJAX call (GET, POST)
            'include_all_form_fields' => true, // optional - only send the current field through AJAX (for a smaller payload if you're not using multiple chained select2s)
            'wrapper'   => [
               'class' => 'form-group col-md-4'
            ]
        ]);
        CRUD::addField([
            'name'  => 'questions_to_show',
            'label' => trans('labels.questions_to_show'),
            'type'  => 'number_with_counter',
            'default' => 10,
            'wrapper'   => [
               'class' => 'form-group col-md-2'
            ]
        ]);
        CRUD::addField([   // Checkbox
            'name'  => 'is_final',
            'label' => trans('labels.is_final'),
            'type'  => 'checkbox',
            'wrapper'   => [
               'class' => 'form-group col-md-2'
            ]
        ]);
        //CRUD::field('questions');
        CRUD::addField([   // repeatable
            'name'  => 'questions',
            'label' => trans('labels.questions'),
            'type'  => 'repeatable',
            'fields' => [
                [   // Browse
                    'name'  => 'image',
                    'label' => trans('labels.image'),
                    'type'  => 'browse',
                    'wrapper'   => [
                       'class' => 'form-group col-md-4'
                    ]
                ],
                [
                    'name'  => 'question',
                    'type'  => 'textarea',
                    'label' => trans('labels.question'),
                    'wrapper'   => [
                       'class' => 'form-group col-md-8'
                    ]
                ],
                [   // Table
                    'name'            => 'answers',
                    'label'           => trans('labels.answers'),
                    'type'            => 'table',
                    'entity_singular' => trans('labels.answer'), // used on the "Add X" button
                    'columns'         => [
                        [
                            'label' => trans('labels.answer'),
                            'name'  => 'option',
                            'type' => 'text'
                        ],
                        [
                            'label' => trans('labels.is_true'),
                            'name'  => 'is_true',
                            'type' => 'checkbox'
                        ],
                        [
                            'label' => trans('labels.id'),
                            'name'  => 'id',
                            'type' => 'hidden'
                        ]
                    ],
                    'max' => 20, // maximum rows allowed in the table
                    'min' => 1, // minimum rows allowed in the table
                ],
                [   // Hidden
                    'name'  => 'id',
                    'type'  => 'hidden',
                    'value' => null,
                ]
            ],

            // optional
            'new_item_label'  => trans('labels.add_question'), // customize the text of the button
            'init_rows' => 1, // number of empty rows to be initialized, by default 1
            'min_rows' => 1, // minimum rows allowed, when reached the "delete" buttons will be hidden
            'max_rows' => 100, // maximum rows allowed, when reached the "new item" button will be hidden

        ]);
        //CRUD::field('created_at');
        //CRUD::field('updated_at');

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

    protected function setupShowOperation()
    {
        $this->crud->set('show.setFromDb', false);

        CRUD::column('course_id')->label(trans('labels.course'))->limit(100);
        CRUD::column('lesson_id')->label(trans('labels.lesson'))->limit(100);
        CRUD::column('total_questions')->label(trans('labels.total_questions'));
        CRUD::column('questions_to_show')->label(trans('labels.questions_to_show'));
        CRUD::column('final')->label(trans('labels.is_final'))->escaped(false)->limit(-1);
        CRUD::column('created_at')->label(trans('labels.created'));
        CRUD::column('updated_at')->label(trans('labels.updated'));
        CRUD::column('questions_info')->label(trans('labels.questions'))->escaped(false)->limit(-1);

    }
}
