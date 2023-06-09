<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\LessonRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class LessonCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class LessonCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Lesson::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/lesson');
        CRUD::setEntityNameStrings(trans('nav.lesson'), trans('nav.lessons'));
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        /* Get only lessons assigned to courses from admins or managers division */
        if(backpack_user()->hasRole('Admin') || backpack_user()->hasRole('Manager')){

            $this->crud->denyAccess('delete');
            $this->crud->denyAccess('clone');

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

            $this->crud->addClause('whereIn', 'course_id', $courses);
        }
        //CRUD::column('id');
        CRUD::column('course_id');
        CRUD::column('name');
        CRUD::column('description');
        //CRUD::column('content');
//        CRUD::column('video');
//        CRUD::column('gallery');
//        CRUD::column('banner');
        CRUD::column('sort_order');
        //CRUD::column('created_at');
        //CRUD::column('updated_at');

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
        CRUD::setValidation(LessonRequest::class);

        //CRUD::field('id');
        CRUD::field('course_id')->label(trans('labels.course'));

        CRUD::field('name')->label(trans('labels.name'));

        CRUD::addField([   // Textarea
            'name'  => 'description',
            'label' => trans('labels.description'),
            'type'  => 'textarea'
        ]);

        CRUD::addField([   // TinyMCE
            'name'  => 'content',
            'label' => trans('labels.content'),
            'type'  => 'tinymce',
            // optional overwrite of the configuration array
            // 'options' => [ 'selector' => 'textarea.tinymce',  'skin' => 'dick-light', 'plugins' => 'image,link,media,anchor' ],
        ]);

        CRUD::addField([   // Browse
            'name'  => 'video',
            'label' => trans('labels.video'),
            'type'  => 'browse_multiple',
            'sortable'   => true, // enable/disable the reordering with drag&drop
            'mime_types' => ['video/mp4']
        ]);

        CRUD::addField([   // Browse
            'name'  => 'gallery',
            'label' => trans('labels.gallery'),
            'type'  => 'browse_multiple',
            'sortable'   => true, // enable/disable the reordering with drag&drop
            'mime_types' => ['image/jpeg', 'image/png']
        ]);

        CRUD::addField([   // Browse
            'name'  => 'banner',
            'label' => trans('labels.banner'),
            'type'  => 'browse'
        ]);

        CRUD::addField([   // Number
            'name' => 'sort_order',
            'label' => trans('labels.sort_order'),
            'type' => 'number',
            // optionals
            'attributes' => ["step" => "1", "min" => "1"], // allow decimals
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
}
