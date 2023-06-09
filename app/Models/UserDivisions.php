<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserDivisions extends Pivot
{
    protected $table = 'user_divisions';
    public $timestamps = false;

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function user(){
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function division(){
        return $this->hasOne('App\Models\Division', 'id', 'division_id');
    }
}
