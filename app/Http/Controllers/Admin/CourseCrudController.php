<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CourseRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\App;

/**
 * Class CourseCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class CourseCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation { update as traitUpdate; }
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        if(!backpack_user()->can('HandleCourses')) {
            $this->crud->denyAccess('update');
            $this->crud->denyAccess('create');
            $this->crud->denyAccess('delete');
        }
        CRUD::setModel(\App\Models\Course::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/course');
        CRUD::setEntityNameStrings(trans('nav.course'), trans('nav.courses'));
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        $this->crud->enableDetailsRow();
        $this->crud->set('show.setFromDb', false);

        /* Get only courses from admins or managers division */
        if(backpack_user()->hasRole('Admin') || backpack_user()->hasRole('Manager')){

            $this->crud->denyAccess('delete');

            /*if(backpack_user()->hasRole('Manager')){
                $this->crud->denyAccess('update');
            }*/

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

            $this->crud->addClause('whereIn', 'id', $courses);
        }

        if(App::getLocale() == 'ru') {
            CRUD::column('name')->label(trans('labels.name'))->limit(50);
        } else if(App::getLocale() == 'ro') {
            CRUD::column('name_ro')->label(trans('labels.name'))->limit(50);
        }else  {
            CRUD::column('name_en')->label(trans('labels.name'))->limit(50);
        }

        if(App::getLocale() == 'ru') {
            CRUD::column('description')->label(trans('labels.description'));
        } else if(App::getLocale() == 'ro') {
            CRUD::column('description_ro')->label(trans('labels.description'));
        }else  {
            CRUD::column('description_en')->label(trans('labels.description'));
        }


        CRUD::addColumn([
            'name'  => 'professions_list',
            'label' => trans('labels.profession'), // Table column heading
            'type'  => 'model_function',
            'function_name' => 'getProfessionsList', // the method in your Model
        ]);
        CRUD::column('sort_order')->label(trans('labels.sort_order'));



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
        CRUD::setValidation(CourseRequest::class);

        CRUD::addField([
            'name'  => 'name',
            'label' => trans('labels.name').'-ru',
            'wrapper'   => [
                'class'      => 'form-group col-md-4'
                ],
            ]);
        CRUD::addField([
            'name'  => 'name_ro',
            'label' => trans('labels.name').'-ro',
            'wrapper'   => [
                'class'      => 'form-group col-md-4'
            ],
        ]);
        CRUD::addField([
            'name'  => 'name_en',
            'label' => trans('labels.name').'-en',
            'wrapper'   => [
                'class'      => 'form-group col-md-4'
            ],
        ]);


        CRUD::addField([
            'name'  => 'description',
            'label' => trans('labels.description').'-ru',
            'type'  => 'textarea',

        ]);
        CRUD::addField([   // Textarea
            'name'  => 'description_ro',
            'label' => trans('labels.description').'-ro',
            'type'  => 'textarea',

        ]);
        CRUD::addField([   // Textarea
            'name'  => 'description_en',
            'label' => trans('labels.description').'-en',
            'type'  => 'textarea'
        ]);

        CRUD::addField([   // 1-n relationship
            'label'       => trans('labels.company'), // Table column heading
            'type'        => "select2_from_ajax_multiple",
            'name'        => 'companies', // the column that contains the ID of that connected entity
            'entity'      => 'companies', // the method that defines the relationship in your Model
            'attribute'   => "name", // foreign key attribute that is shown to user
            'data_source' => url("api/company"), // url to controller search function (with /{id} should return model)

            // OPTIONAL
            'delay' => 500, // the minimum amount of time between ajax requests when searching in the field
            'placeholder'             => trans('labels.select_company'), // placeholder for the select
            'minimum_input_length'    => 0, // minimum characters to type before querying results
            'model'                   => "App\Models\Company", // foreign key model
            // 'method'                  => 'GET', // optional - HTTP method to use for the AJAX call (GET, POST)
            //'include_all_form_fields' => true, // optional - only send the current field through AJAX (for a smaller payload if you're not using multiple chained select2s)
            'wrapper'   => [
               'class' => 'form-group col-md-4'
            ]
        ]);

        CRUD::addField([   // 1-n relationship
            'label'       => trans('labels.division'), // Table column heading
            'type'        => "select2_from_ajax_multiple",
            'name'        => 'divisions', // the column that contains the ID of that connected entity
            'entity'      => 'divisions', // the method that defines the relationship in your Model
            'attribute'   => "name", // foreign key attribute that is shown to user
            'data_source' => url("api/division"), // url to controller search function (with /{id} should return model)

            // OPTIONAL
            'delay' => 500, // the minimum amount of time between ajax requests when searching in the field
            'placeholder'             => trans('labels.select_division'), // placeholder for the select
            'minimum_input_length'    => 0, // minimum characters to type before querying results
            'model'                   => "App\Models\Division", // foreign key model
            'dependencies'            => ['company_id'], // when a dependency changes, this select2 is reset to null
            // 'method'                  => 'GET', // optional - HTTP method to use for the AJAX call (GET, POST)
            'include_all_form_fields' => true, // optional - only send the current field through AJAX (for a smaller payload if you're not using multiple chained select2s)
            'wrapper'   => [
               'class' => 'form-group col-md-4'
            ]
        ]);

        CRUD::addField([   // 1-n relationship
            'label'       => trans('labels.profession'), // Table column heading
            'type'        => "select2_from_ajax_multiple",
            'name'        => 'professions', // the column that contains the ID of that connected entity
            'entity'      => 'professions', // the method that defines the relationship in your Model
            'attribute'   => "name", // foreign key attribute that is shown to user
            'data_source' => url("api/profession"), // url to controller search function (with /{id} should return model)

            // OPTIONAL
            'delay' => 500, // the minimum amount of time between ajax requests when searching in the field
            'placeholder'             => trans('labels.select_division'), // placeholder for the select
            'minimum_input_length'    => 0, // minimum characters to type before querying results
            'model'                   => "App\Models\Profession", // foreign key model
            'wrapper'   => [
               'class' => 'form-group col-md-4'
            ]
        ]);

        //CRUD::field('profession_id')->label(trans('labels.profession'))->wrapper(['class' => 'form-group col-md-4']);

        CRUD::addField([   // Number
            'name' => 'sort_order',
            'label' => trans('labels.sort_order'),
            'type' => 'number',
            // optionals
            'attributes' => ["step" => "1", "min" => "1"], // allow decimals
        ]);

        CRUD::addField([   // Browse
            'name'  => 'banner',
            'label' => trans('labels.banner'),
            'type'  => 'browse'


        ]);



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

    public function update()
    {
        // do something before validation, before save, before everything; for example:
        // $this->crud->addField(['type' => 'hidden', 'name' => 'author_id']);
        // $this->crud->removeField('password_confirmation');

        $response = $this->traitUpdate();
        // do something after save
        $course = \App\Models\Course::find($this->crud->getRequest()->input('id'));
        $subscribed = \App\Models\UserAvailableCourses::where('course_id', $course->id)->pluck('course_id', 'user_id');
        if(!empty($course->professions)){
            foreach($course->professions as $profession){
                foreach($profession->employees as $employee){
                    $pivot = \App\Models\UserAvailableCourses::where(['user_id' => $employee->id, 'course_id' => $course->id])->first();
                    if(empty($pivot)){
                        $pivot = new \App\Models\UserAvailableCourses();
                        $pivot->user_id = $employee->id;
                        $pivot->course_id = $course->id;
                        $pivot->save();
                    } else {
                        unset($subscribed[$pivot->user_id]);
                    }
                }
            }
        }
        foreach($subscribed as $user_id => $course_id){
            $pivot = \App\Models\UserAvailableCourses::where(['user_id' => $user_id, 'course_id' => $course_id])->delete();
        }
        return $response;
    }

    protected function showDetailsRow($id)
    {
        $course = \App\Models\Course::find($id);
        if(!empty($course) && !empty($course->lessons)){
            $markup = "<table>";
            $markup .= "<tr><th>Lesson</th></tr>";

            foreach($course->lessons as $lesson){
                $markup .= "<tr><td>" . $lesson->name . "</td></tr>";
            }

            $markup .= "</table>";
            return $markup;
        } else {
            return "Нет доступных данных";
        }
    }
}
