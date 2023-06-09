<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\PermissionManager\app\Http\Requests\UserStoreCrudRequest as StoreRequest;
use Backpack\PermissionManager\app\Http\Requests\UserUpdateCrudRequest as UpdateRequest;
use Illuminate\Support\Facades\Hash;

class UserReportCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation { store as traitStore; }
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation { update as traitUpdate; }
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;

    public function setup()
    {
        $this->crud->setModel(config('backpack.permissionmanager.models.user'));
        $this->crud->setEntityNameStrings(trans('labels.employee'), trans('labels.employees'));
        $this->crud->setRoute(backpack_url('/report/user'));
    }

    public function setupListOperation()
    {
        // Deny operations
        $this->crud->removeAllButtons();

//        $this->crud->enableResponsiveTable();

        if(backpack_user()->hasRole('SuperAdmin')){
			// add a button whose HTML is returned by a method in the CRUD model
			$this->crud->addButtonFromModelFunction('line', 'evaluation_marks', 'evaluation_marks', 'beginning');
            $this->crud->enableExportButtons();

		}

        /* Get only users from admins divisions */
        if(backpack_user()->hasRole('Admin')){
            $employees = [];
            if(!empty(backpack_user()->divisions)){
                foreach(backpack_user()->divisions as $division){
                    if(!empty($division->employees)){
                        foreach($division->employees as $employee){
                            $employees[] = $employee->id;
                        }
                    }
                }
            }
            $this->crud->addClause('whereIn', 'id', $employees);
        }

        /* Get only users from managers branches */
        if(backpack_user()->hasRole('Manager')){
            $employees = [];
            if(!empty(backpack_user()->branches)){
                foreach(backpack_user()->branches as $branch){
                    if(!empty($branch->employees)){
                        foreach($branch->employees as $employee){
                            $employees[] = $employee->id;
                        }
                    }
                }
            }
            $this->crud->addClause('whereIn', 'id', $employees);
        }

        $this->crud->addColumns([
            [
                'name'  => 'name',
                'label' => trans('backpack::permissionmanager.name'),
                'type'  => 'text',
            ],
            [
                'name'  => 'total_available',
                'label' => trans('labels.total_available'),
                'type'  => 'text'
            ],
            [
                'name'  => 'total_passed',
                'label' => trans('labels.total_passed'),
                'type'  => 'text'
            ],
            [
                'name'  => 'total_in_progress',
                'label' => trans('labels.total_in_progress'),
                'type'  => 'text'
            ],
            [
                'name'  => 'avg_passed',
                'label' => trans('labels.avg_passed'),
                'type'  => 'text'
            ],
            [
                'name'  => 'avg_total',
                'label' => trans('labels.avg_total'),
                'type'  => 'text'
            ],
            [
                'name'  => 'current_month_evaluation_points',
                'label' => trans('labels.avg_points_m'),
                'type'  => 'text'
            ],
            [
                'name'  => 'current_month_evaluation_mark',
                'label' => trans('labels.avg_mark_m'),
                'type'  => 'text'
            ],
            [
                'name'  => 'evaluation_points', // may be better to show for last 6 month, not for all times
                'label' => trans('labels.avg_points'),
                'type'  => 'text'
            ],
            [
                'name'  => 'evaluation_mark', // may be better to show for last 6 month, not for all times
                'label' => trans('labels.avg_mark'),
                'type'  => 'text'
            ],
        ]);
    }
}
