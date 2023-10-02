<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\LessonRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\App;

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
        if(!backpack_user()->can('HandleLessons')) {
            $this->crud->denyAccess('update');
            $this->crud->denyAccess('create');
            $this->crud->denyAccess('delete');
            $this->crud->denyAccess('clone');
        }
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
//        CRUD::column('course_id')->label(trans('labels.course'));

        if(App::getLocale() == 'ru') {
            $this->crud->addColumn([
                'name'      => 'course_id',
                'type'      => 'select',
                'label'     => trans('labels.course'),
                'attribute' => 'name',
                'model'     => "App\Models\Course",
            ]);
        } else if(App::getLocale() == 'ro') {
            $this->crud->addColumn([
                'name'      => 'course_id',
                'type'      => 'select',
                'label'     => trans('labels.course'),
                'attribute' => 'name_ro',
                'model'     => "App\Models\Course",
            ]);
        }else  {
            $this->crud->addColumn([
                'name'      => 'course_id',
                'type'      => 'select',
                'label'     => trans('labels.course'),
                'attribute' => 'name_en',
                'model'     => "App\Models\Course",
            ]);
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


        CRUD::column('sort_order')->label(trans('labels.sort_order'));


        /* FILTERS */

        $this->crud->addFilter(
            [
                'name'  => 'course',
                'type'  => 'select2',
                'label' =>trans('labels.course'),
            ],
            function () {
                return \App\Models\Course::all()->keyBy('id')->pluck('name', 'id')->toArray();
            },
            function ($value) { // if the filter is active
                $this->crud->addClause('where', 'course_id', $value);
            }
        );
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
            'wrapper'   => [
                'class'      => 'form-group col-md-4'
            ],

        ]);
        CRUD::addField([   // Textarea
            'name'  => 'description_ro',
            'label' => trans('labels.description').'-ro',
            'type'  => 'textarea',
            'wrapper'   => [
                'class'      => 'form-group col-md-4'
            ],

        ]);
        CRUD::addField([   // Textarea
            'name'  => 'description_en',
            'label' => trans('labels.description').'-en',
            'type'  => 'textarea',
            'wrapper'   => [
                'class'      => 'form-group col-md-4'
            ],
        ]);

        CRUD::addField([   // TinyMCE
            'name'  => 'content',
            'label' => trans('labels.content').'-ru',
            'type'  => 'tinymce',
        ]);
        CRUD::addField([   // TinyMCE
            'name'  => 'content_ro',
            'label' => trans('labels.content')."-ro",
            'type'  => 'tinymce',
        ]);
        CRUD::addField([   // TinyMCE
            'name'  => 'content_en',
            'label' => trans('labels.content').'-en',
            'type'  => 'tinymce',
        ]);

        CRUD::addField([   // Browse
            'name'  => 'video',
            'label' => trans('labels.video').'-ru',
            'type'  => 'browse_multiple',
            'sortable'   => true, // enable/disable the reordering with drag&drop
            'mime_types' => ['video/mp4'],
            'wrapper'   => [
                'class' => 'form-group col-md-4'
            ]
        ]);
        CRUD::addField([   // Browse
            'name'  => 'video_ro',
            'label' => trans('labels.video').'-ro',
            'type'  => 'browse_multiple',
            'sortable'   => true, // enable/disable the reordering with drag&drop
            'mime_types' => ['video/mp4'],
            'multiple'   => true,
            'wrapper'   => [
                'class' => 'form-group col-md-4'
            ]
        ]);
        CRUD::addField([   // Browse
            'name'  => 'video_en',
            'label' => trans('labels.video').'-en',
            'type'  => 'browse_multiple',
            'sortable'   => true, // enable/disable the reordering with drag&drop
            'mime_types' => ['video/mp4'],
            'wrapper'   => [
                'class' => 'form-group col-md-4'
            ]
        ]);

        CRUD::addField([   // Browse
            'name'  => 'gallery',
            'label' => trans('labels.gallery').'-ru',
            'type'  => 'browse_multiple',
            'sortable'   => true, // enable/disable the reordering with drag&drop
            'mime_types' => ['image/jpeg', 'image/png'],
            'wrapper'   => [
                'class' => 'form-group col-md-4'
            ]
        ]);
        CRUD::addField([   // Browse
            'name'  => 'gallery_ro',
            'label' => trans('labels.gallery').'-ro',
            'type'  => 'browse_multiple',
            'sortable'   => true, // enable/disable the reordering with drag&drop
            'mime_types' => ['image/jpeg', 'image/png'],
            'wrapper'   => [
                'class' => 'form-group col-md-4'
            ]
        ]);
        CRUD::addField([   // Browse
            'name'  => 'gallery_en',
            'label' => trans('labels.gallery').'-en',
            'type'  => 'browse_multiple',
            'sortable'   => true, // enable/disable the reordering with drag&drop
            'mime_types' => ['image/jpeg', 'image/png'],
            'wrapper'   => [
                'class' => 'form-group col-md-4'
            ]
        ]);

        CRUD::addField([   // Browse
            'name'  => 'banner',
            'label' => trans('labels.banner').'-ru',
            'type'  => 'browse',
            'wrapper'   => [
                'class' => 'form-group col-md-4'
            ]
        ]);
        CRUD::addField([   // Browse
            'name'  => 'banner_ro',
            'label' => trans('labels.banner').'-ro',
            'type'  => 'browse',
            'wrapper'   => [
                'class' => 'form-group col-md-4'
            ]
        ]);
        CRUD::addField([   // Browse
            'name'  => 'banner_en',
            'label' => trans('labels.banner').'-en',
            'type'  => 'browse',
            'wrapper'   => [
                'class' => 'form-group col-md-4'
            ]
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
