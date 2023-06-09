<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\DivisionRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

use App\Models\Traits\LimitAccessAccordingToUserPermissions; 

/**
 * Class DivisionCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class DivisionCrudController extends CrudController
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

        CRUD::setModel(\App\Models\Division::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/division');
        CRUD::setEntityNameStrings(trans('nav.division'), trans('nav.divisions'));
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

        CRUD::column('name')->label(trans('labels.name'));
        CRUD::column('company_id')->label(trans('labels.company'));

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
        CRUD::setValidation(DivisionRequest::class);

        //CRUD::field('id');
        CRUD::field('name');
        CRUD::field('company_id');
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
