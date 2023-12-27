<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\EvaluationPaperRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\App;

/**
 * Class EvaluationPaperCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class EvaluationPaperCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CloneOperation;

    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation { store as traitStore; }
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation { update as traitUpdate; }

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
            $this->crud->denyAccess('clone');

        }
        CRUD::setModel(\App\Models\EvaluationPaper::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/evaluation-paper');
        CRUD::setEntityNameStrings(trans('nav.evaluation_paper'), trans('nav.evaluation_papers'));
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {

//        $this->crud->addColumn([
//            'name'      => 'profession_id',
//            'type'      => 'select',
//            'label'     => trans('labels.profession'),
//            'attribute' => 'name',
//            'model'     => "App\Models\Profession",
//        ]);

        if(App::getLocale() == 'ru') {
            $this->crud->addColumn([
                'name'      => 'profession_id',
                'type'      => 'select',
                'label'     => trans('labels.profession'),
                'attribute' => 'name',
                'model'     => "App\Models\Profession",
            ]);
        } else if(App::getLocale() == 'ro') {
            $this->crud->addColumn([
                'name'      => 'profession_id',
                'type'      => 'select',
                'label'     => trans('labels.profession'),
                'attribute' => 'name_ro',
                'model'     => "App\Models\Profession",
            ]);
        }else  {
            $this->crud->addColumn([
                'name'      => 'profession_id',
                'type'      => 'select',
                'label'     => trans('labels.profession'),
                'attribute' => 'name_en',
                'model'     => "App\Models\Profession",
            ]);
        }

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
        CRUD::setValidation(EvaluationPaperRequest::class);

        //CRUD::field('profession_id')->label(trans('labels.profession'));

        if(App::getLocale() == 'ru') {
            CRUD::addField([   // 1-n relationship
                'label'       => trans('labels.profession'), // Table column heading
                'type'        => "select2_from_ajax",
                'name'        => 'profession_id', // the column that contains the ID of that connected entity
                'entity'      => 'profession', // the method that defines the relationship in your Model
                'attribute'   => "name", // foreign key attribute that is shown to user
                'data_source' => url("api/profession"), // url to controller search function (with /{id} should return model)
                'placeholder'             => trans('labels.select_profession'), // placeholder for the select
                'minimum_input_length'    => 0 // minimum characters to type before querying results
            ]);
        } elseif (App::getLocale() == 'ro') {
            CRUD::addField([   // 1-n relationship
                'label'       => trans('labels.profession'), // Table column heading
                'type'        => "select2_from_ajax",
                'name'        => 'profession_id', // the column that contains the ID of that connected entity
                'entity'      => 'profession', // the method that defines the relationship in your Model
                'attribute'   => "name_ro", // foreign key attribute that is shown to user
                'data_source' => url("api/profession"), // url to controller search function (with /{id} should return model)
                'placeholder'             => trans('labels.select_profession'), // placeholder for the select
                'minimum_input_length'    => 0 // minimum characters to type before querying results
            ]);
        } else {
            CRUD::addField([   // 1-n relationship
                'label'       => trans('labels.profession'), // Table column heading
                'type'        => "select2_from_ajax",
                'name'        => 'profession_id', // the column that contains the ID of that connected entity
                'entity'      => 'profession', // the method that defines the relationship in your Model
                'attribute'   => "name_en", // foreign key attribute that is shown to user
                'data_source' => url("api/profession"), // url to controller search function (with /{id} should return model)
                'placeholder'             => trans('labels.select_profession'), // placeholder for the select
                'minimum_input_length'    => 0 // minimum characters to type before querying results
            ]);
        }


        CRUD::addField([   // Hidden
            'name'  => 'criteria_id',
            'type'  => 'hidden',
            'value' => 0,
        ]);

        CRUD::addField([   // repeatable
            'name'  => 'structure',
            'label' => trans('labels.evaluation_paper'),
            'type'  => 'repeatable',
            'fields' => [
                [
                    'label'       => trans('labels.criteria'), // Table column heading
                    'type' => "relationship",
                    'name' => 'criteria', // the method on your model that defines the relationship
                    'model'                   => "App\Models\EvaluationCriteria", // foreign key model
                    'data_source' => url("api/criterias"), // url to controller search function (with /{id} should return model)
                    'delay' => 500,
                    'attribute'   =>App::getLocale() == 'ru' ? "name" : "name_ro",// the minimum amount of time between ajax requests when searching in the field
                    'placeholder'             => trans('labels.select_criteria'), // placeholder for the select
                    'minimum_input_length'    => 0, // minimum characters to type before querying results
                    'method'                  => 'GET',
                    'ajax' => true,
                    'inline_create' => [
                        'entity' => 'criteria',
                        'modal_route' => route('evaluation-criteria-inline-create'), // InlineCreate::getInlineCreateModal()
                        'create_route' =>  route('evaluation-criteria-inline-create-save'), // InlineCreate::storeInlineCreate()
                    ], // assumes the URL will be "/admin/category/inline/create"
                    'include_all_form_fields' => false
                ],
                [   // Table
                    'name'            => 'points',
                    'label'           => trans('labels.points'),
                    'type'            => 'table',
                    'entity_singular' => trans('labels.point'), // used on the "Add X" button
                    'columns'         => [
                        [
                            'label' => trans('labels.point'),
                            'name'  => 'title',
                            'type' => 'text'
                        ],
                        [
                            'label' => trans('labels.point')."-ro",
                            'name'  => 'title_ro',
                            'type' => 'text'
                        ],
                        [
                            'label' => trans('labels.point')."-en",
                            'name'  => 'title_en',
                            'type' => 'text'
                        ],
                        [
                            'label' => trans('labels.mark'),
                            'name'  => 'mark',
                            'type' => 'text'
                        ],
                    ],
                    'max' => 10, // maximum rows allowed in the table
                    'min' => 1, // minimum rows allowed in the table
                ],
            ],

            // optional
            'new_item_label'  => trans('labels.add_criteria'), // customize the text of the button
            'init_rows' => 1, // number of empty rows to be initialized, by default 1
            'min_rows' => 1, // minimum rows allowed, when reached the "delete" buttons will be hidden
            'max_rows' => 100, // maximum rows allowed, when reached the "new item" button will be hidden

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

    protected function fixStructure($structure)
    {
        $criteriasList = \App\Models\EvaluationCriteria::all()->pluck(
            'name',
            'id'
        );
        $structure = json_decode($structure);
        foreach($structure as $skey => $criteria){
            if(!isset($structure[$skey]->code)){
                $structure[$skey]->code = \Illuminate\Support\Str::random(12);
            }
            $points = json_decode($criteria->points);
            foreach($points as $pkey => $point){
                $points[$pkey]->title = preg_replace('/[[:cntrl:]]/', '', trim($points[$pkey]->title));
                if(isset($points[$pkey]->title_ro)) {
                    $points[$pkey]->title_ro = preg_replace('/[[:cntrl:]]/', '', trim($points[$pkey]->title_ro));
                }
                if(isset($points[$pkey]->title_en)) {
                    $points[$pkey]->title_en = preg_replace('/[[:cntrl:]]/', '', trim($points[$pkey]->title_en));
                }

                if(!isset($points[$pkey]->code)){
                    $points[$pkey]->code = \Illuminate\Support\Str::random(12);
                }
                if(isset($points[$pkey]->mark)){
                    $points[$pkey]->mark = floatval(str_replace(',', '.', $points[$pkey]->mark));
                } else {
                    $points[$pkey]->mark = 0;
                }
            }
            $structure[$skey]->points = json_encode($points, JSON_UNESCAPED_UNICODE);
        }
        return $structure;
    }

    public function store()
    {
        $structure = $this->crud->getRequest()->request->get('structure');
        $structure = $this->fixStructure($structure);
        $this->crud->getRequest()->request->add(['structure'=> json_encode($structure, JSON_UNESCAPED_UNICODE)]);
        $response = $this->traitStore();
        return $response;
    }

    public function update()
    {
        $structure = $this->crud->getRequest()->request->get('structure');
        $structure = $this->fixStructure($structure);
        $this->crud->getRequest()->request->add(['structure'=> json_encode($structure, JSON_UNESCAPED_UNICODE)]);
        $response = $this->traitUpdate();
        return $response;
    }
    protected function setupShowOperation()
    {
        $this->crud->set('show.setFromDb', false);

        if(App::getLocale() == 'ru') {
            $this->crud->addColumn([
                'name'      => 'profession_id',
                'type'      => 'select',
                'label'     => trans('labels.profession'),
                'attribute' => 'name',
                'model'     => "App\Models\Profession",
            ]);
        } else if(App::getLocale() == 'ro') {
            $this->crud->addColumn([
                'name'      => 'profession_id',
                'type'      => 'select',
                'label'     => trans('labels.profession'),
                'attribute' => 'name_ro',
                'model'     => "App\Models\Profession",
            ]);
        }else  {
            $this->crud->addColumn([
                'name'      => 'profession_id',
                'type'      => 'select',
                'label'     => trans('labels.profession'),
                'attribute' => 'name_en',
                'model'     => "App\Models\Profession",
            ]);
        }

//        $this->crud->addColumn([   // repeatable
//            'name'  => 'structure',
//            'label' => trans('labels.evaluation_paper'),
//            'type' => 'table',
//            'columns' => [
//                'address_type_id'  =>  __('models/addresses.fields.address_type'),
//                'address_type.name'  =>  __('models/addresses.fields.address_type'),
//                'address1'  => __('models/addresses.fields.address1'),
//                'address2'  => __('models/addresses.fields.address2'),
//                'city'  => __('models/addresses.fields.address2'),
//                'postal_code'  => __('models/addresses.fields.address2'),
//                'country.name'  => __('models/countries.singular'),
//            ],
//
//
//        ]);

//        CRUD::column('course_id')->label(trans('labels.course'))->limit(100);
//        CRUD::column('lesson_id')->label(trans('labels.lesson'))->limit(100);
//        CRUD::column('total_questions')->label(trans('labels.total_questions'));
//        CRUD::column('questions_to_show')->label(trans('labels.questions_to_show'));
//        CRUD::column('final')->label(trans('labels.is_final'))->escaped(false)->limit(-1);
//        CRUD::column('created_at')->label(trans('labels.created'));
//        CRUD::column('updated_at')->label(trans('labels.updated'));
//        CRUD::column('questions_info')->label(trans('labels.questions'))->escaped(false)->limit(-1);

    }
}
