<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CourseDivisions extends Pivot
{
    protected $table = 'course_divisions';
    public $timestamps = false;

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function course(){
        return $this->hasOne('App\Models\Course', 'id', 'course_id');
    }

    public function division(){
        return $this->hasOne('App\Models\Division', 'id', 'division_id');
    }
}
