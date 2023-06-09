<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CourseProfessions extends Pivot
{
    protected $table = 'course_professions';
    public $timestamps = false;

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function course(){
        return $this->hasOne('App\Models\Course', 'id', 'course_id');
    }

    public function profesion(){
        return $this->hasOne('App\Models\Profession', 'id', 'profession_id');
    }
}
