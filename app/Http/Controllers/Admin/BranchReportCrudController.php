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
class BranchReportCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Branch::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/report/branch');
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
        // Deny operations
        $this->crud->removeAllButtons();

        //$this->crud->enableDetailsRow();

        $this->crud->addButtonFromModelFunction('line', 'reportSummary', 'reportSummary', 'beginning');
        $this->crud->addButtonFromModelFunction('line', 'reportEvaluations', 'reportEvaluations', 'beginning');

        CRUD::column('company_id')->label(trans('labels.company'));
        CRUD::column('division_id')->label(trans('labels.division'));
        CRUD::column('name')->label(trans('labels.name'));
        CRUD::column('region_id')->label(trans('labels.region'));
        CRUD::column('locality_id')->label(trans('labels.locality'));
        CRUD::column('address')->label(trans('labels.address'));
        //CRUD::column('total_employees')->label(trans('labels.total_employees'));
        CRUD::addColumn([
            'label' => trans('labels.total_employees'),
            'name' => 'total_employees',
            'orderable'  => true,
            'orderLogic' => function ($query, $column, $columnDirection) {
                return $query->withCount('employees')
                ->orderBy('employees_count', $columnDirection);
            }
        ]);

        /* FILTERS */
        $this->crud->addFilter([
            'name'  => 'company_id',
            'type'  => 'select2',
            'label' => trans('labels.company')
        ],  function () {
            return \App\Models\Company::all()->keyBy('id')->pluck('name', 'id')->toArray();
        },
            function ($value) {
            $companies = \App\Models\Company::where('id', $value)->get('id');
            $this->crud->addClause('whereIn', 'company_id', $companies);
        });

        $this->crud->addFilter([
            'name'  => 'division_id',
            'type'  => 'select2',
            'label' => trans('labels.division')
        ],  function () {
            return \App\Models\Division::all()->keyBy('id')->pluck('name', 'id')->toArray();
        },
            function ($value) {
                $divisions = \App\Models\Division::where('id', $value)->get('id');
                $this->crud->addClause('whereIn', 'division_id', $divisions);
            });

        $this->crud->addFilter([
            'name'  => 'id',
            'type'  => 'select2',
            'label' => trans('labels.branch')
        ], function () {
            return \App\Models\Branch::all()->keyBy('id')->pluck('name', 'id')->toArray();
        },
            function ($value) {
                $branches = \App\Models\Branch::where('id', $value)->get('id');
                $this->crud->addClause('whereIn', 'id', $branches);
            });

        $this->crud->addFilter([
            'name'  => 'region_id',
            'type'  => 'select2',
            'label' => trans('labels.region')
        ],  function () {
            return \App\Models\Region::all()->keyBy('id')->pluck('name', 'id')->toArray();
        },
            function ($value) {
                $regions = \App\Models\Region::where('id', $value)->get('id');
                $this->crud->addClause('whereIn', 'region_id', $regions);
            });



        $this->crud->addFilter([
            'name'  => 'locality_id',
            'type'  => 'select2',
            'label' => trans('labels.locality')
        ],  function () {
            return \App\Models\Locality::all()->keyBy('id')->pluck('name', 'id')->toArray();
        },
            function ($value) {
                $localities = \App\Models\Locality::where('id', $value)->get('id');
                $this->crud->addClause('whereIn', 'locality_id', $localities);
            });

        $this->crud->addFilter([
            'name'  => 'address',
            'type'  => 'select2',
            'label' => trans('labels.address')
        ], function () {
            return \App\Models\Branch::all()->keyBy('id')->pluck('address', 'id')->toArray();
        },
            function ($value) {
                $addresses = \App\Models\Branch::where('id', $value)->get('address');
                $this->crud->addClause('whereIn', 'address', $addresses);
            });



        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']);
         */
    }

    protected function showDetailsRow($id)
    {
        $branch = \App\Models\Branch::find($id);
        $courses = [];
        if($branch->employees->count() > 0){
            foreach($branch->employees as $employee){
                if($employee->courses->count() > 0){
                    foreach($employee->courses as $course){
                        if(!isset($courses[$course->id])){
                            $courses[$course->id] = [
                                'name' => $course->name,
                                'employees' => 1,
                                'involved' => [],
                                'passed_sum' => 0,
                                'passed_qty' => 0,
                                'total_sum' => 0,
                                'total_qty' => 0
                            ];
                        } else {
                            $courses[$course->id]['employees'] += 1;
                        }
                    }
                }
                if($employee->reports->count() > 0){
                    foreach($employee->reports as $report){
                        if(null != $report->quiz){
                            $courseId = $report->quiz->course->id;
                            if(isset($courses[$courseId])){
                                $courses[$courseId]['involved'][$employee->id] = true;
                                if($report->passed){
                                    $courses[$courseId]['passed_sum'] += $report->best_mark;
                                    $courses[$courseId]['passed_qty'] += 1;
                                }
                                $courses[$courseId]['total_sum'] += $report->avg_mark;
                                $courses[$courseId]['total_qty'] += 1;
                            }
                        }
                    }
                }

            }
        }


        $markup = "<table class='table table-striped table-hover'>";
        if(!empty($courses)){
            $markup .= "<tr>";
                $markup .= "<th>Курс</th>";
                $markup .= "<th>На курсе</th>";
                $markup .= "<th>Активных</th>";
                $markup .= "<th>AVG (Пройдено)</th>";
                $markup .= "<th>AVG (Общая)</th>";
            $markup .= "</tr>";
            foreach($courses as $course){
                $markup .= "<tr>";
                    $markup .= "<td>";
                        $markup .= $course['name'];
                    $markup .= "</td>";
                    $markup .= "<td>";
                        $markup .= $course['employees'];
                    $markup .= "</td>";
                    $markup .= "<td>";
                        $markup .= count($course['involved']);
                    $markup .= "</td>";
                    $markup .= "<td>";
                        $markup .= $course['passed_qty'] > 0 ? round($course['passed_sum'] / $course['passed_qty'], 2) : 0;
                    $markup .= "</td>";
                    $markup .= "<td>";
                        $markup .= $course['total_qty'] > 0 ? round($course['total_sum'] / $course['total_qty'], 2) : 0;
                    $markup .= "</td>";
                $markup .= "</tr>";
            }
        }
        $markup .= "</table>";
        return $markup;

    }
}
