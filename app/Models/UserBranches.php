<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserBranches extends Pivot
{
    protected $table = 'user_branches';
    public $timestamps = false;

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function user(){
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function branch(){
        return $this->hasOne('App\Models\Branch', 'id', 'branch_id');
    }
}
