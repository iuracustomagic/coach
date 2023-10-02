<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\EvaluationCriteriaRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\App;

/**
 * Class EvaluationCriteriaCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class EvaluationCriteriaCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\InlineCreateOperation;
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
        if(!backpack_user()->hasRole('SuperAdmin')) {
            $this->crud->denyAccess('update');
            $this->crud->denyAccess('create');
            $this->crud->denyAccess('delete');

        }
        CRUD::setModel(\App\Models\EvaluationCriteria::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/evaluation-criteria');
        CRUD::setEntityNameStrings(trans('labels.evaluation_criteria'), trans('labels.evaluation_criterias'));
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
        $this->crud->denyAccess('show');
      //  $this->crud->denyAccess('delete');
        CRUD::column('name')->label(trans('labels.name'))->limit(50);
//        if(App::getLocale() == 'ru') {
//            CRUD::column('name')->label(trans('labels.name'))->limit(50);
//        } else if(App::getLocale() == 'ro') {
//            CRUD::column('name_ro')->label(trans('labels.name'))->limit(50);
//        }else  {
//            CRUD::column('name_en')->label(trans('labels.name'))->limit(50);
//        }

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
        CRUD::setValidation(EvaluationCriteriaRequest::class);

        CRUD::addField([
            'name'  => 'name',
            'label' => trans('labels.name').'-ru',
            'wrapper'   => [
                'class'      => 'form-group col-md-4'
            ],
        ]);
//        CRUD::addField([
//            'name'  => 'name_ro',
//            'label' => trans('labels.name').'-ro',
//            'wrapper'   => [
//                'class'      => 'form-group col-md-4'
//            ],
//        ]);
//        CRUD::addField([
//            'name'  => 'name_en',
//            'label' => trans('labels.name').'-en',
//            'wrapper'   => [
//                'class'      => 'form-group col-md-4'
//            ],
//        ]);


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
