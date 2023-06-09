<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserAvailableCourses extends Pivot
{
    protected $table = 'user_available_courses';
    public $timestamps = false;

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function user(){
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function course(){
        return $this->hasOne('App\Models\Course', 'id', 'course_id');
    }
}
