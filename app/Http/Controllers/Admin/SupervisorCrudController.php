<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SupervisorRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

use App\Models\Traits\LimitAccessAccordingToUserPermissions; 

/**
 * Class SupervisorCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class SupervisorCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    //use LimitAccessAccordingToUserPermissions;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        //$this->denyAccessIfNoPermission();

        CRUD::setModel(\App\Models\Supervisor::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/supervisor');
        CRUD::setEntityNameStrings('supervisor', 'supervisors');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('id');
        CRUD::column('name');
        CRUD::column('subdivision_id');
        CRUD::column('user_id');
        //CRUD::column('updated_at');
        //CRUD::column('created_at');

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
        CRUD::setValidation(SupervisorRequest::class);

        //CRUD::field('id');
        CRUD::field('name');
        CRUD::field('subdivision_id');
        CRUD::addField(
            [  // Select2
               'label'     => "User",
               'type'      => 'select2',
               'name'      => 'user_id', // the db column for the foreign key

               // optional
               'entity'    => 'user', // the method that defines the relationship in your Model
               'model'     => "App\Models\User", // foreign key model
               'attribute' => 'name', // foreign key attribute that is shown to user
               //'default'   => 2, // set the default value of the select2

                // also optional
               'options'   => (function ($query) {
                    return $query->orderBy('name', 'ASC')
                    ->join('model_has_roles', 'model_has_roles.model_id', '=', 'id')
                    ->where('model_has_roles.role_id', 2) // id 2 - Supervizer
                    ->get();
                }), // force the related options to be a custom query, instead of all(); you can use this to filter the results show in the select
            ]
        );
        //CRUD::field('updated_at');
        //CRUD::field('created_at');

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
}
