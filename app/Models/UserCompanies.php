<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserCompanies extends Pivot
{
    protected $table = 'user_companies';
    public $timestamps = false;

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function user(){
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function company(){
        return $this->hasOne('App\Models\Company', 'id', 'company_id');
    }
}
