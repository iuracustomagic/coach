<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SkillRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class SkillCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class SkillCrudController extends CrudController
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
        if(!backpack_user()->hasRole('SuperAdmin')) {
            $this->crud->denyAccess('update');
            $this->crud->denyAccess('create');
            $this->crud->denyAccess('delete');


        }
        CRUD::setModel(\App\Models\Skill::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/skill');
        CRUD::setEntityNameStrings(trans('nav.skill'), trans('nav.skills'));
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {

        CRUD::column('name')->label(trans('labels.name'));

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
        CRUD::setValidation(SkillRequest::class);

        CRUD::addField([   // 1-n relationship
            'label'       => trans('labels.skill_name'), // Table column heading
            'type'        => "text",
            'name'        => 'name', // the column that contains the ID of that connected entity

        ]);

        CRUD::addField([   // 1-n relationship
            'label'       => trans('labels.criterias'), // Table column heading
            'name'        => 'criteria', // the column that contains the ID of that connected entity
            'type'  => 'repeatable',
            'fields' => [
                [
                    'label'       => trans('labels.criteria'), // Table column heading
                    'type' => "text",
                    'name' => 'criteria', // the method on your model that defines the relationship

                ]
        ]]);
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
