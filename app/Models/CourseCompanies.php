<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CourseCompanies extends Pivot
{
    protected $table = 'course_companies';
    public $timestamps = false;

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function course(){
        return $this->hasOne('App\Models\Course', 'id', 'course_id');
    }

    public function company(){
        return $this->hasOne('App\Models\Company', 'id', 'company_id');
    }
}
