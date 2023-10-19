<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'branches';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    public function reportSummary()
    {
        return '<a class="btn btn-sm btn-default" href="/admin/reports/branch-summary/'.$this->id.'"><i class="las la-chart-bar"></i></a>';
    }
    public function reportEvaluations()
    {
        return '<a class="btn btn-sm btn-info" href="/admin/reports/branch-evaluations/'.$this->id.'"><i class="las la-certificate"></i></a>';
    }
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function employees()
    {
        return $this->belongsToMany('App\Models\User', 'user_branches')->using(\App\Models\UserBranches::class);
    }

    public function activeEmployees()
    {
//        return $this->belongsToMany('App\Models\User', 'users')->where('active', 1);
        return $this->belongsToMany('App\Models\User', 'user_branches')->using(\App\Models\UserBranches::class)->where('users.active', 1);
    }

    public function getTotalEmployeesAttribute()
    {

        return $this->employees->count();
    }

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id', 'id');
    }

    public function division()
    {
        return $this->belongsTo('App\Models\Division', 'division_id', 'id');
    }

    public function region()
    {
        return $this->belongsTo('App\Models\Region', 'region_id', 'id');
    }

    public function locality()
    {
        return $this->belongsTo('App\Models\Locality', 'locality_id', 'id');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
