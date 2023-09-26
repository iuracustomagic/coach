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
class BranchCrudController extends CrudController
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
        if(!backpack_user()->can('HandleBranches')) {
            $this->crud->denyAccess('update');
            $this->crud->denyAccess('create');
            $this->crud->denyAccess('delete');
        }

        CRUD::setModel(\App\Models\Branch::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/branch');
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
        // Deny deleting
        if(!backpack_user()->hasRole('SuperAdmin')){
            $this->crud->denyAccess('delete');
        }

        //$this->crud->enableDetailsRow();

        CRUD::column('company_id')->label(trans('labels.company'));
        CRUD::column('division_id')->label(trans('labels.division'));
        CRUD::column('name')->label(trans('labels.name'));
        CRUD::column('region_id')->label(trans('labels.region'));
        CRUD::column('locality_id')->label(trans('labels.locality'));
        CRUD::column('address')->label(trans('labels.address'));

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
        CRUD::setValidation(BranchRequest::class);

        //CRUD::field('id');
        CRUD::addField([  // Select2
           'label'     => trans('labels.company'),
           'type'      => 'select2',
           'name'      => 'company_id', // the db column for the foreign key

           // optional
           'entity'    => 'company', // the method that defines the relationship in your Model
           'model'     => "App\Models\Company", // foreign key model
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
            'label'       => trans('labels.division'), // Table column heading
            'type'        => "select2_from_ajax",
            'name'        => 'division_id', // the column that contains the ID of that connected entity
            'entity'      => 'division', // the method that defines the relationship in your Model
            'attribute'   => "name", // foreign key attribute that is shown to user
            'data_source' => url("api/division"), // url to controller search function (with /{id} should return model)

            // OPTIONAL
            'delay' => 500, // the minimum amount of time between ajax requests when searching in the field
            'placeholder'             => "Select a division", // placeholder for the select
            'minimum_input_length'    => 0, // minimum characters to type before querying results
            'model'                   => "App\Models\Division", // foreign key model
            'dependencies'            => ['company_id'], // when a dependency changes, this select2 is reset to null
            // 'method'                  => 'GET', // optional - HTTP method to use for the AJAX call (GET, POST)
            'include_all_form_fields' => true, // optional - only send the current field through AJAX (for a smaller payload if you're not using multiple chained select2s)
            'wrapper'   => [
                'class' => 'form-group col-md-4'
            ]
        ]);
        //CRUD::field('division_id');
        CRUD::field('name')->label(trans('labels.name'))->wrapper(['class' => 'form-group col-md-4']);
        //CRUD::field('region_id');
        CRUD::addField([  // Select2
           'label'     => trans('labels.region'),
           'type'      => 'select2',
           'name'      => 'region_id', // the db column for the foreign key

           // optional
           'entity'    => 'region', // the method that defines the relationship in your Model
           'model'     => "App\Models\Region", // foreign key model
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
            'label'       => trans('labels.locality'), // Table column heading
            'type'        => "select2_from_ajax",
            'name'        => 'locality_id', // the column that contains the ID of that connected entity
            'entity'      => 'locality', // the method that defines the relationship in your Model
            'attribute'   => "name", // foreign key attribute that is shown to user
            'data_source' => url("api/locality"), // url to controller search function (with /{id} should return model)

            // OPTIONAL
            'delay' => 500, // the minimum amount of time between ajax requests when searching in the field
            'placeholder'             => "Select locality", // placeholder for the select
            'minimum_input_length'    => 0, // minimum characters to type before querying results
            'model'                   => "App\Models\Locality", // foreign key model
            'dependencies'            => ['region_id'], // when a dependency changes, this select2 is reset to null
            // 'method'                  => 'GET', // optional - HTTP method to use for the AJAX call (GET, POST)
            'include_all_form_fields' => true, // optional - only send the current field through AJAX (for a smaller payload if you're not using multiple chained select2s)
            'wrapper'   => [
                'class' => 'form-group col-md-4'
            ]
        ]);
        CRUD::field('address')->label(trans('labels.address'))->wrapper(['class' => 'form-group col-md-4']);

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
        //$quizzes = \App\Models\Quiz::where('course_id', $course->id)->get()->pluck('id')->toArray();
        $branch = \App\Models\Branch::find($id);
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
        }
    }
}
